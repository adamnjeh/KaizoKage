<?php

namespace App\Controller;

use App\Entity\Vitrine;
use App\Repository\VitrineRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class VitrineController extends AbstractController
{
    /**
     * Étape proj3-b : liste des [inventaires] -> ici Vitrines
     * Route voulue par le guide : la racine "/"
     */
    #[Route('/', name: 'app_vitrine_index', methods: ['GET'])]
    public function index(VitrineRepository $vitrineRepository): Response
    {
        $vitrines = $vitrineRepository->findAll();
        
        $res  = '<h1>Liste des vitrines</h1>';
        $res .= '<ul>';
        
        foreach ($vitrines as $v) {
            $url = $this->generateUrl('app_vitrine_show', ['id' => $v->getId()]);
            // petite sécurité XSS au passage
            $desc = htmlspecialchars((string)$v->getDescription(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $res .= sprintf('<li><a href="%s">%s (id: %d)</a></li>', $url, $desc, $v->getId());
        }
        
        $res .= '</ul>';
        
        return new Response('<html><body>' . $res . '</body></html>');
    }
    
    /**
     * Étape proj3-c : consultation d’un [inventaire] -> ici Vitrine
     * Étape proj3-d : lien "Back" vers la liste
     */
    #[Route('/vitrine/{id}', name: 'app_vitrine_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $repo = $doctrine->getRepository(Vitrine::class);
        $vitrine = $repo->find($id);
        
        if (!$vitrine) {
            throw $this->createNotFoundException('The vitrine does not exist');
        }
        
        $desc = htmlspecialchars((string)$vitrine->getDescription(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        
        $res  = '<h1>Vitrine n°' . $vitrine->getId() . '</h1>';
        $res .= '<p>Description : ' . $desc . '</p>';
        $res .= '<p/><a href="' . $this->generateUrl('app_vitrine_index') . '">Back</a>';
        
        return new Response('<html><body>' . $res . '</body></html>');
    }
}
