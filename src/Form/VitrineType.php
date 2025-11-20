<?php

namespace App\Form;

use App\Entity\Vitrine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class VitrineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('description', null, [
            'label' => 'Description de la vitrine',
            'attr'  => [
                'placeholder' => 'Ex : Vitrine One Piece, Vitrine Naruto…',
            ],
        ])
        ->add('imageFile', FileType::class, [
            'label'    => 'Image de la vitrine (JPG/PNG/WebP, max 2 Mo)',
            'mapped'   => false,
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize'          => '2M',
                    'mimeTypes'        => [
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ],
                    'mimeTypesMessage' => 'Merci d\'uploader une image JPG, PNG ou WebP valide.',
                ]),
            ],
        ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vitrine::class,
        ]);
    }
}
