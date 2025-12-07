<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;

class LogoutController extends AbstractController
{
    public function logout(SessionInterface $session): Response
    {
        $session->remove('tuteur_id');

        // Message flash
        $this->addFlash('success', 'Vous avez bien été déconnecté.');

        return $this->redirectToRoute('login');
    }
}
