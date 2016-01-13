<?php

use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AuthenticationContext implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * @var MinkContext
     */
    protected $minkContext;

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        /** @var InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();
        $this->minkContext = $environment->getContext('Behat\MinkExtension\Context\MinkContext');
    }

    /**
     * @Given I am authenticated as :username
     */
    public function iAmAuthenticatedAs($username)
    {
        /** @var \Behat\Mink\Session $session */
        $minkSession = $this->minkContext->getSession();

        /** @var \Behat\Symfony2Extension\Driver\KernelDriver $driver */
        $driver = $minkSession->getDriver();
        if (!$driver instanceof BrowserKitDriver) {
            throw new UnsupportedDriverActionException('This step is only supported by the BrowserKitDriver', $driver);
        }

        /** @var \Symfony\Component\Security\Core\User\UserProviderInterface $userProvider */
        $userProvider = $this->getContainer()->get('fos_user.user_provider.username');
        $user = $userProvider->loadUserByUsername($username);

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());

        $client = $driver->getClient();
        $session = $client->getContainer()->get('session');
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }
}
