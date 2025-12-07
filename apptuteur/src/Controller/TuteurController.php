<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;

class TuteurController extends AbstractController {
    public function dashboard(Environment $twig) {
        return new Response($twig->render('tuteur/dashboard.html.twig'));
    }
}