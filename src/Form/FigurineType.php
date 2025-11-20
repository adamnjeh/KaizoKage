<?php

namespace App\Form;

use App\Entity\Figurine;
use App\Entity\Galerie;
use App\Entity\Vitrine;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


class FigurineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('vitrine', EntityType::class, [
                'class' => Vitrine::class,
                'choice_label' => 'id',
            ])
            ->add('galeries', EntityType::class, [
                'class' => Galerie::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Photo de la figurine (JPG/PNG, max 2Mo)',
                'mapped' => false,      // pas directement lié à imageFilename
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci d\'uploader une image JPG, PNG ou WebP valide',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figurine::class,
        ]);
    }
}
