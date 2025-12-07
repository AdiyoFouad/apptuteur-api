<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Repository\EtudiantRepository;
use App\Repository\TuteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class EtudiantController extends AbstractController
{
    public function index(Environment $twig, SessionInterface $session, EtudiantRepository $repo, TuteurRepository $tuteurRepo): Response {

        $tuteurId = $session->get('tuteur_id');
        if (!$tuteurId) {
            $this->addFlash('warning', 'Vous devez d\'abord vous connecter.');
            return $this->redirectToRoute('login');
        }

        $tuteur = $tuteurRepo->find($tuteurId);

        $etudiants = $repo->findBy(['tuteur' => $tuteur]);

        //$etudiants = $repo->findAll();

        return new Response($twig->render('etudiants/index.html.twig', [
                'etudiants' => $etudiants
            ]));
    }

    public function new(
        Environment $twig,
        Request $request,
        SessionInterface $session, 
        TuteurRepository $tuteurRepo, 
        EntityManagerInterface $em ) {

        $tuteurId = $session->get('tuteur_id');
        if (!$tuteurId) {
            return $this->redirectToRoute('login');
        }

        $tuteur = $tuteurRepo->find($tuteurId);

        $etudiant = new Etudiant();
        
        $form = $this->createForm(EtudiantType::class, $etudiant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Lier automatiquement au tuteur connecté
            $etudiant->setTuteur($tuteur);

            $em->persist($etudiant);
            $em->flush();

            return $this->redirectToRoute('etudiants_list');
        }

        return $this->render('etudiants/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function edit(
        int $id,
        Request $request,
        SessionInterface $session,
        EtudiantRepository $repo,
        TuteurRepository $tuteurRepo,
        EntityManagerInterface $em
    ) {

        $tuteurId = $session->get('tuteur_id');
        if (!$tuteurId) {
            return $this->redirectToRoute('login');
        }

        $tuteur = $tuteurRepo->find($tuteurId);
        $etudiant = $repo->find($id);

        if (!$etudiant || $etudiant->getTuteur()->getId() !== $tuteurId) {
            throw $this->createAccessDeniedException("Cet étudiant ne vous appartient pas.");
        }

        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('etudiants_list');
        }

        return $this->render('etudiants/edit.html.twig', [
            'form' => $form->createView(),
            'etudiant' => $etudiant
        ]);
    }

    public function delete(
        int $id,
        SessionInterface $session,
        EtudiantRepository $repo,
        EntityManagerInterface $em
    ) {
        
        $etudiant = $repo->find($id);

        $tuteurId = $session->get('tuteur_id');
        if (!$tuteurId) {
            return $this->redirectToRoute('login');
        }

        if (!$etudiant || $etudiant->getTuteur()->getId() !== $tuteurId) {
            throw $this->createAccessDeniedException("Cet étudiant ne vous appartient pas.");
        }

        $em->remove($etudiant);
        $em->flush();
        return $this->redirectToRoute('etudiants_list');
    }
}
