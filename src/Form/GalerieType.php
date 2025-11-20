<?php

namespace App\Form;

use App\Entity\Galerie;
use App\Entity\Figurine;
use App\Repository\FigurineRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Galerie|null $galerie */
        $galerie = $options['data'] ?? null;
        $member = $galerie?->getCreator();
        
        $builder
        ->add('description')
        ->add('published')
        ->add('figurines', EntityType::class, [
            'class' => Figurine::class,
            'choice_label' => 'nom',      // IMPORTANT
            'multiple' => true,
            'expanded' => false,
            'by_reference' => false,
            'query_builder' => function (FigurineRepository $figurineRepo) use ($member) {
            $qb = $figurineRepo->createQueryBuilder('f')
            ->leftJoin('f.vitrine', 'v')
            ->leftJoin('v.owner', 'm');
            
            if ($member) {
                $qb->andWhere('m = :member')
                ->setParameter('member', $member);
            } else {
                $qb->andWhere('1 = 0');
            }
            
            return $qb;
            },
            ])
            ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Galerie::class,
        ]);
    }
}
