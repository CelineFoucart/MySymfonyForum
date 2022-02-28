<?php

namespace App\Controller\Admin;

use App\Entity\Forum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 *  Controller used to manage forums in the backend.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class ForumCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Forum::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            SlugField::new('slug')->setTargetFieldName('title')->setUnlockConfirmationMessage(
                'Il est recommandé de laisser le slug se générer automatiquement à partir du titre'
            ),
            TextField::new('description'),
            AssociationField::new('category'),
            Field::new('orderNumber'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des forums')
            ->setPageTitle('edit', 'Modifier un forum')
            ->setPageTitle('new', 'Créer un forum')
            ->setDefaultSort(['category.title' => 'ASC', 'orderNumber' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setLabel('Ajouter un forum');
        })
        ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
