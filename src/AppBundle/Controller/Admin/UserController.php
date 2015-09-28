<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/", methods={"GET", "POST"}, name="admin_users")
     *
     * @Template("admin/user/index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $datagrid = $this->get('datatheke.datagrid')->createHttpDataGrid('AppBundle:User');
        $datagrid->showOnly(['username', 'email', 'enabled', 'id', 'lastLogin', 'locked']);

        return [
            'datagrid' => $datagrid->handleRequest($request),
        ];
    }
}
