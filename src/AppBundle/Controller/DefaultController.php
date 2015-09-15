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
     * @Template("default/manifests.html.twig")
     */
    public function manifestsListAction()
    {
        return [
            'manifests' => $this->get('doctrine')->getRepository('AppBundle:Manifest')->findAll(),
        ];
    }
}
