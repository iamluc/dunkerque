<?php

namespace AppBundle\Manager;

use Elastica\Query;
use Elastica\Query\Term;
use Elastica\Query\BoolQuery;
use Elastica\Query\SimpleQueryString;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SearchManager
{
    /** @var TransformedFinder */
    protected $finder;

    /** @var string|UserInterface */
    protected $user;

    public function __construct(TransformedFinder $finder, TokenStorageInterface $token)
    {
        $this->finder = $finder;
        $this->user = $token->getToken()->getUser();
    }

    /**
     * @param string $keyword
     *
     * @return array
     */
    public function getPaginatorAdapter($keyword)
    {
        $query = Query::create((new BoolQuery())
            ->addMust($this->getTermQuery($keyword))
            ->addMust($this->getPrivacyQuery())
        );

        return $this->finder->createPaginatorAdapter($query);
    }

    /**
     * @param string $keyword
     *
     * @return SimpleQueryString
     */
    protected function getTermQuery($keyword)
    {
        return new SimpleQueryString($keyword, ['name', 'title', 'description']);
    }

    /**
     * @return BoolQuery
     */
    protected function getPrivacyQuery()
    {
        $query = new BoolQuery();
        $query->addShould(new Term(['private' => false]));

        if ($this->user instanceof UserInterface) {
            $query->addShould(new Term(['owner.id' => $this->user->getId()]));
        }

        return $query;
    }
}
