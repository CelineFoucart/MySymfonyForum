<?php

namespace App\Form\Admin;

use App\Entity\Category;
use App\Repository\RoleRepository;
use App\Service\PermissionHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PermissionType
 * 
 * PermissionType represents an permissions form 
 * with a permissions field to change the permissions of a category.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class PermissionType extends AbstractType
{
    private RoleRepository $roleRepository;

    private PermissionHelper $permissionHelper;

    public function __construct(RoleRepository $roleRepository, PermissionHelper $permissionHelper)
    {
        $this->roleRepository = $roleRepository;
        $this->permissionHelper = $permissionHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('permissions', ChoiceType::class, [
                'label' => 'Sélectionner les rôles pouvant voir cette catégorie :',
                'choices' => $this->getRoles(),
                'multiple' => true,
                'required' => false,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }

    private function getRoles(): array
    {
        $data = $this->roleRepository->findAll();
        $roles = [];
        foreach ($data as $role) {
            $roles[$role->getName()] = $role->getTitle();
        }
        $roles['Anonymes'] = $this->permissionHelper::PUBLIC_ACCESS;

        return $roles;
    }
}
