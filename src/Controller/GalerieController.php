<?php

namespace App\Controller;

use App\Entity\Galerie;
use App\Entity\Member;
use App\Entity\Figurine;
use App\Form\GalerieType;
use App\Repository\GalerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/galerie')]
final class GalerieController extends AbstractController
{
    #[Route('/{galerie_id}/figurine/{figurine_id}', name: 'app_galerie_figurine_show', methods: ['GET'])]
    public function figurineShow(
        #[MapEntity(id: 'galerie_id')] Galerie $galerie,
        #[MapEntity(id: 'figurine_id')] Figurine $figurine
        ): Response {
            // sécurité 1 : la figurine doit appartenir à la galerie
            if (!$galerie->getFigurines()->contains($figurine)) {
                throw $this->createNotFoundException("Figurine introuvable dans cette galerie.");
            }
            
            // sécurité 2 : même accès que pour show()
            $this->assertGalerieViewAccess($galerie);
            
            return $this->render('galerie/figurine_show.html.twig', [
                'galerie'  => $galerie,
                'figurine' => $figurine,
            ]);
    }
    
    #[Route('/member/{id}/new', name: 'app_member_galerie_new', methods: ['GET', 'POST'])]
    public function memberNew(
        Member $member,
        Request $request,
        EntityManagerInterface $entityManager
        ): Response {
            $galerie = new Galerie();
            $galerie->setCreator($member);
            
            $form = $this->createForm(GalerieType::class, $galerie);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($galerie);
                $entityManager->flush();
                
                return $this->redirectToRoute('app_member_show', [
                    'id' => $member->getId(),
                ], Response::HTTP_SEE_OTHER);
            }
            
            return $this->render('galerie/member_new.html.twig', [
                'member'  => $member,
                'galerie' => $galerie,
                'form'    => $form,
            ]);
    }
    
    #[Route(name: 'app_galerie_index', methods: ['GET'])]
    public function index(GalerieRepository $galerieRepository, EntityManagerInterface $em): Response
    {
        // Admin : voit toutes les galeries
        if ($this->isGranted('ROLE_ADMIN')) {
            $galeries = $galerieRepository->findAll();
        } else {
            $user = $this->getUser(); // Member
            
            if (!$user) {
                throw $this->createAccessDeniedException();
            }
            
            // Galeries publiques OU galeries privées du membre
            $qb = $em->createQueryBuilder();
            $qb->select('g')
            ->from(\App\Entity\Galerie::class, 'g')
            ->where('g.published = :pub')
            ->orWhere('g.creator = :user')
            ->setParameter('pub', true)
            ->setParameter('user', $user);
            
            $galeries = $qb->getQuery()->getResult();
        }
        
        return $this->render('galerie/index.html.twig', [
            'galeries' => $galeries,
        ]);
    }
    
    
    #[Route('/new', name: 'app_galerie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $galerie = new Galerie();
        $form = $this->createForm(GalerieType::class, $galerie);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($galerie);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_galerie_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('galerie/new.html.twig', [
            'galerie' => $galerie,
            'form'    => $form,
        ]);
    }
    
    #[Route('/{id}', name: 'app_galerie_show', methods: ['GET'])]
    public function show(Galerie $galerie): Response
    {
        $this->assertGalerieViewAccess($galerie);
        
        return $this->render('galerie/show.html.twig', [
            'galerie' => $galerie,
        ]);
    }
    
    #[Route('/{id}/edit', name: 'app_galerie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Galerie $galerie, EntityManagerInterface $entityManager): Response
    {
        // modification réservée au créateur ou à l’admin
        $this->assertGalerieOwnerAccess($galerie);
        
        $form = $this->createForm(GalerieType::class, $galerie);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            return $this->redirectToRoute('app_member_show', [
                'id' => $galerie->getCreator()->getId(),
            ], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('galerie/edit.html.twig', [
            'galerie' => $galerie,
            'form'    => $form,
        ]);
    }
    
    #[Route('/{id}', name: 'app_galerie_delete', methods: ['POST'])]
    public function delete(Request $request, Galerie $galerie, EntityManagerInterface $entityManager): Response
    {
        // suppression réservée au créateur ou à l’admin
        $this->assertGalerieOwnerAccess($galerie);
        
        if ($this->isCsrfTokenValid('delete'.$galerie->getId(), $request->getPayload()->getString('_token'))) {
            $member = $galerie->getCreator();
            
            $entityManager->remove($galerie);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_member_show', [
                'id' => $member?->getId(),
            ], Response::HTTP_SEE_OTHER);
        }
        
        return $this->redirectToRoute('app_member_show', [
            'id' => $galerie->getCreator()?->getId(),
        ], Response::HTTP_SEE_OTHER);
    }
    
    private function assertGalerieViewAccess(Galerie $galerie): void
    {
        // Admin : accès total
        if ($this->isGranted('ROLE_ADMIN')) {
            return;
        }
        
        // Galerie publique : accessible à tous les utilisateurs connectés
        if (method_exists($galerie, 'isPublished') && $galerie->isPublished()) {
            return;
        }
        if (method_exists($galerie, 'getPublished') && $galerie->getPublished()) {
            return;
        }
        
        // Sinon : seulement le créateur
        $user    = $this->getUser();
        $creator = $galerie->getCreator();
        
        if (!$user || !$creator || $user !== $creator) {
            throw $this->createAccessDeniedException(
                "Vous ne pouvez pas accéder à cette galerie."
                );
        }
    }
    
    private function assertGalerieOwnerAccess(Galerie $galerie): void
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return;
        }
        
        $user    = $this->getUser();
        $creator = $galerie->getCreator();
        
        if (!$user || !$creator || $user !== $creator) {
            throw $this->createAccessDeniedException(
                "Vous ne pouvez pas modifier cette galerie."
                );
        }
    }
}
