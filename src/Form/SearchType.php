<?php

namespace App\Form;

use App\Entity\Search;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class SearchType
 * 
 * SearchType represents an topic and post search form in the forum
 * with a type field, keywords field and user field.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'posts' => 'post',
                    'topics' => 'topic',
                ],
            ])
            ->add('keywords', TextType::class, [
                'required' => false,
                'label' => 'Recherche par mot clé',
                'attr' => [
                    'placeholder' => 'Recherche de plus de 3 caractères',
                ],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre recherche doit faire au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre recherche doit faire au maximum {{ limit }} caractères',
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'multiple' => false,
                'required' => false,
                'label' => 'Auteur',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
        ]);
    }
}
