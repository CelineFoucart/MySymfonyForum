<?php

namespace App\Form\Admin;

use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', EntityType::class, [
                'label' => "Sélectionner les rôles de l'utilisateur :",
                'class' => Role::class,
                'choice_label' => 'name',
                'multiple' => true
            ])
            ->add('default', EntityType::class, [
                'label' => "Sélectionner le rôle par défaut de l'utilisateur :",
                'class' => Role::class,
                'choice_label' => 'name',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
