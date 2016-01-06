<?php

namespace AppBundle\Controller\Registry;

use AppBundle\Entity\Layer;
use AppBundle\Entity\Repository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/v2/{name}/blobs", requirements={"name"="%regex_name%"})
 */
class LayerController extends Controller
{
    /**
     * @Route("/{digest}", methods={"GET"}, name="layer_get")
     *
     * @ParamConverter(name="repository", options={"mapping": {"name": "name"}})
     * @ParamConverter(name="layer", options={"mapping": {"digest": "digest"}})
     *
     * @Security("is_granted('REPO_READ', repository)")
     *
     * @link http://docs.docker.com/registry/spec/api/#existing-layers
     */
    public function getAction(Request $request, Repository $repository, Layer $layer)
    {
        // FIXME: check that the layer belongs to the repository ?

        if ($request->isMethod('HEAD')) {
            return new Response('', Response::HTTP_OK, [
                'Docker-Content-Digest' => $layer->getDigest(),
                'Content-Length' => filesize($this->get('layer_manager')->getContentPath($layer)),
            ]);
        }

        return new BinaryFileResponse($this->get('layer_manager')->getContentPath($layer), Response::HTTP_OK, [
            'Docker-Content-Digest' => $layer->getDigest(),
        ], false);
    }

    /**
     * @Route("/uploads/", methods={"POST"}, name="layer_new")
     *
     * @Security("is_granted('REPO_WRITE', name)")
     *
     * @link http://docs.docker.com/registry/spec/api/#starting-an-upload
     */
    public function newAction($name)
    {
        // FIXME: keep a link between the layer and the repository ?

        // Create the repository if it does not exist yet
        $repository = $this->get('doctrine')->getRepository('AppBundle:Repository')->findByNameOrCreate($name, $this->getUser());
        $this->get('doctrine')->getRepository('AppBundle:Repository')->save($repository);

        $layer = $this->get('layer_manager')->create();
        $this->get('layer_manager')->save($layer);

        return new Response('', Response::HTTP_ACCEPTED, [
            'Location' => $this->generateUrl('layer_upload', [
                'name' => $name,
                'uuid' => $layer->getUuid(),
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'Docker-Upload-UUID' => $layer->getUuid(),
        ]);
    }

    /**
     * @Route("/uploads/{uuid}", methods={"GET"}, name="layer_upload_status")
     *
     * @ParamConverter(name="repository", options={"mapping": {"name": "name"}})
     * @Security("is_granted('REPO_WRITE', repository)")
     *
     * @link http://docs.docker.com/registry/spec/api/#upload-progress
     */
    public function uploadStatusAction(Repository $repository)
    {
        return new Response('', 404);
    }

    /**
     * @Route("/uploads/{uuid}", methods={"PUT", "PATCH"}, name="layer_upload", requirements={"uuid"="[0-9a-z-]+"})
     *
     * @ParamConverter(name="repository", options={"mapping": {"name": "name"}})
     * @ParamConverter(name="layer", options={"mapping": {"uuid": "uuid"}})
     *
     * @Security("is_granted('REPO_WRITE', repository)")
     *
     * @link http://docs.docker.com/registry/spec/api/#uploading-the-layer
     */
    public function uploadAction(Request $request, Repository $repository, Layer $layer)
    {
        if (Layer::STATUS_COMPLETE === $layer->getStatus()) {
            throw new BadRequestHttpException(sprintf('Layer with uuid "%s" has already been uploaded', $layer->getUuid()));
        }

        $finalUpload = $request->query->has('digest');
        $size = $this->get('layer_manager')->write($layer, $request->getContent(false), true);

        if (!$finalUpload) {
            $layer->setStatus(Layer::STATUS_PARTIAL);
            $this->get('layer_manager')->save($layer);

            return new Response('', Response::HTTP_ACCEPTED, [
                'Location' => $this->generateUrl('layer_upload', [
                    'name' => $repository->getName(),
                    'uuid' => $layer->getUuid(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'Docker-Upload-UUID' => $layer->getUuid(),
                'Range' => '0-'.$size,
            ]);
        }

        $digest = $this->get('layer_manager')->computeDigest($layer);

        if ($digest !== $request->query->get('digest')) {
            throw new BadRequestHttpException(sprintf('Digest does not match with received data (computed: "%s")', $digest));
        }

        $layer->setDigest($digest);
        $layer->setStatus(Layer::STATUS_COMPLETE);
        $this->get('layer_manager')->save($layer);

        return new Response('', Response::HTTP_CREATED, [
            'Location' => $this->generateUrl('layer_get', [
                'name' => $repository->getName(),
                'digest' => $layer->getDigest(),
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'Docker-Content-Digest' => $layer->getDigest(),
        ]);
    }
}
