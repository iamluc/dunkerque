<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RepositoryRepository extends EntityRepository
{
    public function findByAccount(User $account, $isOwner = false)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.owner = :account')
            ->setParameter('account', $account)
            ;

        if (!$isOwner) {
            $qb->andWhere('r.private = false');
        }

        return $qb;
    }

    public function findByNameOrCreate($name, User $owner)
    {
        $repository = $this->findOneByName($name);
        if (null === $repository) {
            $repository = $this->create($name, $owner);
        }

        return $repository;
    }

    public function create($name, User $owner)
    {
        return new Repository($name, $owner);
    }

    public function save(Repository $repository)
    {
        $this->_em->persist($repository);
        $this->_em->flush();
    }

    public function getLatestPublic($limit = 10)
    {
        return $this->findBy(['private' => false], null, $limit);
    }

    public function getMostStared($limit = 10)
    {
        return $this->findBy(['private' => false], ['stars' => 'desc'], $limit);
    }

    public function getMostPulled($limit = 10)
    {
        return $this->findBy(['private' => false], ['pulls' => 'desc'], $limit);
    }
}
