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
    private $session;

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
            "username":"' . $identifier . '",
            "password":"' . $usersContext->password . '"
    }'
        );
        $usersContext->response = $this->response;
    }

    /**
     * @Given /^I want to login with "([^"]*)" as password$/
     */
    public function iWantToLoginWithAsPassword($password)
    {
        $usersContext = $this->getMainContext()->getSubcontext('users');
        $Request = new \HttpWrapper\Request();
        $this->response = $Request->post(
            'http://localhost:8000/v1/sessions?scenario=' . urlencode($this->scenario_title),
            array(),
            '{
            "username":"' . $usersContext->userName . '",
            "password":"' . $password . '"
    }'
        );
        $usersContext->response = $this->response;
    }

    /**
     * @Given /^the body string should match the following regexp:$/
     */
    public function theBodyStringShouldMatchTheFollowingRegexp(PyStringNode $string)
    {
        assertRegExp(trim($string->getRaw()), trim($this->response->getBody()));
    }

    /**
     * @Given /^the headers  should match the following regexp:$/
     */
    public function theHeadersShouldMatchTheFollowingRegexp(PyStringNode $string)
    {
        assertRegExp(trim($string->getRaw()), implode('', $this->response->getHeaders()));
    }

    /**
     * @Given /^that I\'m logged in as "([^"]*)" "([^"]*)"$/
     */
    public function thatIMLoggedInAs($username, $password)
    {
        $Request = new \HttpWrapper\Request();
        $response = $Request->post(
            'http://localhost:8000/v1/sessions?scenario=' . urlencode($this->scenario_title),
            array(),
            '{
            "username":"' . $username . '",
            "password":"' . $password . '"
    }'
        );
        $response = json_decode($response->getBody(), true);
        $this->session = $response['session'];
    }

    /**
     * @When /^I log out$/
     */
    public function iLogOut()
    {
        $Request = new \HttpWrapper\Request();
        $this->response = $Request->delete(
            'http://localhost:8000/v1/sessions/current?scenario=' . urlencode($this->scenario_title),
            array('Cookie: SESSION=' . $this->session)
        );
        $usersContext = $this->getMainContext()->getSubcontext('users');
        $usersContext->response = $this->response;
    }
}