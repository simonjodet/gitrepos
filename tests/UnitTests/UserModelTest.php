<?php
namespace Tests\UnitTests;
class UserModelTest extends \PHPUnit_Framework_TestCase
{
    public function test_create_expects_a_user_object()
    {
        $this->setExpectedException('\Exception');

        $AppMock = \Mockery::mock('\Silex\Application');

        $UserModel = new \Gitrepos\UserModel($AppMock);

        $UserModel->create('Not a User object');
    }

    private function getEncoderMock(\Silex\Application $app)
    {
        $encoderFactoryMock = \Mockery::mock('\Symfony\Component\Security\Core\Encoder\EncoderFactory');
        $encoderFactoryMock
            ->shouldReceive('getEncoder->encodePassword')
            ->andReturn('encoded pwd');
        $app['security.encoder_factory'] = $encoderFactoryMock;
return $app;
    }

    public function test_create_encodes_the_users_password_and_returns_user_id()
    {
        $app = $this->getEncoderMock(new \Silex\Application());

        $dbMock = \Mockery::mock();
        $dbMock
            ->shouldReceive('insert')
            ->with(
            'users',
            array(
                'username' => 'simon',
                'email' => 'email@domain.com',
                'password' => 'encoded pwd'
            )
        )
            ->once();

        $dbMock
            ->shouldReceive('lastInsertId')
            ->andReturn(42)
            ->once();
        $app['db'] = $dbMock;

        $UserModel = new \Gitrepos\UserModel($app);
        $User = new \Gitrepos\User(array(
            'username' => 'simon',
            'email' => 'email@domain.com',
            'password' => 'encoded pwd'
        ));
        $createdUser = $UserModel->create($User);

        $this->assertEquals(42, $createdUser->getId());
        $this->assertEquals('simon', $createdUser->getUsername());
        $this->assertEquals('encoded pwd', $createdUser->getPassword());
        $this->assertEquals('email@domain.com', $createdUser->getEmail());
    }

    public function test_create_throws_the_expected_exceptions_if_username_already_exists()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\DuplicateUsername');

        $app = $this->getEncoderMock(new \Silex\Application());

        $dbMock = \Mockery::mock();
        $dbMock
            ->shouldReceive('insert')
            ->andThrow('PDOException', 'SQLSTATE[23000]: Integrity constraint violation: 19 column username is not unique', 23000);

        $app['db'] = $dbMock;

        $UserModel = new \Gitrepos\UserModel($app);
        $User = new \Gitrepos\User(array(
            'username' => 'simon',
            'email' => 'email@domain.com',
            'password' => 'encoded pwd'
        ));

        $UserModel->create($User);

    }
    public function test_create_throws_the_expected_exceptions_if_email_already_exists()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\DuplicateEmail');

        $app = $this->getEncoderMock(new \Silex\Application());

        $dbMock = \Mockery::mock();
        $dbMock
            ->shouldReceive('insert')
            ->andThrow('PDOException', 'SQLSTATE[23000]: Integrity constraint violation: 19 column email is not unique', 23000);

        $app['db'] = $dbMock;

        $UserModel = new \Gitrepos\UserModel($app);
        $User = new \Gitrepos\User(array(
            'username' => 'simon',
            'email' => 'email@domain.com',
            'password' => 'encoded pwd'
        ));

        $UserModel->create($User);
    }
}
