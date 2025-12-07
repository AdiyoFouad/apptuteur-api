<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\Visite;
use App\Form\VisiteType;
use App\Repository\VisiteRepository;
use App\Repository\EtudiantRepository;
use App\Repository\TuteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;


use Dompdf\Dompdf;
use Dompdf\Options;

class VisiteController extends AbstractController
{
    
    public function index(int $id, EtudiantRepository $etudiantRepo, VisiteRepository $visiteRepo, SessionInterface $session): Response
    {
        $tuteurId = $session->get('tuteur_id');
        if (!$tuteurId) {
            return $this->redirectToRoute('login');
        }

        $etudiant = $etudiantRepo->find($id);
        if (!$etudiant || $etudiant->getTuteur()->getId() !== $tuteurId) {
            throw $this->createAccessDeniedException("Cet étudiant ne vous appartient pas.");
        }

        $visites = $visiteRepo->findBy(['etudiant' => $etudiant]);

        return $this->render('visites/index.html.twig', [
            'etudiant' => $etudiant,
            'visites' => $visites
        ]);
    }

    public function new(
        int $id,
        Request $request,
        SessionInterface $session,
        EtudiantRepository $etudiantRepo,
        TuteurRepository $tuteurRepo,
        EntityManagerInterface $em
    ): Response {

        $tuteurId = $session->get('tuteur_id');
        if (!$tuteurId) {
            return $this->redirectToRoute('login');
        }

        $etudiant = $etudiantRepo->find($id);
        if (!$etudiant || $etudiant->getTuteur()->getId() !== $tuteurId) {
            throw $this->createAccessDeniedException("Cet étudiant ne vous appartient pas.");
        }

        $visite = new Visite();
        $form = $this->createForm(VisiteType::class, $visite);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $visite->setEtudiant($etudiant);
            $visite->setTuteur($tuteurRepo->find($tuteurId));
            $visite->setStatut('prévue');

            $em->persist($visite);
            $em->flush();

            return $this->redirectToRoute('visites_etudiant', ['id' => $etudiant->getId()]);
        }

        return $this->render('visites/new.html.twig', [
            'form' => $form->createView(),
            'etudiant' => $etudiant
        ]);
    }

    public function edit(
        int $id,
        Request $request,
        SessionInterface $session,
        VisiteRepository $visiteRepo,
        EntityManagerInterface $em
    ): Response {

        $tuteurId = $session->get('tuteur_id');
        if (!$tuteurId) {
            return $this->redirectToRoute('login');
        }

        $visite = $visiteRepo->find($id);
        if (!$visite || $visite->getTuteur()->getId() !== $tuteurId) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas modifier cette visite.");
        }

        $form = $this->createForm(VisiteType::class, $visite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('visites_etudiant', ['id' => $visite->getEtudiant()->getId()]);
        }

        return $this->render('visites/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function compteRendu(
        int $id,
        Request $request,
        VisiteRepository $visiteRepo,
        EntityManagerInterface $em
    ): Response {

        $visite = $visiteRepo->find($id);
        if (!$visite) {
            throw $this->createNotFoundException("Visite introuvable.");
        }

        $form = $this->createFormBuilder($visite)
            ->add('compteRendu', TextareaType::class, [
                'label' => 'Compte-rendu', 
                'attr' => ['class' => 'form-control']
                ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('visites_etudiant', ['id' => $visite->getEtudiant()->getId()]);
        }

        return $this->render('visites/compte_rendu.html.twig', [
            'form' => $form->createView(),
            'visite' => $visite,
        ]);
    }

    public function exportPdf(Visite $visite): Response
    {
        // Vérification que la visite existe
        if (!$visite) {
            throw $this->createNotFoundException("Visite introuvable.");
        }

        // Configuration de DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        // Générer le contenu HTML pour le PDF
        $html = $this->renderView('visites/pdf.html.twig', [
            'visite' => $visite
        ]);

        // Charger le HTML dans DomPDF
        $dompdf->loadHtml($html);
        $dompdf->render();

        // Générer le PDF
        $output = $dompdf->output();

        // Retourner le PDF en réponse
        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="compte-rendu.pdf"',
        ]);
    }

}
