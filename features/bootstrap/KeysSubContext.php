<?php

use Behat\Behat\Context\BehatContext,
    Behat\Gherkin\Node\PyStringNode,
    Behat\Behat\Exception\PendingException;

class KeysSubContext extends BehatContext
{
    private $key_title;
    private $key_value;

    public function __construct()
    {
        // do subcontext initialization
    }

    /**
     * @Given /^my key title is "([^"]*)"$/
     */
    public function myKeyTitleIs($title)
    {
        $this->key_title = $title;
    }

    /**
     * @Given /^my key value is:$/
     */
    public function myKeyValueIs(PyStringNode $value)
    {
        $this->key_value = $value->getRaw();
    }

    /**
     * @When /^I add the key$/
     */
    public function iAddTheKey()
    {
        $Request = new \HttpWrapper\Request();
        $this->getMainContext()->response = $Request->post(
            $this->getMainContext()->url . '/v1/keys?scenario=' . urlencode($this->getMainContext()->scenario_title),
            array('Cookie: SESSION=' . $this->getMainContext()->session),
            json_encode(
                array(
                    'title' => $this->key_title,
                    'value' => $this->key_value
                )
            )
        );

    }
}