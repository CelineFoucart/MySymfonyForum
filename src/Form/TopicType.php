<?php

namespace App\Form;

use App\Entity\Topic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class TopicType
 * 
 * TopicType represents a new topic form 
 * with a title field and a message field for the first post.
 * 
 * @author Céline Foucart
 */
class TopicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du sujet :',
            ])
            ->add('message', TextareaType::class, [
                'mapped' => false,
                'label' => 'Votre message :',
                'attr' => [
                    'style' => 'height:200px',
                ],
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Votre message doit faire au moins {{ limit }} caractères',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Topic::class,
        ]);
    }
}
