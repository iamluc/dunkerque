<?php

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Sanpi\Behatch\Context\RestContext as BaseRestContext;
use Sanpi\Behatch\HttpCall\Request;

class RestContext extends BaseRestContext
{
    protected $headers = [];
    protected $variables = [];

    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;

        parent::__construct($request);
    }

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
     * @BeforeScenario
     */
    public function beforeScenario(BeforeScenarioScope $scope)
    {
        $this->variables = [];
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

    /**
     * @Then the response should be equal to file :filename
     */
    public function theResponseShouldBeEqualToFile($filename)
    {
        $fixturesPath = __DIR__.'/../fixtures/';

        $actual = $this->request->getContent();
        $message = "The content of file '$filename' is not equal to the response of the current page";
        $this->assertEquals(file_get_contents($fixturesPath.$filename), $actual, $message);
    }

    /**
     * @Then I store value of header :header to variable :name
     */
    public function iStoreValueOfHeaderToVariable($header, $name)
    {
        $this->variables[$name] = $this->request->getHttpHeader($header);
    }

    /**
     * @Then I send a :method request to :url with body :filename

     */
    public function iSendARequestToWithBody($method, $url, $filename)
    {
        $fixturesPath = __DIR__.'/../fixtures/';

        $vars = array_map(function ($val) {return '{'.$val.'}';}, array_keys($this->variables));
        $url = strtr($url, array_combine($vars, array_values($this->variables)));

        return $this->request->send(
            $method,
            $this->locatePath($url),
            [],
            [],
            file_get_contents($fixturesPath.$filename)
        );
    }
}
