<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BuildConfig;
use AppBundle\Entity\Repository;
use AppBundle\Entity\Webhook;
use Datatheke\Bundle\PagerBundle\Pager\Filter;
use Datatheke\Bundle\PagerBundle\Pager\PagerView;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Swarrot\Broker\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RepositoryController extends Controller
{
    /**
     * @Route("/r/{name}", requirements={"name"="%regex_name%"}, methods={"GET", "POST"}, name="repository")
     *
     * @ParamConverter("repository", options={"mapping": {"name": "name"}})
     *
     * @Security("is_granted('REPO_READ', repository)")
     *
     * @Template("repository/index.html.twig")
     */
    public function indexAction(Request $request, Repository $repository)
    {
        if ($this->isGranted('REPO_WRITE', $repository)) {
            $form = $this->createForm('repository', $repository);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->get('doctrine')->getManager()->flush();

                return $this->redirect($this->generateUrl('repository', [
                    'name' => $repository->getName(),
                ]));
            }
        }

        return [
            'repository' => $repository,
            'form' => isset($form) ? $form->createView() : null,
        ];
    }

    /**
     * @Route("/r/{name}/~/webhooks", requirements={"name"="%regex_name%"}, methods={"GET", "POST"}, name="repository_webhooks")
     *
     * @ParamConverter("repository", options={"mapping": {"name": "name"}})
     *
     * @Security("is_granted('REPO_WRITE', repository)")
     *
     * @Template("repository/webhooks.html.twig")
     */
    public function webhooksAction(Request $request, Repository $repository)
    {
        $form = $this->createForm('webhook', new Webhook($repository));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($form->getData());
            $em->flush();

            $this->addFlash('success', 'webhook.created');

            return $this->redirect($this->generateUrl('repository_webhooks', [
                'name' => $repository->getName(),
            ]));
        }

        $pager = $this->get('datatheke.pager')->createHttpPager('AppBundle:Webhook');
        $pager->setFilter(new Filter(['repository'], [$repository]), 'base');

        /** @var PagerView $pagerView */
        $pagerView = $pager->handleRequest($request);
        $pagerView->setParameters(['name' => $repository->getName()]);

        return [
            'repository' => $repository,
            'form' => $form->createView(),
            'pager' => $pagerView,
        ];
    }

    /**
     * @Route("/r/{name}/~/webhooks/{id}/remove", requirements={"name"="%regex_name%"}, methods={"GET", "POST"}, name="repository_webhooks_remove")
     *
     * @ParamConverter("repository", options={"mapping": {"name": "name"}})
     *
     * @Security("is_granted('REPO_WRITE', repository)")
     */
    public function removeWebhookAction(Request $request, Repository $repository, $id)
    {
        if (!$this->isCsrfTokenValid('webhook_remove', $request->query->get('_token'))) {
            $this->addFlash('error', 'invalid_csrf_token');

            return $this->redirectToRoute('repository_webhooks', ['name' => $repository->getName()]);
        }

        $webhook = $this->get('doctrine')->getRepository('AppBundle:Webhook')->findOneBy([
            'repository' => $repository,
            'id' => $id,
        ]);
        if (null === $webhook) {
            $this->addFlash('error', 'webhook.not_found_or_not_granted');

            return $this->redirectToRoute('repository_webhooks', ['name' => $repository->getName()]);
        }

        $manager = $this->get('doctrine')->getManager();
        $manager->remove($webhook);
        $manager->flush();

        $this->addFlash('success', 'webhook.removed');

        return $this->redirectToRoute('repository_webhooks', ['name' => $repository->getName()]);
    }

    /**
     * @Route("/r/{name}/~/build", requirements={"name"="%regex_name%"}, methods={"GET", "POST"}, name="repository_build")
     *
     * @ParamConverter("repository", options={"mapping": {"name": "name"}})
     *
     * @Security("is_granted('REPO_WRITE', repository)")
     *
     * @Template("repository/build.html.twig")
     */
    public function buildAction(Request $request, Repository $repository)
    {
        $form = $this->createForm('build_config', new BuildConfig($repository));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($form->getData());
            $em->flush();

            $this->addFlash('success', 'build_config.updated');

            return $this->redirect($this->generateUrl('repository_build', [
                'name' => $repository->getName(),
            ]));
        }

        $pager = $this->get('datatheke.pager')->createHttpPager('AppBundle:BuildConfig');
        $pager->setFilter(new Filter(['repository'], [$repository]), 'base');

        /** @var PagerView $pagerView */
        $pagerView = $pager->handleRequest($request);
        $pagerView->setParameters(['name' => $repository->getName()]);

        return [
            'repository' => $repository,
            'form' => $form->createView(),
            'pager' => $pagerView,
        ];
    }

    /**
     * @Route("/r/{name}/~/build/url", requirements={"name"="%regex_name%"}, methods={"GET", "POST"}, name="repository_build_url")
     *
     * @ParamConverter("repository", options={"mapping": {"name": "name"}})
     *
     * @Security("is_granted('REPO_WRITE', repository)")
     *
     * @Template("repository/build_url.html.twig")
     */
    public function buildUrlAction(Request $request, Repository $repository)
    {
        $form = $this->createForm('repository_build_config', $repository, [
            'action' => $this->generateUrl('repository_build_url', ['name' => $repository->getName()]),
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->get('doctrine')->getManager()->flush();

            return $this->redirect($this->generateUrl('repository_build', [
                'name' => $repository->getName(),
            ]));
        }

        return [
            'repository' => $repository,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/r/{name}/~/build/{id}/remove", requirements={"name"="%regex_name%"}, methods={"GET", "POST"}, name="repository_build_remove")
     *
     * @ParamConverter("repository", options={"mapping": {"name": "name"}})
     *
     * @Security("is_granted('REPO_WRITE', repository)")
     */
    public function removeBuildConfigAction(Request $request, Repository $repository, $id)
    {
        if (!$this->isCsrfTokenValid('build_remove', $request->query->get('_token'))) {
            $this->addFlash('error', 'invalid_csrf_token');

            return $this->redirectToRoute('repository_build', ['name' => $repository->getName()]);
        }

        $buildConfig = $this->get('doctrine')->getRepository('AppBundle:BuildConfig')->findOneBy([
            'repository' => $repository,
            'id' => $id,
        ]);
        if (null === $buildConfig) {
            $this->addFlash('error', 'build.not_found_or_not_granted');

            return $this->redirectToRoute('repository_build', ['name' => $repository->getName()]);
        }

        $manager = $this->get('doctrine')->getManager();
        $manager->remove($buildConfig);
        $manager->flush();

        $this->addFlash('success', 'build_config.removed');

        return $this->redirectToRoute('repository_build', ['name' => $repository->getName()]);
    }

    /**
     * @Route("/r/{name}/~/build/trigger", requirements={"name"="%regex_name%"}, methods={"GET", "POST"}, name="repository_build_trigger")
     *
     * @ParamConverter("repository", options={"mapping": {"name": "name"}})
     *
     * @Security("is_granted('REPO_WRITE', repository)")
     */
    public function triggerBuildAction(Request $request, Repository $repository)
    {
        $buildConfigs = $this->get('doctrine')->getRepository('AppBundle:BuildConfig')->findByRepository($repository);

        /** @var BuildConfig $config */
        foreach ($buildConfigs as $config) {
            $message = new Message($this->get('serializer')->serialize($config, 'json', ['groups' => ['repository_build']]));
            $this->get('swarrot.publisher')->publish('repository_build', $message);
        }

        return $this->redirectToRoute('repository_build', ['name' => $repository->getName()]);
    }
}
