<?php

namespace App\DataFixtures;

use App\Entity\Figurine;
use App\Entity\Vitrine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // --- Vitrine: One Piece ---
        $onePiece = new Vitrine();
        $onePiece->setDescription('Collection One Piece – personnages emblématiques en édition spéciale');
        $manager->persist($onePiece);
        
        $onePieceFigurines = [
            "Monkey D. Luffy (Gear Five)",
            "Roronoa Zoro (Enma Dual Wield)",
            "Nami (Clima-Tact Power-Up)",
        ];
        
        foreach ($onePieceFigurines as $nom) {
            $figurine = new Figurine();
            $figurine->setNom($nom);
            $figurine->setVitrine($onePiece);   // ✅ assign relation
            $manager->persist($figurine);
        }
        
        // --- Vitrine: Naruto ---
        $naruto = new Vitrine();
        $naruto->setDescription('Collection Naruto – moments iconiques de la saga');
        $manager->persist($naruto);
        
        $narutoFigurines = [
            "Uzumaki Naruto (Sage of Six Paths)",
            "Sasuke Uchiha (Rinnegan)",
            "Hatake Kakashi (Double Sharingan)",
        ];
        
        foreach ($narutoFigurines as $nom) {
            $figurine = new Figurine();
            $figurine->setNom($nom);
            $figurine->setVitrine($naruto);     // ✅ assign relation
            $manager->persist($figurine);
        }
        
        $manager->flush();
    }
}
