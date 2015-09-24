<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\Repository;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RepositoryVoter implements VoterInterface
{
    const READ = 'REPO_READ';
    const WRITE = 'REPO_WRITE';

    /**
     * @var RoleHierarchyVoter
     */
    private $roleHierarchyVoter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ObjectManager
     */
    private $om;

    public function __construct(ObjectManager $om, RoleHierarchyVoter $roleHierarchyVoter, LoggerInterface $logger)
    {
        $this->om = $om;
        $this->roleHierarchyVoter = $roleHierarchyVoter;
        $this->logger = $logger;
    }

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, [self::READ, self::WRITE]);
    }

    public function supportsClass($class)
    {
        return true;
    }

    public function vote(TokenInterface $token, $repository, array $attributes)
    {
        // abstain vote by default in case none of the attributes are supported
        $vote = self::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            // as soon as at least one attribute is supported, default is to deny access
            $vote = self::ACCESS_DENIED;

            if ($this->isGranted($attribute, $repository, $token)) {
                // grant access as soon as at least one voter returns a positive response
                return self::ACCESS_GRANTED;
            }
        }

        return $vote;
    }

    protected function isGranted($attribute, $repository, TokenInterface $token)
    {
        // Admin can do everything
        if (VoterInterface::ACCESS_GRANTED === $this->roleHierarchyVoter->vote($token, null, ['ROLE_ADMIN'])) {
            return true;
        }

        $user = $token->getUser();

        // We allow to check by repository name
        // Needed when pushing the first manifest, that will create the repository
        if (!$repository instanceof Repository) {
            $name = $repository;
            $repository = $this->om->getRepository('AppBundle:Repository')->findOneByName($repository);
            if (null === $repository) {
                // repository does not exist
                // User tries to access root namespace but is not ADMIN
                if (false === strpos($name, '/')) {
                    return false;
                }

                // Use not logged
                if (!$user instanceof UserInterface) {
                    return false;
                }

                list($tld) = explode('/', $name);

                return $tld === $user->getUsername();
            }
        }

        $isOwner = $user instanceof UserInterface && $repository->getOwner() === $user;

        switch ($attribute) {
            case self::READ:
                return $isOwner || $repository->isPublic();

            case self::WRITE:
                return $isOwner;
        }

        return false;
    }
}
