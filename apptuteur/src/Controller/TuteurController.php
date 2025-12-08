<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\TuteurRepository;
use App\Repository\EtudiantRepository;
use App\Repository\VisiteRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TuteurController extends AbstractController {
    public function dashboard(TuteurRepository $tuteurRepo, EtudiantRepository $etudiantRepo, VisiteRepository $visiteRepo, SessionInterface $session): Response
    {
        // Récupérer l'id du tuteur connecté depuis la session
        $tuteurId = $session->get('tuteur_id');
        if (!$tuteurId) {
            return $this->redirectToRoute('login');
        }

        // Récupérer le tuteur connecté
        $tuteur = $tuteurRepo->find($tuteurId);
        if (!$tuteur) {
            throw $this->createNotFoundException('Tuteur introuvable.');
        }

        // Récupérer la liste des étudiants associés au tuteur
        $etudiants = $etudiantRepo->findBy(['tuteur' => $tuteur]);

        // Récupérer les prochaines visites planifiées (statut "prévue" et dont la date est dans le futur)
        $now = new \DateTimeImmutable(); // Date actuelle

        $prochainesVisites = $visiteRepo->createQueryBuilder('v')
            ->where('v.tuteur = :tuteur')
            ->andWhere('v.statut = :statut')
            ->andWhere('v.date >= :now') // Date future ou égale à aujourd'hui
            ->andWhere('v.statut != :annule') // Exclure les visites annulées
            ->setParameter('tuteur', $tuteur)
            ->setParameter('statut', 'prévue')
            ->setParameter('now', $now)
            ->setParameter('annule', 'annulée')
            ->orderBy('v.date', 'ASC') // Trier par date croissante
            ->getQuery()
            ->getResult();

        return $this->render('tuteur/dashboard.html.twig', [
            'tuteur' => $tuteur,
            'etudiants' => $etudiants,
            'prochaines_visites' => $prochainesVisites,
        ]);
    }
}