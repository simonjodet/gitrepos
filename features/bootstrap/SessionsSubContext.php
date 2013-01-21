<?php

use Behat\Behat\Context\BehatContext,
    Behat\Gherkin\Node\PyStringNode,
    Behat\Behat\Exception\PendingException;

class SessionsSubContext extends BehatContext
{
    private $userName = '';
    private $password = '';
    private $email;
    private $scenario_title;
    /**
     * @var \HttpWrapper\Response $response
     */
    private $response;

    public function __construct()
    {
        // do subcontext initialization
    }

    /** @BeforeScenario */
    public function before(Behat\Behat\Event\ScenarioEvent $event)
    {
        $this->scenario_title = $event->getScenario()->getTitle();
        exec(__DIR__ . '/../../vendor/bin/phake db:reset');
    }

    /**
     * @When /^I create the account$/
     */
    public function iCreateTheAccount()
    {
        $usersContext = $this->getMainContext()->getSubcontext('users');
        $Request = new \HttpWrapper\Request();
        $Request->post(
            'http://localhost:8000/v1/users?scenario=' . urlencode($this->scenario_title),
            array(),
            '{
            "username":"' . $usersContext->userName . '",
            "email":"' . $usersContext->email . '",
            "password":"' . $usersContext->password . '"
    }'
        );
    }

    /**
     * @Given /^I want to login with "([^"]*)" as identifier$/
     */
    public function iWantToLoginWithAsIdentifier($identifier)
    {
        $usersContext = $this->getMainContext()->getSubcontext('users');
        $Request = new \HttpWrapper\Request();
        $this->response = $Request->post(
            'http://localhost:8000/v1/sessions?scenario=' . urlencode($this->scenario_title),
            array(),
            '{
            "user":"' . $identifier . '",
            "password":"' . $usersContext->password . '"
    }'
        );
    }

    /**
     * @Given /^the body string should match the following regexp:$/
     */
    public function theBodyStringShouldMatchTheFollowingRegexp(PyStringNode $string)
    {
        assertRegExp(trim($string->getRaw()), trim($this->response->getBody()));
    }
}