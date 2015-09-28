<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/repositories")
 */
class RepositoryController extends Controller
{
    /**
     * @Route("/", methods={"GET", "POST"}, name="admin_repositories")
     *
     * @Template("admin/repository/index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $datagrid = $this->get('datatheke.datagrid')->createHttpDataGrid('AppBundle:Repository');

        return [
            'datagrid' => $datagrid->handleRequest($request),
        ];
    }
}
