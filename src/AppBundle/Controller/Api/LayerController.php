<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Layer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/v2/{name}/blobs", requirements={"name"="%regex_name%"})
 */
class LayerController extends Controller
{
    /**
     * @Route("/{digest}", methods={"GET"}, name="layer_get")
     *
     * @ParamConverter(name="layer", options={"mapping": {"digest": "digest"}})
     *
     * @link http://docs.docker.com/registry/spec/api/#existing-layers
     */
    public function getAction(Request $request, Layer $layer)
    {
        if ($request->isMethod('HEAD')) {
            return new Response('', Response::HTTP_OK, [
                'Docker-Content-Digest' => $layer->getDigest(),
            ]);
        }

        return new BinaryFileResponse($this->get('layer_manager')->getContentPath($layer), Response::HTTP_OK, [
            'Docker-Content-Digest' => $layer->getDigest(),
        ], false);
    }

    /**
     * @Route("/uploads/", methods={"POST"}, name="layer_new")
     *
     * @link http://docs.docker.com/registry/spec/api/#starting-an-upload
     */
    public function newAction($name)
    {
        $layer = $this->get('layer_manager')->create($name);
        $this->get('layer_manager')->save($layer);

        return new Response('', Response::HTTP_ACCEPTED, [
            'Location' => $this->generateUrl('layer_upload', [
                'name' => $layer->getName(),
                'uuid' => $layer->getUuid(),
            ], true),
            'Docker-Upload-UUID' => $layer->getUuid(),
        ]);
    }

    /**
     * @Route("/uploads/{uuid}", methods={"PUT"}, name="layer_upload", requirements={"uuid"="[0-9a-z-]+"})
     *
     * @ParamConverter(name="layer", options={"mapping": {"uuid": "uuid"}})
     *
     * @link http://docs.docker.com/registry/spec/api/#uploading-the-layer
     */
    public function uploadAction(Request $request, Layer $layer)
    {
        $finalUpload = $request->query->has('digest');

        // TODO: manage chunked uploads
        $this->get('layer_manager')->write($layer, $request->getContent(true));

        if (!$finalUpload) {
            return new Response('', Response::HTTP_ACCEPTED, [
                'Location' => $this->generateUrl('layer_upload', [
                    'name' => $layer->getName(),
                    'uuid' => $layer->getUuid(),
                ], true),
                'Docker-Upload-UUID' => $layer->getUuid(),
            ]);
        }

        // TODO: validate digest
        $layer->setDigest($request->query->get('digest'));
        $this->get('layer_manager')->save($layer);

        return new Response('', Response::HTTP_CREATED, [
            'Location' => $this->generateUrl('layer_get', [
                'name' => $layer->getName(),
                'digest' => $layer->getDigest(),
            ], true),
            'Docker-Content-Digest' => $layer->getDigest(),
        ]);
    }

    /**
     * @Route("/uploads/{uuid}", methods={"GET"}, name="layer_upload_status")
     *
     * @link http://docs.docker.com/registry/spec/api/#upload-progress
     */
    public function uploadStatusAction()
    {
        throw new \Exception('Not implemented');
    }
}
