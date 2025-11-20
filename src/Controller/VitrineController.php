<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\Vitrine;
use App\Form\VitrineType;
use App\Repository\VitrineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vitrines')]
class VitrineController extends AbstractController
{
    #[Route('', name: 'vitrine_index', methods: ['GET'])]
    public function index(VitrineRepository $repo): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $vitrines = $repo->findAll();
        } else {
            $user = $this->getUser(); 
            
            if (!$user) {
                throw $this->createAccessDeniedException();
            }
            
            $vitrines = $repo->findBy(['owner' => $user]);
        }
        
        return $this->render('vitrine/index.html.twig', [
            'vitrines' => $vitrines,
        ]);
    }
    
    #[Route('/{id}', name: 'vitrine_show', methods: ['GET'])]
    public function show(Vitrine $vitrine): Response
    {
        $hasAccess = false;
        
        if ($this->isGranted('ROLE_ADMIN')) {
            $hasAccess = true;
        } else {
            $member = $this->getUser(); 
            if ($member && $vitrine->getOwner() === $member) {
                $hasAccess = true;
            }
        }
        
        if (!$hasAccess) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas accéder à cette vitrine.");
        }
        
        return $this->render('vitrine/show.html.twig', [
            'vitrine' => $vitrine,
        ]);
    }
    
    #[Route('/member/{id}/new', name: 'app_member_vitrine_new', methods: ['GET', 'POST'])]
    public function memberNew(
        Member $member,
        Request $request,
        EntityManagerInterface $entityManager
        ): Response {
            $vitrine = new Vitrine();
            $vitrine->setOwner($member);
            
            $form = $this->createForm(VitrineType::class, $vitrine);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                
                /** @var UploadedFile|null $imageFile */
                $imageFile = $form->get('imageFile')->getData();
                
                if ($imageFile) {
                    $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/vitrines';
                    
                    $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeName     = transliterator_transliterate(
                        'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                        $originalName
                        );
                    $newFilename  = $safeName.'-'.uniqid().'.'.$imageFile->guessExtension();
                    
                    $imageFile->move($uploadsDir, $newFilename);
                    
                    $vitrine->setImageFilename($newFilename);
                }
                
                $entityManager->persist($vitrine);
                $entityManager->flush();
                
                return $this->redirectToRoute('app_member_show', [
                    'id' => $member->getId(),
                ], Response::HTTP_SEE_OTHER);
            }
            
            return $this->render('vitrine/member_new.html.twig', [
                'member'  => $member,
                'vitrine' => $vitrine,
                'form'    => $form,
            ]);
    }
    
    #[Route('/{id}/edit', name: 'vitrine_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Vitrine $vitrine,
        EntityManagerInterface $entityManager
        ): Response {
            if (
                !$this->isGranted('ROLE_ADMIN') &&
                (
                    !$this->getUser() ||
                    $vitrine->getOwner() !== $this->getUser()
                    )
                ) {
                    throw $this->createAccessDeniedException(
                        "Vous ne pouvez pas modifier cette vitrine."
                        );
                }
                
                $form = $this->createForm(VitrineType::class, $vitrine);
                $form->handleRequest($request);
                
                if ($form->isSubmitted() && $form->isValid()) {
                    
                    /** @var UploadedFile|null $imageFile */
                    $imageFile = $form->get('imageFile')->getData();
                    
                    if ($imageFile) {
                        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/vitrines';
                        
                        $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeName     = transliterator_transliterate(
                            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                            $originalName
                            );
                        $newFilename  = $safeName.'-'.uniqid().'.'.$imageFile->guessExtension();
                        
                        $imageFile->move($uploadsDir, $newFilename);
                        
                        $vitrine->setImageFilename($newFilename);
                    }
                    
                    $entityManager->flush();
                    
                    return $this->redirectToRoute('vitrine_show', [
                        'id' => $vitrine->getId(),
                    ], Response::HTTP_SEE_OTHER);
                }
                
                return $this->render('vitrine/edit.html.twig', [
                    'vitrine' => $vitrine,
                    'form'    => $form,
                ]);
    }
}
