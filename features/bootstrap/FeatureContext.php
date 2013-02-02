<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    public $scenario_title;
    /**
     * @var \HttpWrapper\Response $response
     */
    public $response;
    public $session;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->useContext('users', new UsersSubContext());
        $this->useContext('sessions', new SessionsSubContext());
        $this->useContext('keys', new KeysSubContext());
    }

    /** @BeforeScenario */
    public function before(Behat\Behat\Event\ScenarioEvent $event)
    {
        $this->scenario_title = $event->getScenario()->getTitle();
        exec(__DIR__ . '/../../vendor/bin/phake db:reset');
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
     * @Then /^the response code should be "([^"]*)"$/
     */
    public function theResponseCodeShouldBe($responseCode)
    {
        $responseCode = intval($responseCode, 10);
        assertEquals($responseCode, $this->response->getCode());
    }

    /**
     * @Given /^the body should be "([^"]*)"$/
     */
    public function theBodyShouldBe($body)
    {
        assertEquals($body, $this->response->getBody());
    }

    /**
     * @Given /^the body string should be:$/
     */
    public function theBodyStringShouldBe(PyStringNode $string)
    {
        assertEquals($string->getRaw(), $this->response->getBody());
    }
}
