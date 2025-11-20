<?php

namespace App\Controller;

use App\Entity\Figurine;
use App\Form\FigurineType;
use App\Repository\FigurineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;


#[Route('/figurine')]
final class FigurineController extends AbstractController
{
    #[Route(name: 'app_figurine_index', methods: ['GET'])]
    public function index(FigurineRepository $figurineRepository, EntityManagerInterface $em): Response
    {
        // Admin : voit toutes les figurines
        if ($this->isGranted('ROLE_ADMIN')) {
            $figurines = $figurineRepository->findAll();
        } else {
            $user = $this->getUser(); // Member
            
            if (!$user) {
                throw $this->createAccessDeniedException();
            }
            
            // Figurines dont la vitrine appartient au membre connecté
            $qb = $em->createQueryBuilder();
            $qb->select('f')
            ->from(\App\Entity\Figurine::class, 'f')
            ->join('f.vitrine', 'v')
            ->where('v.owner = :owner')
            ->setParameter('owner', $user);
            
            $figurines = $qb->getQuery()->getResult();
        }
        
        return $this->render('figurine/index.html.twig', [
            'figurines' => $figurines,
        ]);
    }
    

    #[Route('/new', name: 'app_figurine_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $figurine = new Figurine();
        
        $user    = $this->getUser();               // Member ou null
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        
        $form = $this->createForm(FigurineType::class, $figurine, [
            'current_member' => $user,
            'is_admin'       => $isAdmin,
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Sécurité serveur : un membre ne peut utiliser que SES vitrines/galeries
            if (!$isAdmin) {
                if (!$user) {
                    throw $this->createAccessDeniedException();
                }
                
                $vitrine = $figurine->getVitrine();
                if (!$vitrine || $vitrine->getOwner() !== $user) {
                    throw $this->createAccessDeniedException(
                        "Vous ne pouvez pas utiliser une vitrine qui ne vous appartient pas."
                        );
                }
                
                foreach ($figurine->getGaleries() as $galerie) {
                    if ($galerie->getCreator() !== $user) {
                        throw $this->createAccessDeniedException(
                            "Vous ne pouvez pas utiliser une galerie qui ne vous appartient pas."
                            );
                    }
                }
            }
            
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/figurines';
                
                $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName     = transliterator_transliterate(
                    'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                    $originalName
                    );
                $newFilename  = $safeName.'-'.uniqid().'.'.$imageFile->guessExtension();
                
                $imageFile->move($uploadsDir, $newFilename);
                
                $figurine->setImageFilename($newFilename);
            }
            
            $entityManager->persist($figurine);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_figurine_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('figurine/new.html.twig', [
            'figurine' => $figurine,
            'form'     => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_figurine_show', methods: ['GET'])]
    public function show(Figurine $figurine): Response
    {
        $this->assertFigurineAccess($figurine);
        
        $hasAccess = false;
        
        if ($this->isGranted('ROLE_ADMIN')) {
            $hasAccess = true;
        } else {
            $member = $this->getUser();
            $owner  = $figurine->getVitrine()?->getOwner();
            
            if ($member && $owner && $member === $owner) {
                $hasAccess = true;
            }
        }
        
        if (!$hasAccess) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas accéder à cette figurine.");
        }
        
        return $this->render('figurine/show.html.twig', [
            'figurine' => $figurine,
        ]);
    }
    

    #[Route('/{id}/edit', name: 'app_figurine_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Figurine $figurine, EntityManagerInterface $entityManager): Response
    {
        // contrôle d’accès sur la figurine actuelle (avant modification)
        $this->assertFigurineAccess($figurine);
        
        $user    = $this->getUser();
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        
        $form = $this->createForm(FigurineType::class, $figurine, [
            'current_member' => $user,
            'is_admin'       => $isAdmin,
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Re-sécurité côté serveur : empêcher de déplacer la figurine vers une vitrine/galerie d’un autre membre
            if (!$isAdmin) {
                if (!$user) {
                    throw $this->createAccessDeniedException();
                }
                
                $vitrine = $figurine->getVitrine();
                if (!$vitrine || $vitrine->getOwner() !== $user) {
                    throw $this->createAccessDeniedException(
                        "Vous ne pouvez pas déplacer cette figurine dans une vitrine qui ne vous appartient pas."
                        );
                }
                
                foreach ($figurine->getGaleries() as $galerie) {
                    if ($galerie->getCreator() !== $user) {
                        throw $this->createAccessDeniedException(
                            "Vous ne pouvez pas associer cette figurine à une galerie qui ne vous appartient pas."
                            );
                    }
                }
            }
            
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/figurines';
                
                $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName     = transliterator_transliterate(
                    'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                    $originalName
                    );
                $newFilename  = $safeName.'-'.uniqid().'.'.$imageFile->guessExtension();
                
                $imageFile->move($uploadsDir, $newFilename);
                
                $figurine->setImageFilename($newFilename);
            }
            
            $entityManager->flush();
            
            return $this->redirectToRoute('app_figurine_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('figurine/edit.html.twig', [
            'figurine' => $figurine,
            'form'     => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_figurine_delete', methods: ['POST'])]
    public function delete(Request $request, Figurine $figurine, EntityManagerInterface $entityManager): Response
    {
        $this->assertFigurineAccess($figurine);
        
        if ($this->isCsrfTokenValid('delete'.$figurine->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($figurine);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_figurine_index', [], Response::HTTP_SEE_OTHER);
    }
    
    private function assertFigurineAccess(Figurine $figurine): void
    {
        // Admin : accès total
        if ($this->isGranted('ROLE_ADMIN')) {
            return;
        }
        
        $user  = $this->getUser();                     // Member ou null
        $owner = $figurine->getVitrine()?->getOwner(); // Member ou null
        
        if (!$user || !$owner || $user !== $owner) {
            throw $this->createAccessDeniedException(
                "Vous ne pouvez pas accéder à cette figurine."
                );
        }
    }
    
}
