<?php

namespace AppBundle\Controller;

use Datatheke\Bundle\PagerBundle\Pager\HttpPagerInterface;
use Datatheke\Bundle\PagerBundle\Pager\PagerView;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    /**
     * @Route("/search", methods={"GET"}, name="search")
     *
     * @Template("search/results.html.twig")
     */
    public function search(Request $request)
    {
        $keyword = $request->query->get('q', '');

        /** @var HttpPagerInterface $pager */
        $pager = $this->get('search_manager')->createPager($keyword);

        /** @var PagerView $view */
        $view = $pager->handleRequest($request);
        $view->setParameters(['q' => $keyword]);

        return [
            'keyword' => $keyword,
            'pager' => $view,
        ];
    }
}
