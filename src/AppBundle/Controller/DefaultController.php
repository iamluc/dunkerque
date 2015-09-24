<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", methods={"GET"}, name="index")
     *
     * @Template("default/index.html.twig")
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Template("default/last_public_repositories.html.twig")
     */
    public function lastPublicRepositoriesAction()
    {
        $repositories = $this->get('doctrine')->getRepository('AppBundle:Repository')->getLatestPublic();

        return [
            'repositories' => $repositories,
        ];
    }

    /**
     * @Template("default/most_stared_repositories.html.twig")
     */
    public function mostStaredRepositoriesAction()
    {
        $repositories = $this->get('doctrine')->getRepository('AppBundle:Repository')->getMostStared();

        return [
            'repositories' => $repositories,
        ];
    }

    /**
     * @Template("default/most_pulled_repositories.html.twig")
     */
    public function mostPulledRepositoriesAction()
    {
        $repositories = $this->get('doctrine')->getRepository('AppBundle:Repository')->getMostPulled();

        return [
            'repositories' => $repositories,
        ];
    }
}
