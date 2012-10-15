<?php
namespace Tests\UnitTests;
class UserTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructor_accepts_array_of_data()
    {
        $UserData = array(
            'id' => 42,
            'username' => 'simon',
            'email' => 'simon@jodet.com',
            'password' => 'pa$$word'
        );

        $User = new \Gitrepos\User($UserData);

        $this->assertEquals(42, $User->getId());
        $this->assertEquals('simon', $User->getUsername());
        $this->assertEquals('simon@jodet.com', $User->getEmail());
        $this->assertEquals('pa$$word', $User->getPassword());
    }

    public function test_constructor_ignores_unexpected_property()
    {
        $UserData = array(
            'id' => 42,
            'not_a_property' => 'foo',
            'email' => 'simon@jodet.com',
            'password' => 'pa$$word'
        );

        $User = new \Gitrepos\User($UserData);

        $this->assertEquals(42, $User->getId());
        $this->assertEquals('simon@jodet.com', $User->getEmail());
        $this->assertEquals('pa$$word', $User->getPassword());
    }

    public function test_constructor_ignores_non_arrays()
    {
        $UserData = 'string';

        $User = new \Gitrepos\User($UserData);

        $this->assertInstanceOf('\Gitrepos\User', $User);
    }

    public function test_constructor_data_parameter_is_optional()
    {
        $User = new \Gitrepos\User();

        $this->assertInstanceOf('\Gitrepos\User', $User);
    }
}
