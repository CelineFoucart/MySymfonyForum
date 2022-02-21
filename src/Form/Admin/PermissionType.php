<?php

namespace App\Form\Admin;

use App\Entity\Category;
use App\Repository\RoleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermissionType extends AbstractType
{
    private RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
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
        $roles['Anonymes'] = 'PUBLIC_ACCESS';

        return $roles;
    }
}
