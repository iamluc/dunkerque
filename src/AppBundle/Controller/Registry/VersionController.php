<?php

namespace AppBundle\Controller\Registry;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/v2")
 */
class VersionController extends Controller
{
    /**
     * @Route("/", methods={"GET"}, name="version_check")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @link http://docs.docker.com/registry/spec/api/#api-version-check
     */
    public function indexAction(Request $request)
    {
        return new Response('{}', Response::HTTP_OK, [
            'Content-Type' => 'application/json; charset=utf-8',
        ]);
    }
}
