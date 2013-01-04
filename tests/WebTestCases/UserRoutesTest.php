<?php
use \Silex\WebTestCase;

class UserRoutesTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../../src/gitrepos.php';
        $app['debug'] = true;
        $app['session.test'] = true;

        unset($app['exception_handler']);

        return $app;
    }

    public function test_login_route_exists()
    {
        $client = $this->createClient();
        $client->request('GET', '/login');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function test_get_register_route_exists()
    {
        $client = $this->createClient();
        $client->request('GET', '/signin');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function test_post_register_route_exists()
    {
        $client = $this->createClient();
        $client->request('POST', '/signin');

        $this->assertTrue($client->getResponse()->isOk());
    }
}