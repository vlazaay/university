<?php
namespace App\Controller\Api;


use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * Class ApiUserController
 * @package App\Controller\Api
 * @Route("/api", name="profile_api")
 */
class ApiUserController extends AbstractController
{
    /**
     * @return JsonResponse
     * @Route("/profile/active", name="api_profile_active", methods={"POST"})
     */
    public function activeProfile(
        TokenStorageInterface $tokenStorage,
        UserRepository $user,
        EntityManagerInterface $em,
        Request  $request
    )
    {

        try {

            $request = $this->transformJsonBody($request);

            $thisUser = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;

            if (!$thisUser) {
                throw new \Exception('Войдите в систему!');
            }

            if (
                !$request ||
                !$request->get('id') ||
                !$request->get('active')
            ) {
                throw new \Exception('Нет данных active');
            }

            $active = $request->get('active');
            $id = $request->get('id');
            $status = $active === 'true' ? 1 : 0;
            $findUser = $user->find($id);
            $findUser->setBlocked($status);
            $em->persist($findUser);
            $em->flush();

            return $this->response($status);

        } catch (\Exception $e) {
            $data = [
                'status' => 422,
                '$e' => $e->getMessage(),
                'errors' => "Data User no valid",
            ];
            return $this->response($data, 422);
        }
    }

    protected function response($data, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }
    protected function transformJsonBody(\Symfony\Component\HttpFoundation\Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}