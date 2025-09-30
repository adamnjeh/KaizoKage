<?php

namespace App\Controller;

use App\Entity\Figurine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class FigurineController extends AbstractController
{
    // Consultation d’une figurine
    #[Route('/figurine/{id}', name: 'app_figurine_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Figurine $figurine): Response
    {
        // ParamConverter charge l'entité; 404 auto si non trouvée
        return $this->render('figurine/show.html.twig', [
            'figurine' => $figurine,
        ]);
    }
}
