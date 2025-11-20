<?php

namespace App\DataFixtures;

use App\Entity\Member;
use App\Entity\Vitrine;
use App\Entity\Figurine;
use App\Entity\Galerie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    
    private function membersData(): array
    {
        return [
            ['adam@localhost', '000000'],
            ['slash@localhost',   '123456'],
            ['nami@localhost',    '123456'],
        ];
    }
    
    public function load(ObjectManager $manager): void
    {
        foreach ($this->membersData() as [$email, $plainPass]) {
            // 1) Membre
            $member = new Member();
            $password = $this->hasher->hashPassword($member, $plainPass);
            $member->setEmail($email);
            $member->setPassword($password);
            $manager->persist($member);
            
            $vitrine = new Vitrine();
            $vitrine->setDescription('Vitrine de ' . $email);
            $vitrine->setOwner($member);
            $manager->persist($vitrine);
            
            $figurineNames = [
                'Luffy',
                'Zoro',
                'Nami',
                'Sanji',
                'Usopp',
            ];
            
            $figurines = [];
            foreach ($figurineNames as $name) {
                $figurine = new Figurine();
                $figurine->setNom($name);
                $figurine->setVitrine($vitrine);
                $manager->persist($figurine);
                $figurines[] = $figurine;
            }
            
            $galeriePublique = new Galerie();
            $galeriePublique->setDescription('Galerie publique de ' . $email);
            $galeriePublique->setPublished(true);
            $galeriePublique->setCreator($member);
            
            foreach (array_slice($figurines, 0, 3) as $figurine) {
                $galeriePublique->addFigurine($figurine);
            }
            
            $manager->persist($galeriePublique);
            
            $galeriePrivee = new Galerie();
            $galeriePrivee->setDescription('Galerie privée de ' . $email);
            $galeriePrivee->setPublished(false);
            $galeriePrivee->setCreator($member);
            
            foreach (array_slice($figurines, 2, 3) as $figurine) {
                $galeriePrivee->addFigurine($figurine);
            }
            
            $manager->persist($galeriePrivee);
        }
        
        $manager->flush();
    }
}
