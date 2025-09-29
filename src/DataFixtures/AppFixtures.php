<?php

namespace App\DataFixtures;

use App\Entity\Vitrine;
use App\Entity\Figurine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    // références internes pour relier vite Figurines -> Vitrines
    private const VITRINE_LUFFY = 'vitrine-luffy';
    private const VITRINE_NARUTO = 'vitrine-naruto';
    
    public function load(ObjectManager $manager): void
    {
        // -- 1) Vitrines
        $vitrines = [
            [self::VITRINE_LUFFY,  "Vitrine One Piece (Mugiwara)"],
            [self::VITRINE_NARUTO, "Vitrine Naruto (Konoha)"],
        ];
        
        foreach ($vitrines as [$ref, $desc]) {
            $v = new Vitrine();
            $v->setDescription($desc);
            $manager->persist($v);
            $manager->flush(); // pour avoir un id
            $this->addReference($ref, $v);
        }
        
        // -- 2) Figurines
        $figurines = [
            [self::VITRINE_LUFFY,  'Monkey D. Luffy (Gear 5)'],
            [self::VITRINE_LUFFY,  'Roronoa Zoro (Enma)'],
            [self::VITRINE_LUFFY,  'Nami (Clima-Tact)'],
            
            [self::VITRINE_NARUTO, 'Uzumaki Naruto (Sage Mode)'],
            [self::VITRINE_NARUTO, 'Uchiha Sasuke (Rinnegan)'],
        ];
        
        foreach ($figurines as [$ref, $nom]) {
            /** @var Vitrine $vitrine */
            $vitrine = $this->getReference($ref, Vitrine::class);
            
            $f = new Figurine();
            $f->setNom($nom);
            $f->setVitrine($vitrine); // côté owning
            $manager->persist($f);
        }
        
        $manager->flush();
    }
}
