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

        $scheme = $request->getScheme().':';
        $tokenEndpoint = $scheme.$this->generateUrl('registry_token', [], UrlGeneratorInterface::NETWORK_PATH);
        $serviceEndpoint = $scheme.$this->generateUrl('index', [], UrlGeneratorInterface::NETWORK_PATH);

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
