<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Datatheke\Bundle\PagerBundle\Pager\PagerView;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AccountController extends Controller
{
    /**
     * @Route("/u/{account}", methods={"GET", "POST"}, name="account")
     *
     * @ParamConverter("account", options={"mapping": {"account": "username"}})
     *
     * @Template("account/index.html.twig")
     */
    public function indexAction(Request $request, User $account)
    {
        $isOwner = $this->getUser() && ($account === $this->getUser());

        $qb = $this->get('doctrine')->getRepository('AppBundle:Repository')->findByAccount($account, $isOwner);
        $pager = $this->get('datatheke.pager')->createHttpPager($qb);

        /** @var PagerView $view */
        $view = $pager->handleRequest($request);
        $view->setParameters(['account' => $account->getUsername()]);

        return [
            'isOwner' => $isOwner,
            'account' => $account,
            'pager' => $view,
        ];
    }
}
