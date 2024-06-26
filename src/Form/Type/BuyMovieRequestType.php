<?php

namespace App\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class BuyMovieRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('movieId');

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                'csrf_protection' => false
        ]);
    }
}