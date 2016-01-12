<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

class RepositoryStarListener
{
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
        $qb = $em->createQueryBuilder()
            ->update(Repository::class, 'r')
            ->set('r.stars', "r.stars $operator 1")
            ->where('r.id = :id')
            ->setParameter('id', $repositoryStar->getRepository()->getId())
            ->getQuery();

        return $qb->execute();
    }
}
