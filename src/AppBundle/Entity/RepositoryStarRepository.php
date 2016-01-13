<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RepositoryStarRepository extends EntityRepository
{
    public function findOneByRepositoryAndUser(Repository $repository, User $user)
    {
        return $this->findOneBy([
            'repository' => $repository,
            'user' => $user,
        ]);
    }
}
