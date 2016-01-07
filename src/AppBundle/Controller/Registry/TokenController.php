<?php

namespace AppBundle\Controller\Registry;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\User;

class TokenController extends Controller
{
    /**
     * @Route("/token", methods={"GET"}, name="registry_token")
     *
     * @link https://docs.docker.com/registry/spec/auth/token/
     * @link https://docs.docker.com/registry/spec/auth/jwt/
     */
    public function tokenAction()
    {
        // TODO: check scope/repository

        $user = $this->getUser();
        if (null === $user) {
            $user = new User('Anon.', null);
        }

        $token = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);

        return new JsonResponse([
            'token' => $token,
        ]);
    }
}
