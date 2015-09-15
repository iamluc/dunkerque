<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/v2")
 */
class VersionController extends Controller
{
    /**
     * @Route("/", methods={"GET"}, name="version_check")
     *
     * @link http://docs.docker.com/registry/spec/api/#api-version-check
     */
    public function indexAction()
    {
        return new Response('{}', Response::HTTP_OK, [
            'Content-Type' => 'application/json; charset=utf-8',
        ]);
    }
}
