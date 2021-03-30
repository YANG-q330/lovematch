<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_home")
     */
    public function home(): Response
    {
        return $this->render('main/home.html.twig');
    }

    /**
     * @Route("/tos",name="main_tos")
     */
    public function tos():Response{
        return $this->render('main/tos.html.twig');
    }
    /**
     * @Route("/legalnotice",name="main_legalnotice")
     */
    public function legalnotice():Response{
        return $this->render('main/legalnotice.html.twig');
    }
}
