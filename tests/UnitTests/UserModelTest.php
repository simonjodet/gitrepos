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

    public function test_create_encodes_the_users_password_and_returns_user_id()
    {
        $app = new \Silex\Application();
        $encoderFactoryMock = \Mockery::mock('\Symfony\Component\Security\Core\Encoder\EncoderFactory');
        $encoderFactoryMock
            ->shouldReceive('getEncoder->encodePassword')
            ->andReturn('encoded pwd');
        $app['security.encoder_factory'] = $encoderFactoryMock;

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
}
