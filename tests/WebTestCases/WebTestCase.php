<?php
namespace Tests\WebTestCases;

abstract class WebTestCase extends \Silex\WebTestCase
{
    protected function authenticateUser()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'foo';

        $client->submit($form);
        return $client;
    }

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
}