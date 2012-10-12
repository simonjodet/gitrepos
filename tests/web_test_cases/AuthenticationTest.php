<?php
use \Silex\WebTestCase;

class AuthenticationTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../../src/gitrepos.php';
        $app['debug'] = true;
        unset($app['exception_handler']);
        $firewalls = $app['security.firewalls'];
        $firewalls['user_firewall']['users'] = array(
            // raw password is foo
            'user' => array('ROLE_USER', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
        );
        $app['security.firewalls'] = $firewalls;

        return $app;
    }

    public function test_user_domain_is_not_accessible_by_non_users()
    {
        $client = $this->createClient();
        $client->request('GET', '/username/');

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function test_user_domain_is_accessible_by_users()
    {
        $client = $this->createClient(array(
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW' => 'foo',
        ));
        $client->request('GET', '/username/');

        $this->assertTrue($client->getResponse()->isOk());
    }
}