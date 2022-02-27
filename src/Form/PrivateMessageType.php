<?php

namespace App\Form;

use App\Entity\PrivateMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class PrivateMessageType
 * 
 * PrivateMessageType represents a private message form
 * with a addressee field, a title field and a content field.
 * 
 * @author Céline Foucart
 */
class PrivateMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('addressee', TextType::class, [
                'mapped' => false,
                'label' => 'Destinataire du message :',
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le pseudo doit faire au moins {{ limit }} caractères',
                        'maxMessage' => 'Le pseudo doit faire au maximum {{ limit }} caractères',
                        'max' => 180,
                    ]),
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),
                ],
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre du message :',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Corps du message :',
                'attr' => [
                    'style' => 'height:200px',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrivateMessage::class,
        ]);
    }
}
