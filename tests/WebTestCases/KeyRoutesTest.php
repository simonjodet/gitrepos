<?php
namespace Tests\WebTestCases;

class KeyRoutesTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../../src/bootstrap.php';
        $app['debug'] = true;
        $app['session.test'] = true;
        $firewalls = $app['security.firewalls'];
        $firewalls['user_firewall']['users'] = array(
            'admin' => array(
                'ROLE_USER',
                '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='
            )
        );
        $app['security.firewalls'] = $firewalls;

        unset($app['exception_handler']);
        return $app;
    }

    public function test_key_creation_route_exists()
    {
        $client = $this->authenticateUser();
        $client->request('GET', '/key/add');
        $this->assertTrue($client->getResponse()->isOk());
    }

}