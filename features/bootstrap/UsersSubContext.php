<?php

use Behat\Behat\Context\BehatContext,
    Behat\Gherkin\Node\PyStringNode,
    Guzzle\Http\Client;

class UsersSubContext extends BehatContext
{
    private $userName;
    private $password;
    private $email;
    /**
     * @var \Guzzle\Http\Message\Response $response
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
     * @Given /^his password is "([^"]*)"$/
     */
    public function hisPasswordIs($password)
    {
        $this->password = $password;
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
        $client = new Client();
        $this->response = $client->post(
            'http://localhost:8000' . $url,
            null,
            '{
	        "username":"' . $this->userName . '",
	        "email":"' . $this->email . '",
	        "password":"' . $this->password . '"
	    }'
        )->send();
    }

    /**
     * @Then /^the response code should be "([^"]*)"$/
     */
    public function theResponseCodeShouldBe($responseCode)
    {
        $responseCode = intval($responseCode, 10);
        assertEquals($responseCode, $this->response->getStatusCode());
    }

    /**
     * @Given /^the body should be "([^"]*)"$/
     */
    public function theBodyShouldBe($body)
    {
        assertEquals($body, $this->response->getBody(true));
    }
}