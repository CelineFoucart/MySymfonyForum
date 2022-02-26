<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\Admin\PermissionType;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryCrudController extends AbstractCrudController
{
    private RoleRepository $roleRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(RoleRepository $roleRepository, EntityManagerInterface $entityManager)
    {
        $this->roleRepository = $roleRepository;
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            SlugField::new('slug')->setTargetFieldName('title')->setUnlockConfirmationMessage(
                'Il est recommandé de laisser le slug se générer automatiquement à partir du titre'
            ),
            TextField::new('description')->hideOnIndex(),
            Field::new('orderNumber'),
            AssociationField::new('forums')->hideOnForm(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des catégories')
            ->setPageTitle('edit', 'Modifier une catégorie')
            ->setPageTitle('new', 'Créer une catégorie')
            ->showEntityActionsInlined()
            ->setDefaultSort(['orderNumber' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $permissions = Action::new('setPermissions', 'Permissions')->linkToCrudAction('setPermissions');

        return $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setLabel('Ajouter une catégorie');
        })
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ->add(Crud::PAGE_INDEX, $permissions)
        ->add(Crud::PAGE_DETAIL, $permissions);
    }

    public function setPermissions(AdminContext $adminContext, Request $request): Response
    {
        $category = $adminContext->getEntity()->getInstance();
        $roles = $this->roleRepository->findAll();
        $form = $this->createForm(PermissionType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $permissions = $form->get('permissions')->getData();
            $category->setPermissions([]);
            foreach ($permissions as $permission) {
                $category->setPermission($permission);
            }
            $this->entityManager->flush();
        }

        return $this->render('admin/permissions.html.twig', [
            'category' => $category,
            'roles' => $roles,
            'form' => $form->createView(),
        ]);
    }
}
