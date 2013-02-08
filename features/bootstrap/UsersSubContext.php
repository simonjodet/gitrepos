<?php

use Behat\Behat\Context\BehatContext;

class UsersSubContext extends BehatContext
{
    public $userName = '';
    public $password = '';
    public $email;
    public $scenario_title;

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
        $this->getMainContext()->response = $Request->post(
            $this->getMainContext()->url . '' . $url . '?scenario=' . urlencode($this->getMainContext()->scenario_title),
            array(),
            '{
	        "username":"' . $this->userName . '",
	        "email":"' . $this->email . '",
	        "password":"' . $this->password . '"
	    }'
        );
    }

    /**
     * @Given /^I request the URL "([^"]*)" with the POST method again$/
     */
    public function iRequestTheUrlWithThePostMethodAgain($url)
    {
        $Request = new \HttpWrapper\Request();
        $this->getMainContext()->response = $Request->post(
            $this->getMainContext()->url . '' . $url . '?scenario=' . urlencode($this->getMainContext()->scenario_title),
            array(),
            '{
	        "username":"' . $this->userName . '",
	        "email":"' . $this->email . '",
	        "password":"' . $this->password . '"
	    }'
        );
    }
}