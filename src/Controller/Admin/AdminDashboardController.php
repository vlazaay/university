<?php

namespace App\Controller\Admin;


use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;

/**
 * Class AdminDashboardController
 * @package  App\Controller
 */

class AdminDashboardController extends AbstractController
{

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param DocumentManager $dm
     * @return Response
     * @Route("/admin", name="admin_dashboard")
     */
    public function indexDashboard(
        Request  $request,
        EntityManagerInterface $em,
        DocumentManager $dm)
    {

        return $this->render('admin/dashboard.html.twig', [


        ]);
    }
}