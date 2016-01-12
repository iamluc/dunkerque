<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Repository;
use AppBundle\Entity\User;
use AppBundle\Manager\RepositoryStarManager;

class AppExtension extends \Twig_Extension
{
    /** @var RepositoryStarManager */
    protected $repositoryStarManager;

    public function __construct(RepositoryStarManager $repositoryStarManager)
    {
        $this->repositoryStarManager = $repositoryStarManager;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('is_starred_by_user', [$this, 'isStarredByUser']),
        ];
    }

    public function isStarredByUser(Repository $repository, User $user)
    {
        return $this->repositoryStarManager->isStarredByUser($repository, $user);
    }

    public function getName()
    {
        return 'app_extension';
    }
}
