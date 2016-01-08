<?php

namespace AppBundle\Controller\Registry;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
    public function indexAction(Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new Response('{}', Response::HTTP_OK, [
                'Content-Type' => 'application/json; charset=utf-8',
            ]);
        }

        $tokenEndpoint = $this->generateUrl('registry_token', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $serviceEndpoint = $this->generateUrl('index', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse([
           'errors' => [
               [
                   'code' => 'UNAUTHORIZED',
                   'details' => null,
                   'message' => 'access to the requested resource is not authorized',
               ],
           ],
        ], Response::HTTP_UNAUTHORIZED, [
            'WWW-Authenticate' => sprintf('Bearer realm="%s",service="%s"', $tokenEndpoint, $serviceEndpoint),
        ]);
    }
}
