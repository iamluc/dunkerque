<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Repository;
use AppBundle\Entity\Webhook;
use AppBundle\Form\Type\RepositoryType;
use AppBundle\Form\Type\WebhookType;
use Datatheke\Bundle\PagerBundle\Pager\Filter;
use Datatheke\Bundle\PagerBundle\Pager\PagerView;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
            $form = $this->createForm(RepositoryType::class, $repository);
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
        $form = $this->createForm(WebhookType::class, new Webhook($repository));
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
}
