<?php


namespace App\Controller\Admin;


use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCsvController extends AbstractController
{
    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param DocumentManager $dm
     * @return Response
     * @Route("/admin/csv", name="admin_csv")
     */
    public function index(
        Request  $request,
        EntityManagerInterface $em,
        DocumentManager $dm)
    {

        return $this->render('admin/reports/index.html.twig', [


        ]);
    }
}