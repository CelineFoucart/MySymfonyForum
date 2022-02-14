<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('birthday', BirthdayType::class, [
                'label' => 'Date de naissance',
                'required' => false,
                'format' => \IntlDateFormatter::LONG,
                'choice_translation_domain' => True
            ])
            ->add('rank', TextType::class, [
                'label' => "Rang",
                "required" => false
            ])
            ->add('localisation', TextType::class, [
                'label' => "Localisation",
                "required" => false
            ])
            ->add('avatar', FileType::class, [
                'label' => "Avatar",
                'help' => "Dimensions maximales : 150 pixels de large et 150 pixels de haut",
                'mapped' => false,
                "required" => false,
                'constraints' => [
                    new Image([
                        'maxWidth' => 150,
                        'maxHeight' => 150,
                        'maxWidthMessage' => "Cette image est trop large",
                        'maxHeightMessage' => "Cette image est trop haute",
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
