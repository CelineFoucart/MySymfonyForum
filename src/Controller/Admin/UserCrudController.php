<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\UserRoleType;
use App\Repository\RoleRepository;
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

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;    
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
        ->add(Crud::PAGE_DETAIL, $changeRole);
    }

    public function changeRoles(AdminContext $adminContext, Request $request, EntityManagerInterface $em): Response
    {
        $user = $adminContext->getEntity()->getInstance();
        $roles = $this->roleRepository->findAll();
        $data = [];
        foreach ($roles as $role) {
            if(in_array($role->getTitle(), $user->getRoles()))
            $data[] = $role;
        }
        
        $form = $this->createForm(UserRoleType::class, [
            'roles' => $data,
            'default' => $user->getDefaultRole()
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $default = $form->get('default')->getData();
            $newRoles = $form->get('roles')->getData();
            if(!in_array($default, $newRoles)) {
                $newRoles[] = $default;
            }
            $user->setDefaultRole($default);
            $titlesRoles = [];
            foreach ($newRoles as $role) {
                $titlesRoles[] = $role->getTitle();
            }
            $user->setRoles($titlesRoles);
            $em->flush();
        }

        return $this->render('admin/user_role.html.twig', [
            'user' => $user,
            'roles' => $roles,
            'form' => $form->createView()
        ]);
    }
}
