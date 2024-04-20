<?php

namespace App\Form\Type;

use App\Entity\Request\ProfilePicUploadRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfilePicUploadRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('profile_pic', FileType::class, [
                'mapped' => false,
                'constraints' => [
                    new File(
                        [
                            'mimeTypes' => [
                                'image/jpg',
                                'image/x-jpg',
                                'image/png',
                                'image/x-png',
                                'image/jpeg',
                                'image/x-jpeg'
                            ]
                        ]
                    )
                ],
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfilePicUploadRequest::class,
            'csrf_protection' => false
        ]);
    }

}