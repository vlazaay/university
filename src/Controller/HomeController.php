<?php

namespace App\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function createAction(DocumentManager $dm)
    {
        $product = new User();
        $product->setName('NATAN');

        $dm->persist($product);
        $dm->flush();

        return new Response('Created user id ' . $product->getId());
    }
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('frontend/home/index.html.twig', [
        ]);
    }
}
