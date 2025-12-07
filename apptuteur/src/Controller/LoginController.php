<?php

namespace App\Controller;

use App\Repository\TuteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;

class LoginController extends AbstractController {
    public function login(Environment $twig, Request $request, SessionInterface $session, TuteurRepository $tuteurRepository) {
        
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            //$password = $request->request->get('password');

            // Recherche du tuteur
            $tuteur = $tuteurRepository->findOneBy(['email' => $email]);

            /* if ($tuteur && $tuteur->getPassword() === $password) { 
                // Il faudrait un hash !
                
                // Stockage en session
                $session->set('tuteur_id', $tuteur->getId());

                return $this->redirectToRoute('dashboard');
            } else {
                $error = "Email ou mot de passe incorrect";
            } */

            if($tuteur) {
                $session->set('tuteur_id', $tuteur->getId());
                return $this->redirectToRoute('dashboard');
            } else {
                //$error = "Email ou mot de passe incorrect";
                $this->addFlash('danger', 'Email ou mot de passe incorrect');
            }
        }

        return new Response($twig->render('login/login.html.twig'));
    }
}