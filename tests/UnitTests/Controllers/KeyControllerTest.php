<?php

namespace Tests\UnitTests;

class KeyControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mockery\MockInterface
     */
    private $requestMock;

    protected function setUp()
    {
        parent::setUp();
        $this->requestMock = \Mockery::mock('\Symfony\Component\HttpFoundation\Request');
    }

    private function get_KeyController_with_mocked_buildAddForm($app, $FormMock = null)
    {
        if (is_null($FormMock)) {
            $FormMock = \Mockery::mock(array('createView' => 'form->createView', 'bind' => null));
        }
        $KeyController = \Mockery::mock('\Gitrepos\Controllers\KeyController[buildAddForm]', array($app));
        $KeyController
            ->shouldReceive('buildAddForm')
            ->once()
            ->andReturn($FormMock);
        return $KeyController;
    }

    public function test_buildAddForm_returns_the_correct_form()
    {
        $app = new \Silex\Application();

        $formFactoryMock = \Mockery::mock('\Symfony\Component\Form\FormFactory');
        $formFactoryMock
            ->shouldReceive('createBuilder->add')
            ->with(
                'title',
                'text',
                array(
                    'constraints' => array(
                        new \Symfony\Component\Validator\Constraints\MinLength(1),
                        new \Symfony\Component\Validator\Constraints\MaxLength(128)
                    )
                )
            )
            ->once()
            ->andReturn($formFactoryMock);
        $formFactoryMock
            ->shouldReceive('createBuilder->add')
            ->with(
                'key',
                'text',
                array(
                    'constraints' => array(
                        new \Symfony\Component\Validator\Constraints\MinLength(1),
                        new \Symfony\Component\Validator\Constraints\MaxLength(512)
                    )
                )
            )
            ->once()
            ->andReturn($formFactoryMock);
        $app['form.factory'] = $formFactoryMock;
        $app['form.factory']
            ->shouldReceive('getForm')
            ->once()
            ->andReturn('\Symfony\Component\Form\Form');

        $KeyController = new \Gitrepos\Controllers\KeyController();
        $this->assertEquals('\Symfony\Component\Form\Form', $KeyController->buildAddForm($app));
    }

    public function test_addAction_does_not_create_key_if_HTTP_method_is_GET()
    {
        $app = new \Silex\Application();
        $this->requestMock
            ->shouldReceive('getMethod')
            ->andReturn('GET')
            ->once();

        $app['request'] = $this->requestMock;

        $ModelFactoryMock = \Mockery::mock();
        $ModelFactoryMock
            ->shouldReceive('get')
            ->never();

        $app['model.factory'] = $ModelFactoryMock;

        $twigMock = \Mockery::mock();
        $twigMock
            ->shouldReceive('render')
            ->once()
            ->with(
                'key/add.twig',
                array('form' => 'form->createView')
            )
            ->andReturn('template');
        $app['twig'] = $twigMock;

        $KeyController = $this->get_KeyController_with_mocked_buildAddForm($app);
        $KeyController->addAction($this->requestMock, $app);
    }

    public function test_addAction_does_not_create_key_if_form_is_not_valid()
    {
        $app = new \Silex\Application();
        $this->requestMock
            ->shouldReceive('getMethod')
            ->andReturn('POST')
            ->once();

        $app['request'] = $this->requestMock;

        $FormMock = \Mockery::mock(array('createView' => 'form->createView', 'bind' => null, 'isValid' => false));


        $ModelFactoryMock = \Mockery::mock();
        $ModelFactoryMock
            ->shouldReceive('get')
            ->never();

        $app['model.factory'] = $ModelFactoryMock;

        $twigMock = \Mockery::mock();
        $twigMock
            ->shouldReceive('render')
            ->once()
            ->with(
                'key/add.twig',
                array('form' => 'form->createView')
            )
            ->andReturn('template');
        $app['twig'] = $twigMock;

        $KeyController = $this->get_KeyController_with_mocked_buildAddForm($app, $FormMock);
        $KeyController->addAction($this->requestMock, $app);
    }

    public function test_addAction_calls_KeyModel_add_with_correct_params()
    {
        $app = new \Silex\Application();
        $this->requestMock
            ->shouldReceive('getMethod')
            ->andReturn('POST')
            ->once();

        $app['request'] = $this->requestMock;
        $FormMock = \Mockery::mock(array('createView' => 'form->createView', 'bind' => null, 'isValid' => true));
        $FormMock
            ->shouldReceive('getData')
            ->once()
            ->andReturn(
                array(
                    'title' => 'key_title',
                    'value' => 'key_value'
                )
            );

        $KeyModelMock = \Mockery::mock('\Gitrepos\KeyModel');
        $KeyModelMock
            ->shouldReceive('add')
            ->with(
                \Mockery::on(
                    function ($arg) {
                        $Key = new \Gitrepos\Entities\Key(array(
                            'title' => 'key_title',
                            'value' => 'key_value'
                        ));

                        return $arg == $Key;
                    }
                )
            )
            ->once();

        $ModelFactoryMock = \Mockery::mock();
        $ModelFactoryMock
            ->shouldReceive('get')
            ->with('Key')
            ->once()
            ->andReturn($KeyModelMock);

        $app['model.factory'] = $ModelFactoryMock;

        $twigMock = \Mockery::mock();
        $twigMock
            ->shouldReceive('render')
            ->once()
            ->with(
                'key/add.twig',
                array('form' => 'form->createView')
            )
            ->andReturn('template');
        $app['twig'] = $twigMock;

        $KeyController = $this->get_KeyController_with_mocked_buildAddForm($app, $FormMock);
        $KeyController->addAction($this->requestMock, $app);
    }
}