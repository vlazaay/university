<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubjectController extends AbstractController
{
    /**
     * @Route("/subject", name="subject")
     */
    public function index(): Response
    {
        return $this->render('frontend/subject/index.html.twig', [
            'controller_name' => 'SubjectController',
        ]);
    }
}
