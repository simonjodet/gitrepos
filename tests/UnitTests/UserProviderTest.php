<?php

namespace Tests\UnitTests;

class UserProviderTest extends \PHPUnit_Framework_TestCase
{
    public function test_loadUserByUsername_returns_a_User_object()
    {
        $app = new \Silex\Application();

        $ConnectionMock = \Mockery::mock('\Doctrine\DBAL\Connection');
        $ConnectionMock
            ->shouldReceive('executeQuery->fetch')
            ->andReturn(array('something'));

        $app['db'] = $ConnectionMock;

        $UserProvider = new \Gitrepos\UserProvider($app);


        $this->assertInstanceOf(
            '\Gitrepos\Entities\User',
            $UserProvider->loadUserByUsername('username')
        );
    }

    public function test_loadUserByUsername_throws_exception_if_db_fetch_fails()
    {
        $this->setExpectedException(
            '\Symfony\Component\Security\Core\Exception\UsernameNotFoundException',
            'Username "username" does not exist.'
        );
        $app = new \Silex\Application();

        $ConnectionMock = \Mockery::mock('\Doctrine\DBAL\Connection');
        $ConnectionMock
            ->shouldReceive('executeQuery->fetch')
            ->andReturn(false);

        $app['db'] = $ConnectionMock;

        $UserProvider = new \Gitrepos\UserProvider($app);


        $UserProvider->loadUserByUsername('username');
    }

    public function test_refreshUser_calls_loadUserByUsername_correctly()
    {
        $User = new \Gitrepos\Entities\User(array('username' => 'username'));

        $app = new \Silex\Application();

        $ConnectionMock = \Mockery::mock('\Doctrine\DBAL\Connection');
        $app['db'] = $ConnectionMock;

        $UserProvider = \Mockery::mock('\Gitrepos\UserProvider[loadUserByUsername]', array($app));

        $UserProvider
            ->shouldReceive('loadUserByUsername')
            ->with('username')
            ->once()
            ->andReturn('Refreshed user');

        $this->assertEquals('Refreshed user', $UserProvider->refreshUser($User));
    }

    public function test_refreshUser_throws_exception_if_passed_the_wrong_parameter()
    {
        $User = \Mockery::mock('\Symfony\Component\Security\Core\User\UserInterface');

        $this->setExpectedException(
            '\Symfony\Component\Security\Core\Exception\UnsupportedUserException',
            'Instances of "' . get_class($User) . '" are not supported.'
        );

        $app = new \Silex\Application();

        $ConnectionMock = \Mockery::mock('\Doctrine\DBAL\Connection');
        $app['db'] = $ConnectionMock;
        $UserProvider = new \Gitrepos\UserProvider($app);

        $UserProvider->refreshUser($User);
    }

    public function test_supportsClass_checks_for_the_correct_class()
    {
        $app = new \Silex\Application();

        $ConnectionMock = \Mockery::mock('\Doctrine\DBAL\Connection');
        $app['db'] = $ConnectionMock;
        $UserProvider = new \Gitrepos\UserProvider($app);

        $this->assertTrue($UserProvider->supportsClass('Symfony\Component\Security\Core\User\User'));
    }
}
