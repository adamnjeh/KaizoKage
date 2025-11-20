<?php
namespace App\Controller;

use App\Entity\Vitrine;
use App\Entity\Member;
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
    
    #[Route('/{id}', name: 'vitrine_show', methods: ['GET'])]
    public function show(Vitrine $vitrine): Response
    {
        $hasAccess = false;
        
        if ($this->isGranted('ROLE_ADMIN')) {
            $hasAccess = true;
        } else {
            $member = $this->getUser(); // Member ou null
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
}
