<?php

namespace App\Form\Moderator;

use App\Entity\Forum;
use App\Entity\Topic;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopicMoveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('forum', EntityType::class, [
                'class' => Forum::class,
                'choice_label' => 'title',
                'multiple' => false,
                'label' => 'Nouveau forum :',
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
