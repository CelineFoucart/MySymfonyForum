<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 *  Controller used to manage roles in the backend.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class RoleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Role::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title')->hideWhenUpdating(),
            TextField::new('name'),
            TextField::new('description'),
            ColorField::new('color'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des rôles')
            ->setPageTitle('edit', 'Editer un rôle')
            ->setPageTitle('new', 'Créer un nouveau rôle')
            ->setPageTitle('detail', 'Consulter les informations d\'un rôle')
            ->showEntityActionsInlined()
            ->setHelp('index', 'Vous ne pouvez pas supprimer les rôles de ROLE_ADMIN, ROLE_USER et ROLE_MODERATOR');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setLabel('Ajouter un rôle');
        })
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ->remove(Crud::PAGE_INDEX, Action::DELETE)
        ->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) {
            return $action->displayIf(static function ($entity) {
                return !in_array($entity->getTitle(), ['ROLE_ADMIN', 'ROLE_MODERATOR', 'ROLE_USER']);
            });
        });
    }
}
