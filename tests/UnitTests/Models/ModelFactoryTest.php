<?php

namespace Tests\UnitTests;

class ModelFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_get_returns_correct_model_instance()
    {
        $app = new \Silex\Application();
        $ModelFactory = new \Gitrepos\Models\ModelFactory($app);
        $this->assertInstanceOf('\Gitrepos\Models\UserModel', $ModelFactory->get('User'));
    }
}