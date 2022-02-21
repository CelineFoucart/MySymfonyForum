<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\UserRoleType;
use App\Repository\RoleRepository;
use App\Service\RoleUserService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserCrudController extends AbstractCrudController
{
    private RoleRepository $roleRepository;

    private RoleUserService $roleUserService;

    public function __construct(RoleRepository $roleRepository, RoleUserService $roleUserService)
    {
        $this->roleRepository = $roleRepository;   
        $this->roleUserService = $roleUserService;  
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('username'),
            TextField::new('email'),
            Field::new('created')->hideOnForm(),
            Field::new('password')->hideOnIndex()->hideOnDetail()->setFormType(PasswordType::class),
            Field::new('localisation')->hideOnIndex(),
            Field::new('rank')->hideOnIndex(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des membres')
            ->setPageTitle('edit', 'Editer les informations d\'une membre')
            ->setPageTitle('new', 'Créer un compte utilisateur')
            ->setPageTitle('detail', 'Consulter le profil d\'un utilisateur')
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        $changeRole = Action::new('changeRoles', 'Rôles')->linkToCrudAction('changeRoles');
        return $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setLabel('Ajouter un utilisateur');
        })
        ->add(Crud::PAGE_INDEX, $changeRole)
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ->add(Crud::PAGE_EDIT, $changeRole)
        ->add(Crud::PAGE_DETAIL, $changeRole)
        ->remove(Crud::PAGE_INDEX, Action::DELETE)
        ->update(Crud::PAGE_DETAIL, Action::DELETE, function(Action $action) {
            return $action->displayIf(static function(User $entity) {
                return !$entity->hasRole('ROLE_ADMIN');
            });
        });
    }

    public function changeRoles(AdminContext $adminContext, Request $request): Response
    {
        $user = $adminContext->getEntity()->getInstance();
        $roles = $this->roleRepository->findAll();
        
        $form = $this->createForm(UserRoleType::class, [
            'roles' => $this->roleUserService->getUserRoles($user, $roles),
            'default' => $user->getDefaultRole()
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $this->roleUserService->persistRoles(
                $form->get('default')->getData(),
                $form->get('roles')->getData(),
                $user
            );
        }

        return $this->render('admin/user_role.html.twig', [
            'user' => $user,
            'roles' => $roles,
            'form' => $form->createView()
        ]);
    }
}
