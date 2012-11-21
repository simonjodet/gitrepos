<?php

namespace Tests\UnitTests;

class UserControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mockery\MockInterface
     */
    private $requestMock;

    protected function setUp()
    {
        $this->requestMock = \Mockery::mock('\Symfony\Component\HttpFoundation\Request');
    }

    public function test_loginAction_returns_the_correct_template()
    {
        $twigMock = \Mockery::mock();
        $twigMock
            ->shouldReceive('render')
            ->once()
            ->with('login.twig', \Mockery::any())
            ->andReturn('template');

        $app = new \Silex\Application();
        $app['twig'] = $twigMock;
        $app['security.last_error'] = $app->protect(function ()
        {
            return null;
        });
        $app['session'] = \Mockery::mock(array('get' => null));

        $UserController = new \Gitrepos\Controllers\UserController();
        $this->assertEquals('template', $UserController->loginAction($this->requestMock, $app));
    }

    private function validate_signing_form()
    {
        $app = new \Silex\Application();

        $formFactoryMock = \Mockery::mock('\Symfony\Component\Form\FormFactory');
        $formFactoryMock
            ->shouldReceive('createBuilder->add')
            ->with('username',
            'text',
            array(
                'constraints' => array(
                    new \Symfony\Component\Validator\Constraints\MinLength(3),
                    new \Symfony\Component\Validator\Constraints\MaxLength(64)
                )
            ))
            ->once()
            ->andReturn($formFactoryMock);
        $formFactoryMock
            ->shouldReceive('createBuilder->add')
            ->with('email',
            'text',
            array(
                'constraints' => array(
                    new \Symfony\Component\Validator\Constraints\Email()
                )
            ))
            ->once()
            ->andReturn($formFactoryMock);
        $formFactoryMock
            ->shouldReceive('createBuilder->add')
            ->with('password',
            'password',
            array(
                'always_empty' => false,
                'constraints' => array(
                    new \Symfony\Component\Validator\Constraints\MinLength(6),
                    new \Symfony\Component\Validator\Constraints\MaxLength(128)
                )
            ))
            ->once()
            ->andReturn($formFactoryMock);
        $formFactoryMock
            ->shouldReceive('createBuilder->add')
            ->with('password2',
            'password',
            array(
                'label' => 'Retype password',
                'always_empty' => false
            ))
            ->once()
            ->andReturn($formFactoryMock);

        $app['form.factory'] = $formFactoryMock;
        return $app;
    }

    public function test_signingAction_creates_the_correct_form()
    {
        $app = $this->validate_signing_form();

        $app['form.factory']
            ->shouldReceive('getForm')
            ->once()
            ->andReturn(\Mockery::mock(array('createView' => 'form->createView', 'bind' => null)));


        $this->requestMock
            ->shouldReceive('getMethod');
        $app['request'] = $this->requestMock;

        $twigMock = \Mockery::mock();
        $twigMock
            ->shouldReceive('render')
            ->once()
            ->with(
            'signin.twig', array('form' => 'form->createView'))
            ->andReturn('template');
        $app['twig'] = $twigMock;


        $UserController = new \Gitrepos\Controllers\UserController();
        $this->assertEquals('template', $UserController->signinAction($this->requestMock, $app));
    }

    public function test_signingAction_validates_valid_submitted_form()
    {
        $app = $this->validate_signing_form();

        $FormMock = \Mockery::mock(array('createView' => 'form->createView', 'bind' => null, 'isValid' => true));

        $FormMock
            ->shouldReceive('getData')
            ->once()
            ->andReturn(array(
            'password' => 'my_password',
            'password2' => 'my_password'
        ));

        $app['form.factory']
            ->shouldReceive('getForm')
            ->once()
            ->andReturn($FormMock);

        $this->requestMock
            ->shouldReceive('getMethod')
            ->andReturn('POST');
        $app['request'] = $this->requestMock;

        $UserModelMock = \Mockery::mock();
        $UserModelMock
            ->shouldReceive('create')
            ->once()
            ->with(\Mockery::type('\Gitrepos\User'))
            ->andReturn(\Mockery::mock(array('getPassword' => null)));

        $SecurityMock = \Mockery::mock();
        $SecurityMock
            ->shouldReceive('setToken')
            ->once()
            ->with(\Mockery::type('\Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken'));

        $app['security'] = $SecurityMock;

        $ModelFactoryMock = \Mockery::mock();
        $ModelFactoryMock
            ->shouldReceive('get')
            ->once()
            ->with('User')
            ->andReturn($UserModelMock);

        $app['model.factory'] = $ModelFactoryMock;

        $UserController = new \Gitrepos\Controllers\UserController();
        $redirect = $UserController->signinAction($this->requestMock, $app);
        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\RedirectResponse', $redirect);
        $this->assertEquals('/', $redirect->getTargetUrl());
    }

    public function test_signingAction_discards_form_if_password_fields_do_not_match()
    {
        $app = $this->validate_signing_form();

        $FormMock = \Mockery::mock(array('createView' => 'form->createView', 'bind' => null, 'isValid' => false));

        $FormMock
            ->shouldReceive('getData')
            ->once()
            ->andReturn(array(
            'password' => 'my_password',
            'password2' => 'not-the_same_password'
        ));


        $FieldMock = \Mockery::mock();
        $FieldMock
            ->shouldReceive('addError')
            ->once()
            ->with(\Mockery::on(
            function($FormError)
            {
                return $FormError instanceof \Symfony\Component\Form\FormError && $FormError->getMessage() == 'The two password fields don\'t match.';
            }
        ));

        $FormMock
            ->shouldReceive('get')
            ->once()
            ->with('password2')
            ->andReturn($FieldMock);

        $app['form.factory']
            ->shouldReceive('getForm')
            ->once()
            ->andReturn($FormMock);

        $this->requestMock
            ->shouldReceive('getMethod')
            ->andReturn('POST');
        $app['request'] = $this->requestMock;
        $twigMock = \Mockery::mock();
        $twigMock
            ->shouldReceive('render')
            ->once()
            ->with('signin.twig', \Mockery::any())
            ->andReturn('template');

        $app['twig'] = $twigMock;


        $UserController = new \Gitrepos\Controllers\UserController();
        $this->assertEquals('template', $UserController->signinAction($this->requestMock, $app));
    }

    public function test_signingAction_returns_correct_error_for_duplicate_username()
    {
        $app = $this->validate_signing_form();

        $FormMock = \Mockery::mock(array('createView' => 'form->createView', 'bind' => null, 'isValid' => true));

        $FormMock
            ->shouldReceive('getData')
            ->once()
            ->andReturn(array(
            'password' => 'my_password',
            'password2' => 'my_password'
        ));

        $FieldMock = \Mockery::mock();
        $FieldMock
            ->shouldReceive('addError')
            ->once()
            ->with(\Mockery::on(
            function($FormError)
            {
                return $FormError instanceof \Symfony\Component\Form\FormError && $FormError->getMessage() == 'This username is already used.';
            }
        ));

        $FormMock
            ->shouldReceive('get')
            ->once()
            ->with('username')
            ->andReturn($FieldMock);

        $app['form.factory']
            ->shouldReceive('getForm')
            ->once()
            ->andReturn($FormMock);

        $this->requestMock
            ->shouldReceive('getMethod')
            ->andReturn('POST');
        $app['request'] = $this->requestMock;

        $UserModelMock = \Mockery::mock();
        $UserModelMock
            ->shouldReceive('create')
            ->once()
            ->with(\Mockery::type('\Gitrepos\User'))
            ->andThrow(new \Gitrepos\Exceptions\DuplicateUsername);

        $ModelFactoryMock = \Mockery::mock();
        $ModelFactoryMock
            ->shouldReceive('get')
            ->once()
            ->with('User')
            ->andReturn($UserModelMock);
        $app['model.factory'] = $ModelFactoryMock;


        $twigMock = \Mockery::mock();
        $twigMock
            ->shouldReceive('render')
            ->once()
            ->with('signin.twig', \Mockery::any())
            ->andReturn('template');

        $app['twig'] = $twigMock;


        $UserController = new \Gitrepos\Controllers\UserController();
        $this->assertEquals('template', $UserController->signinAction($this->requestMock, $app));
    }

    public function test_signingAction_returns_correct_error_for_duplicate_email()
    {
        $app = $this->validate_signing_form();

        $FormMock = \Mockery::mock(array('createView' => 'form->createView', 'bind' => null, 'isValid' => true));

        $FormMock
            ->shouldReceive('getData')
            ->once()
            ->andReturn(array(
            'password' => 'my_password',
            'password2' => 'my_password'
        ));

        $FieldMock = \Mockery::mock();
        $FieldMock
            ->shouldReceive('addError')
            ->once()
            ->with(\Mockery::on(
            function($FormError)
            {
                return $FormError instanceof \Symfony\Component\Form\FormError && $FormError->getMessage() == 'This email address is already used.';
            }
        ));

        $FormMock
            ->shouldReceive('get')
            ->once()
            ->with('email')
            ->andReturn($FieldMock);

        $app['form.factory']
            ->shouldReceive('getForm')
            ->once()
            ->andReturn($FormMock);

        $this->requestMock
            ->shouldReceive('getMethod')
            ->andReturn('POST');
        $app['request'] = $this->requestMock;

        $UserModelMock = \Mockery::mock();
        $UserModelMock
            ->shouldReceive('create')
            ->once()
            ->with(\Mockery::type('\Gitrepos\User'))
            ->andThrow(new \Gitrepos\Exceptions\DuplicateEmail);

        $ModelFactoryMock = \Mockery::mock();
        $ModelFactoryMock
            ->shouldReceive('get')
            ->once()
            ->with('User')
            ->andReturn($UserModelMock);
        $app['model.factory'] = $ModelFactoryMock;


        $twigMock = \Mockery::mock();
        $twigMock
            ->shouldReceive('render')
            ->once()
            ->with('signin.twig', \Mockery::any())
            ->andReturn('template');

        $app['twig'] = $twigMock;


        $UserController = new \Gitrepos\Controllers\UserController();
        $this->assertEquals('template', $UserController->signinAction($this->requestMock, $app));
    }
}