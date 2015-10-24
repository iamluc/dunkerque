<?php

namespace AppBundle\Controller\Registry;

use AppBundle\Entity\Manifest;
use AppBundle\Entity\Repository;
use AppBundle\Event\DelayedEvent;
use AppBundle\Event\ManifestEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("/v2/{name}/manifests", requirements={"name"="%regex_name%"})
 *
 * @ParamConverter("repository", options={"mapping": {"name": "name"}})
 */
class ManifestController extends Controller
{
    /**
     * @Route("/{reference}", methods={"GET"}, name="manifest_get")
     *
     * @ParamConverter("manifest", options={"repository_method": "findOneByReference", "map_method_signature": true})
     *
     * @Security("is_granted('REPO_READ', repository)")
     *
     * @link http://docs.docker.com/registry/spec/api/#get-manifest
     */
    public function getAction(Repository $repository, Manifest $manifest)
    {
        // Dispatch event
        $event = new ManifestEvent($manifest);
        $this->get('event_dispatcher')->dispatch('delayed', new DelayedEvent('kernel.terminate', 'manifest.pull', $event));

        return new Response($manifest->getContent(), Response::HTTP_OK, [
            'Content-Type' => 'application/json; charset=utf-8',
        ]);
    }

    /**
     * @Route("/{reference}", methods={"PUT"}, name="manifest_put")
     *
     * @ParamConverter("manifest", options={"repository_method": "findOneByReferenceOrCreate", "map_method_signature": true})
     *
     * @Security("is_granted('REPO_WRITE', repository)")
     *
     * @link http://docs.docker.com/registry/spec/api/#put-manifest
     */
    public function uploadAction(Request $request, Repository $repository, Manifest $manifest, $reference)
    {
        $manifest->setContent($request->getContent());

        if ($reference !== $manifest->getTag() && $reference !== $manifest->getDigest()) {
            throw new BadRequestHttpException('Provided reference does not match with tag or digest.');
        }

        // TODO: validate layers & signatures

        $this->get('doctrine')->getRepository('AppBundle:Manifest')->save($manifest);

        // Dispatch event
        $event = new ManifestEvent($manifest);
        $this->get('event_dispatcher')->dispatch('delayed', new DelayedEvent('kernel.terminate', 'manifest.push', $event));

        return new Response('', Response::HTTP_CREATED, [
            'Location' => $this->generateUrl('manifest_get', [
                'name' => $manifest->getRepository()->getName(),
                'reference' => $reference,
            ], true),
            'Docker-Content-Digest' => $manifest->getDigest(),
        ]);
    }
}
