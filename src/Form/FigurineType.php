<?php

namespace App\Form;

use App\Entity\Figurine;
use App\Entity\Galerie;
use App\Entity\Vitrine;
use App\Entity\Member;
use App\Repository\GalerieRepository;
use App\Repository\VitrineRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class FigurineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Member|null $currentMember */
        $currentMember = $options['current_member'] ?? null;
        $isAdmin       = (bool) ($options['is_admin'] ?? false);
        
        $builder
        ->add('nom', null, [
            'label' => 'Nom de la figurine',
            'attr'  => [
                'placeholder' => 'Ex : Luffy Gear 5',
            ],
        ])
        
        // Vitrine : filtrée sur le propriétaire (sauf admin)
        ->add('vitrine', EntityType::class, [
            'class'        => Vitrine::class,
            'label'        => 'Vitrine',
            'placeholder'  => 'Choisir une vitrine…',
            'choice_label' => function (Vitrine $v) {
            $desc = $v->getDescription() ?: '(sans description)';
            return sprintf('Vitrine #%d — %s', $v->getId(), $desc);
            },
            'query_builder' => function (VitrineRepository $vr) use ($currentMember, $isAdmin) {
            $qb = $vr->createQueryBuilder('v');
            
            if ($isAdmin || !$currentMember) {
                return $qb;
            }
            
            return $qb
            ->where('v.owner = :owner')
            ->setParameter('owner', $currentMember);
            },
            ])
            
            // Galeries : filtrées sur le créateur (sauf admin)
        ->add('galeries', EntityType::class, [
            'class'        => Galerie::class,
            'label'        => 'Galeries (optionnel)',
            'required'     => false,
            'multiple'     => true,
            'expanded'     => false,
            'choice_label' => function (Galerie $g) {
            $desc = $g->getDescription() ?: '(sans description)';
            return sprintf('Galerie #%d — %s', $g->getId(), $desc);
            },
            'query_builder' => function (GalerieRepository $gr) use ($currentMember, $isAdmin) {
            $qb = $gr->createQueryBuilder('g');
            
            if ($isAdmin || !$currentMember) {
                return $qb;
            }
            
            return $qb
            ->where('g.creator = :creator')
            ->setParameter('creator', $currentMember);
            },
            'help'         => 'Sélectionnez une ou plusieurs galeries où apparaît cette figurine.',
            // IMPORTANT pour ManyToMany : appeler addGalerie/removeGalerie
            'by_reference' => false,
            ])
            
            ->add('imageFile', FileType::class, [
                'label'    => 'Photo de la figurine (JPG/PNG/WebP, max 2 Mo)',
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
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'     => Figurine::class,
            'current_member' => null,
            'is_admin'       => false,
        ]);
        
        $resolver->setAllowedTypes('current_member', ['null', Member::class]);
        $resolver->setAllowedTypes('is_admin', 'bool');
    }
}
