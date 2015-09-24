<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Repository;
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
        $write = $this->isGranted('REPO_WRITE', $repository);

        $form = $this->createForm('repository', $repository, ['disabled' => !$write]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->get('doctrine')->getManager()->flush();

            return $this->redirect($this->generateUrl('repository', [
                'name' => $repository->getName(),
            ]));
        }

        return [
            'repository' => $repository,
            'form' => $form->createView(),
        ];
    }
}
