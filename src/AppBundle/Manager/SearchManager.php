<?php

namespace AppBundle\Manager;

use AppBundle\Entity\User;
use Datatheke\Bundle\PagerBundle\Pager\Adapter\ElasticaAdapter;
use Datatheke\Bundle\PagerBundle\Pager\Factory;
use Datatheke\Bundle\PagerBundle\Pager\Field;
use Datatheke\Bundle\PagerBundle\Pager\HttpPager;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\SimpleQueryString;
use Elastica\Query\Term;
use Elastica\Type;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SearchManager
{
    /**
     * @var Factory
     */
    private $pagerFactory;
    /**
     * @var Type
     */
    private $repositoryType;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(Factory $pagerFactory, Type $repositoryType, TokenStorageInterface $tokenStorage)
    {
        $this->pagerFactory = $pagerFactory;
        $this->repositoryType = $repositoryType;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param string $keyword
     *
     * @return HttpPager
     */
    public function createPager($keyword)
    {
        $fields = [
            'name' => new Field('name'),
            'title' => new Field('title'),
            'description' => new Field('description'),
            'private' => new Field('private'),
        ];
        $adapter = new ElasticaAdapter($this->repositoryType, $fields, $this->createSearchQuery($keyword));

        return $this->pagerFactory->createHttpPager($adapter);
    }

    /**
     * @param string $keyword
     *
     * @return Query
     */
    protected function createSearchQuery($keyword)
    {
        return Query::create((new BoolQuery())
            ->addMust($this->getTermQuery($keyword))
            ->addMust($this->getPrivacyQuery())
        );
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
        if (($user = $this->tokenStorage->getToken()->getUser()) instanceof User) {
            $query->addShould(new Term(['owner.id' => $user->getId()]));
        }

        return $query;
    }
}
