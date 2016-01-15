<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\SearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    /**
     * @Template("search/form.html.twig")
     */
    public function formAction()
    {
        $form = $this->createForm(SearchType::class, null, ['action' => $this->generateUrl('search')]);

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/search", methods={"GET"}, name="search")
     *
     * @Template("search/results.html.twig")
     */
    public function search(Request $request)
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->redirectToRoute('index');
        }

        $data = $form->getData();
        $keyword = $data['keyword'];

        $adapter = $this->get('search_manager')->getPaginatorAdapter($keyword);
        $pagination = $this->get('knp_paginator')->paginate($adapter, $request->query->getInt('page', 1));

        return [
            'keyword' => $keyword,
            'pagination' => $pagination,
        ];
    }
}
