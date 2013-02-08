<?php

use Behat\Behat\Context\BehatContext;

class SessionsSubContext extends BehatContext
{
    private $userName = '';
    private $password = '';
    private $email;
    private $scenario_title;

    /**
     * @When /^I create the account$/
     */
    public function iCreateTheAccount()
    {
        $usersContext = $this->getMainContext()->getSubcontext('users');
        $Request = new \HttpWrapper\Request();
        $Request->post(
            $this->getMainContext()->url . '/v1/users?scenario=' . urlencode($this->getMainContext()->scenario_title),
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
        $this->getMainContext()->response = $Request->post(
            $this->getMainContext()->url . '/v1/sessions?scenario=' . urlencode($this->getMainContext()->scenario_title),
            array(),
            '{
            "username":"' . $identifier . '",
            "password":"' . $usersContext->password . '"
    }'
        );
    }

    /**
     * @Given /^I want to login with "([^"]*)" as password$/
     */
    public function iWantToLoginWithAsPassword($password)
    {
        $usersContext = $this->getMainContext()->getSubcontext('users');
        $Request = new \HttpWrapper\Request();
        $this->getMainContext()->response = $Request->post(
            $this->getMainContext()->url . '/v1/sessions?scenario=' . urlencode($this->getMainContext()->scenario_title),
            array(),
            '{
            "username":"' . $usersContext->userName . '",
            "password":"' . $password . '"
    }'
        );
    }

    /**
     * @When /^I log out$/
     */
    public function iLogOut()
    {
        $Request = new \HttpWrapper\Request();
        $this->getMainContext()->response = $Request->delete(
            $this->getMainContext()->url . '/v1/sessions/current?scenario=' . urlencode($this->getMainContext()->scenario_title),
            array('Cookie: SESSION=' . $this->getMainContext()->session)
        );
    }
}