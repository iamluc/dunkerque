<?php

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Sanpi\Behatch\Context\RestContext as BaseRestContext;

class RestContext extends BaseRestContext
{
    protected $headers = [];

    /**
     * @BeforeScenario @fixtures
     */
    public function loadFixtures(BeforeScenarioScope $scope)
    {
        $this->headers = [];
        $this->iAddHeaderEqualTo('PHP_AUTH_USER', null);
        $this->iAddHeaderEqualTo('PHP_AUTH_PW', null);
    }

    /**
     * @AfterScenario
     */
    public function afterScenario(AfterScenarioScope $scope)
    {
        foreach ($this->headers as $name) {
            $this->iAddHeaderEqualTo($name, null);
        }
    }

    /**
     * @Given I set basic authentication with :username and :password
     */
    public function iAmAuthenticatedAs($username, $password)
    {
        // Does not work !
        // But let it to be future proof
        $this->getSession()->setBasicAuth($username, $password);

        // Workaround
        $this->iAddHeaderEqualTo('PHP_AUTH_USER', $username);
        $this->iAddHeaderEqualTo('PHP_AUTH_PW', $password);
    }

    /**
     * @Then I set header :name to :value
     */
    public function iAddHeaderEqualTo($name, $value)
    {
        $this->headers[] = $name;

        parent::iAddHeaderEqualTo('HTTP_'.$name, $value);
    }
}
