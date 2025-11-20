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
            ['adam@localhost',  '000000', ['ROLE_ADMIN'], 'one_piece'],
            ['slash@localhost', '000000', ['ROLE_USER'],  'naruto'],
            ['nami@localhost',  '000000', ['ROLE_USER'],  'dragon_ball'],
        ];
    }
    
    private function universeImages(): array
    {
        return [
            'one_piece'    => 'one_piece.jpg',
            'naruto'       => 'naruto.jpg',
            'dragon_ball'  => 'dragon_ball.jpg',
        ];
    }
    
    private function figurinesByUniverse(): array
    {
        return [
            'one_piece' => [
                'Luffy'  => 'luffy.jpg',
                'Zoro'   => 'zoro.jpg',
                'Nami'   => 'nami.jpg',
            ],
            'naruto' => [
                'Naruto' => 'naruto.jpg',
                'Sasuke' => 'sasuke.jpg',
                'Sakura' => 'sakura.jpg',
            ],
            'dragon_ball' => [
                'Goku'   => 'goku.jpg',
                'Vegeta' => 'vegeta.jpg',
                'Gohan'  => 'gohan.jpg',
            ],
        ];
    }
    
    public function load(ObjectManager $manager): void
    {
        $universes = $this->figurinesByUniverse();
        $vitrineImages = $this->universeImages();
        
        foreach ($this->membersData() as [$email, $plainPass, $roles, $universeKey]) {
            
            $member = new Member();
            $member->setEmail($email);
            $member->setRoles($roles);
            
            $password = $this->hasher->hashPassword($member, $plainPass);
            $member->setPassword($password);
            $manager->persist($member);
            
            // ----------------------------
            // CRÉATION DE LA VITRINE
            // ----------------------------
            $vitrine = new Vitrine();
            $vitrine->setDescription('Vitrine de ' . $email);
            $vitrine->setOwner($member);
            
            // Image de vitrine selon univers
            if (isset($vitrineImages[$universeKey])) {
                $vitrine->setImageFilename($vitrineImages[$universeKey]);
            }
            
            $manager->persist($vitrine);
            
            // ----------------------------
            // FIGURINES
            // ----------------------------
            $figurines = [];
            
            foreach ($universes[$universeKey] as $name => $image) {
                $figurine = new Figurine();
                $figurine->setNom($name);
                $figurine->setVitrine($vitrine);
                $figurine->setImageFilename($image);
                
                $manager->persist($figurine);
                $figurines[] = $figurine;
            }
            
            // ----------------------------
            // GALERIE PUBLIQUE
            // ----------------------------
            $galeriePublique = new Galerie();
            $galeriePublique->setDescription("Galerie publique de $email");
            $galeriePublique->setPublished(true);
            $galeriePublique->setCreator($member);
            
            $galeriePublique->addFigurine($figurines[0]);
            $galeriePublique->addFigurine($figurines[1]);
            
            $manager->persist($galeriePublique);
            
            // ----------------------------
            // GALERIE PRIVÉE
            // ----------------------------
            $galeriePrivee = new Galerie();
            $galeriePrivee->setDescription("Galerie privée de $email");
            $galeriePrivee->setPublished(false);
            $galeriePrivee->setCreator($member);
            
            $galeriePrivee->addFigurine($figurines[2]);
            
            $manager->persist($galeriePrivee);
        }
        
        $manager->flush();
    }
}
