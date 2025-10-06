<?php
namespace App\Controller;

use App\Entity\Vitrine;
use App\Repository\VitrineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vitrines')]
class VitrineController extends AbstractController
{
    #[Route('', name: 'vitrine_index', methods: ['GET'])]
    public function index(VitrineRepository $repo): Response
    {
        $vitrines = $repo->findAll();
        return $this->render('vitrine/index.html.twig', [
            'vitrines' => $vitrines,
        ]);
    }
    
    #[Route('/{id}', name: 'vitrine_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Vitrine $vitrine): Response
    {
        return $this->render('vitrine/show.html.twig', [
            'vitrine' => $vitrine,
        ]);
    }
}
