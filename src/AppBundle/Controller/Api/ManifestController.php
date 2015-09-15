<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("/v2/{name}/manifests", requirements={"name"="%regex_name%"})
 */
class ManifestController extends Controller
{
    /**
     * @Route("/{reference}", methods={"GET"}, name="manifest_get")
     *
     * @link http://docs.docker.com/registry/spec/api/#get-manifest
     */
    public function getAction($name, $reference)
    {
        $manifest = $this->get('manifest_manager')->get($name, $reference);
        if (null === $manifest) {
            throw $this->createNotFoundException();
        }

        return new Response($manifest->getContent(), Response::HTTP_OK, [
            'Content-Type' => 'application/json; charset=utf-8',
        ]);
    }

    /**
     * @Route("/{reference}", methods={"PUT"}, name="manifest_put")
     *
     * @link http://docs.docker.com/registry/spec/api/#put-manifest
     */
    public function uploadAction(Request $request, $name, $reference)
    {
        $manifest = $this->get('manifest_manager')->create($request->getContent());

        if ($name !== $manifest->getName()) {
            throw new BadRequestHttpException('Provided name does not match with manifest');
        }

        $this->get('manifest_manager')->save($manifest);

        return new Response('', Response::HTTP_CREATED, [
            'Location' => $this->generateUrl('manifest_get', [
                'name' => $manifest->getName(),
                'reference' => $manifest->getDigest(),
            ], true),
            'Docker-Content-Digest' => $manifest->getDigest(),
        ]);
    }
}
