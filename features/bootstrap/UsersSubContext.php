<?php

use Behat\Behat\Context\BehatContext,
    Behat\Gherkin\Node\PyStringNode;

class UsersSubContext extends BehatContext
{
    private $userName = '';
    private $password = '';
    private $email;
    /**
     * @var \HttpWrapper\Response $response
     */
    private $response;

    public function __construct()
    {
        // do subcontext initialization
    }

    /**
     * @Given /^that I want to create a new "([^"]*)" user$/
     */
    public function thatIWantToCreateANewUser($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @Given /^that I want to create a new user with a username shorter than (\d+) characters$/
     */
    public function thatIWantToCreateANewUserWithAUsernameShorterThanCharacters($length)
    {
        while (strlen($this->userName) < $length - 1) {
            $this->userName .= 'a';
        }
    }

    /**
     * @Given /^that I want to create a new user with a username longer than (\d+) characters$/
     */
    public function thatIWantToCreateANewUserWithAUsernameLongerThanCharacters($length)
    {
        while (strlen($this->userName) < $length + 1) {
            $this->userName .= 'a';
        }
    }

    /**
     * @Given /^his password is "([^"]*)"$/
     */
    public function hisPasswordIs($password)
    {
        $this->password = $password;
    }

    /**
     * @Given /^his password is shorter than (\d+) characters$/
     */
    public function hisPasswordIsShorterThanCharacters($length)
    {
        while (strlen($this->password) < $length - 1) {
            $this->password .= 'a';
        }
    }

    /**
     * @Given /^his password is longer than (\d+) characters$/
     */
    public function hisPasswordIsLongerThanCharacters($length)
    {
        while (strlen($this->password) < $length + 1) {
            $this->password .= 'a';
        }
    }

    /**
     * @Given /^his email is "([^"]*)"$/
     */
    public function hisEmailIs($email)
    {
        $this->email = $email;
    }

    /**
     * @When /^I request the URL "([^"]*)" with the POST method$/
     */
    public function iRequestTheUrlWithThePostMethod($url)
    {
        $Request = new \HttpWrapper\Request();
        $this->response = $Request->post(
            'http://localhost:8000' . $url,
            array(),
            '{
	        "username":"' . $this->userName . '",
	        "email":"' . $this->email . '",
	        "password":"' . $this->password . '"
	    }'
        );
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