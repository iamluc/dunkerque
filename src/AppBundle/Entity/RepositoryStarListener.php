<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use FOS\ElasticaBundle\Persister\ObjectPersister;

class RepositoryStarListener
{
    /** @var ObjectPersister */
    protected $elasticaObjectPersister;

    public function __construct(ObjectPersister $elasticaObjectPersister)
    {
        $this->elasticaObjectPersister = $elasticaObjectPersister;
    }

    /**
     * @ORM\PostPersist
     */
    public function incStarsCount(RepositoryStar $repositoryStar, LifecycleEventArgs $events)
    {
        $this->updateStarsCount($repositoryStar, $events->getEntityManager(), '+');
    }

    /**
     * @ORM\PostRemove
     */
    public function decStarsCount(RepositoryStar $repositoryStar, LifecycleEventArgs $events)
    {
        $this->updateStarsCount($repositoryStar, $events->getEntityManager(), '-');
    }

    protected function updateStarsCount(RepositoryStar $repositoryStar, EntityManager $em, $operator)
    {
        $repository = $repositoryStar->getRepository();

        $qb = $em->createQueryBuilder()
            ->update(Repository::class, 'r')
            ->set('r.stars', "r.stars $operator 1")
            ->where('r.id = :id')
            ->setParameter('id', $repository->getId())
            ->getQuery();
        $qb->execute();

        $em->refresh($repository);

        $this->elasticaObjectPersister->replaceOne($repository);
    }
}
