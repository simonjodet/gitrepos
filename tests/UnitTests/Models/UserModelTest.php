<?php
namespace Tests\UnitTests;

class UserModelTest extends \PHPUnit_Framework_TestCase
{
    public function test_create_expects_a_user_object()
    {
        $this->setExpectedException('\Exception');

        $AppMock = \Mockery::mock('\Silex\Application');

        $UserModel = new \Gitrepos\Models\UserModel($AppMock);

        $UserModel->create('Not a User object');
    }

    private function getEncoderMock(\Silex\Application $app)
    {
        $PasswordsMock = \Mockery::mock('\Gitrepos\Passwords');
        $PasswordsMock
            ->shouldReceive('password_hash')
            ->andReturn('encoded pwd');
        $app['passwords'] = $PasswordsMock;
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

        $UserModel = new \Gitrepos\Models\UserModel($app);
        $User = new \Gitrepos\Entities\User(array(
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
            ->andThrow(
                'PDOException',
                'SQLSTATE[23000]: Integrity constraint violation: 19 column username is not unique',
                23000
            );

        $app['db'] = $dbMock;

        $UserModel = new \Gitrepos\Models\UserModel($app);
        $User = new \Gitrepos\Entities\User(array(
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
            ->andThrow(
                'PDOException',
                'SQLSTATE[23000]: Integrity constraint violation: 19 column email is not unique',
                23000
            );

        $app['db'] = $dbMock;

        $UserModel = new \Gitrepos\Models\UserModel($app);
        $User = new \Gitrepos\Entities\User(array(
            'username' => 'simon',
            'email' => 'email@domain.com',
            'password' => 'encoded pwd'
        ));

        $UserModel->create($User);
    }

    public function test_authenticate_checks_password()
    {
        $app = new \Silex\Application();
        $PasswordsMock = \Mockery::mock('\Gitrepos\Passwords');
        $PasswordsMock
            ->shouldReceive('password_verify')
            ->once()
            ->andReturn(true);
        $app['passwords'] = $PasswordsMock;

        $statementMock = \Mockery::mock();
        $statementMock
            ->shouldReceive('bindValue')
            ->with('username', 'user_credential')
            ->once();


        $statementMock
            ->shouldReceive('execute')
            ->once();

        $statementMock
            ->shouldReceive('fetch')
            ->with(\PDO::FETCH_ASSOC)
            ->once()
            ->andReturn(
                array(
                    'id' => 42,
                    'username' => 'simon',
                    'email' => 'nobody@example.com',
                    'password' => 'some hash'
                )
            );

        $dbMock = \Mockery::mock();
        $dbMock
            ->shouldReceive('prepare')
            ->with("SELECT * FROM users WHERE username = :username LIMIT 1")
            ->once()
            ->andReturn($statementMock);

        $app['db'] = $dbMock;

        $UserModel = new \Gitrepos\Models\UserModel($app);
        $return = $UserModel->authenticate('user_credential', 'pwd');
        $this->assertEquals(
            array(
                'id' => 42,
                'username' => 'simon',
                'email' => 'nobody@example.com'
            ),
            $return
        );
    }

    public function test_authenticate_returns_user_info_without_password_on_correct_credentials()
    {
        $app = new \Silex\Application();
        $PasswordsMock = \Mockery::mock('\Gitrepos\Passwords');
        $PasswordsMock
            ->shouldReceive('password_verify')
            ->andReturn(true);
        $app['passwords'] = $PasswordsMock;

        $statementMock = \Mockery::mock();
        $statementMock->shouldReceive('bindValue');
        $statementMock->shouldReceive('execute');

        $statementMock
            ->shouldReceive('fetch')
            ->andReturn(
                array(
                    'id' => 42,
                    'username' => 'simon',
                    'email' => 'nobody@example.com',
                    'password' => 'some hash'
                )
            );

        $dbMock = \Mockery::mock();
        $dbMock
            ->shouldReceive('prepare')
            ->andReturn($statementMock);

        $app['db'] = $dbMock;

        $UserModel = new \Gitrepos\Models\UserModel($app);
        $return = $UserModel->authenticate('user_credential', 'pwd');
        $this->assertEquals(
            array(
                'id' => 42,
                'username' => 'simon',
                'email' => 'nobody@example.com'
            ),
            $return
        );
    }

    public function test_authenticate_returns_false_on_bad_password()
    {
        $app = new \Silex\Application();
        $PasswordsMock = \Mockery::mock('\Gitrepos\Passwords');
        $PasswordsMock
            ->shouldReceive('password_verify')
            ->andReturn(false);
        $app['passwords'] = $PasswordsMock;

        $statementMock = \Mockery::mock();
        $statementMock->shouldReceive('bindValue');
        $statementMock->shouldReceive('execute');

        $statementMock
            ->shouldReceive('fetch')
            ->andReturn(
                array(
                    'id' => 42,
                    'username' => 'simon',
                    'email' => 'nobody@example.com',
                    'password' => 'some hash'
                )
            );

        $dbMock = \Mockery::mock();
        $dbMock
            ->shouldReceive('prepare')
            ->andReturn($statementMock);

        $app['db'] = $dbMock;

        $UserModel = new \Gitrepos\Models\UserModel($app);
        $return = $UserModel->authenticate('user_credential', 'pwd');
        $this->assertFalse($return);
    }

    public function test_authenticate_returns_false_on_bad_user()
    {
        $app = new \Silex\Application();
        $statementMock = \Mockery::mock();
        $statementMock->shouldReceive('bindValue');
        $statementMock->shouldReceive('execute');

        $statementMock
            ->shouldReceive('fetch')
            ->andReturn(
                false
            );

        $dbMock = \Mockery::mock();
        $dbMock
            ->shouldReceive('prepare')
            ->andReturn($statementMock);

        $app['db'] = $dbMock;

        $UserModel = new \Gitrepos\Models\UserModel($app);
        $return = $UserModel->authenticate('user_credential', 'pwd');
        $this->assertFalse($return);
    }
}
