<?php
namespace App\Controller;

use App\Entity\Figurine;
use App\Repository\FigurineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/figurines')]
class FigurineController extends AbstractController
{
    #[Route('', name: 'figurine_index', methods: ['GET'])]
    public function index(FigurineRepository $repo): Response
    {
        $figurines = $repo->findAll();
        return $this->render('figurine/index.html.twig', [
            'figurines' => $figurines,
        ]);
    }
    
    #[Route('/{id}', name: 'figurine_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Figurine $figurine): Response
    {
        return $this->render('figurine/show.html.twig', [
            'figurine' => $figurine,
        ]);
    }
}
