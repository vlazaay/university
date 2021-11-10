<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Entity\ProfileBro;
use App\Entity\ProfileFiz;
use App\Entity\ProfileFop;
use App\Entity\ProfileYrl;
use App\Entity\User;
use App\Entity\CategoryCharacteristics;
use App\Entity\Characteristics;
use App\Form\AdminUserType;
use App\Form\RegistrationFormTypeBro;
use App\Form\RegistrationFormTypeFiz;
use App\Form\RegistrationFormTypeFop;
use App\Form\RegistrationFormTypeYrl;
use App\Repository\CategoryRepository;
use App\Repository\ProfileBroRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\AdminCategoryType;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AdminUserController
 * @package  App\Controller
 */
class AdminUserController extends AbstractController
{

    /**
     * @param UserRepository $userRepository
     * @param UserInterface $user
     * @return Response
     * @Route("/admin/users", name="admin_users")
     */
    public function indexUsers(
        Request                $request,
        EntityManagerInterface $em,
        UserInterface          $user,
        TranslatorInterface    $translator,
        DocumentManager        $dm): Response
    {
        $userId = $user->getId();
        if (!$userId) {
            return $this->redirect('/');
        }

        $page = $request->get('page') ? $request->get('page') : 1;

        // get the user repository
        $developers = $em->getRepository(User::class);

        // build the query for the doctrine paginator
        $query = $developers->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC')
            ->getQuery();

        //set page size
        $pageSize = $this->getParameter('pageSize');

        // load doctrine Paginator
        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);

        // now get one page's items:
        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page - 1)) // set the offset
            ->setMaxResults($pageSize); // set the limit

        $list = [];
        $x = 0;
        foreach ($paginator as $pageItem) {
            $roles = $pageItem->getRoles();

            $list[$x]['scope'] = $x + 1;
            $list[$x]['id'] = $pageItem->getId();
            $list[$x]['email'] = $pageItem->getEmail();
            $list[$x]['roles'] = $roles;
            $list[$x]['isBlocked'] = $pageItem->isBlocked();

            $x++;
        }

//        if($x === 0) {
//            return $this->redirectToRoute('admin_users');
//        }

        // you can get total items
        $totalItems = count($paginator);

        // get total pages
        $pagesCount = ceil($totalItems / $pageSize);

        $pagination = null;
        for ($i = 0; $i < $pagesCount; $i++) {
            if ($page == ($i + 1)) {
                $pagination .= '<li class="paginate_button page-item active"><a class="page-link" href="/admin/users?page=' . ($i + 1) . '">' . ($i + 1) . '</a></li>';
            } else {
                $pagination .= '<li class="paginate_button page-item "><a class="page-link" href="/admin/users?page=' . ($i + 1) . '">' . ($i + 1) . '</a></li>';
            }
        }

        return $this->render('admin/users/users.html.twig', [
            'list' => $list,
            'pagination' => $pagination,
            'totalItems' => $totalItems,
            'onPageItems' => $x,
        ]);


    }
    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     * @Route("/admin/users/update/{id}", name="admin_users_update")
     */

    public function updateUser(
        Request                      $request,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository               $userRepository,
        EntityManagerInterface       $em,
        UserInterface                $user,
        $id): Response
    {

        $userId = $user->getId();
        if (!$userId) {
            return $this->redirect('/');
        }

        $userToUpdate = $userRepository->find($id);

        $roles = $userToUpdate->getRoles();


        $form = $this->createForm(AdminUserType::class, $userToUpdate);

        $form->handleRequest($request);
        $role = $userToUpdate->getRoles()[0];

        if (!$userToUpdate) {
            throw $this->createNotFoundException('No user found for update');
        }

        if ($form->isSubmitted()) {
            if($form->get('plainPassword')->getData() !== null && !empty($form->get('plainPassword')->getData())) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Updated!!!');

            return $this->redirectToRoute('admin_users');
        }


        return $this->render('admin/users/user-update.html.twig', [
            'UpdateUserForm' => $form->createView(),
            'id' => $id,
            'roles' => $roles,
        ]);
    }
    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     * @Route("/admin/users/add", name="admin_users_add")
     */
    public function addUser(
        Request                      $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface       $em,
        UserInterface                $user): Response
    {

        $userId = $user->getId();
        if (!$userId) {
            return $this->redirect('/');
        }

        $user = new User();

        $form = $this->createForm(AdminUserType::class, $user);

        $form->handleRequest($request);

        if (!$user) {
            throw $this->createNotFoundException('No user found');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Saved!!!');

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/users/user-add.html.twig', [
            'CreateUserForm' => $form->createView(),
        ]);
    }
}