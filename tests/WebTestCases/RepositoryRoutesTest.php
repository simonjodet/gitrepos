<?php
namespace Tests\WebTestCases;

class RepositoryRoutesTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../../src/gitrepos.php';
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

    public function test_repositories_list_route_exists()
    {
        $client = $this->authenticateUser();
        $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function test_repositories_get_add_route_exists()
    {
        $client = $this->authenticateUser();
        $client->request('GET', '/add');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function test_repositories_post_add_route_exists()
    {
        $client = $this->authenticateUser();
        $client->request('POST', '/add');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function test_repository_details_route_exists()
    {
        $client = $this->authenticateUser();
        $client->request('GET', '/simon/reponame/');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function test_naming_a_repository_with_repositories_actions_works()
    {
        $client = $this->authenticateUser();
        $client->request('GET', '/simon/add/');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function test_repository_get_edit_route_exists()
    {
        $client = $this->authenticateUser();
        $client->request('GET', '/simon/reponame/edit');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function test_repository_post_edit_route_exists()
    {
        $client = $this->authenticateUser();
        $client->request('POST', '/simon/reponame/edit');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function test_repository_deletion_route_exists()
    {
        $client = $this->authenticateUser();
        $client->request('POST', '/simon/reponame/delete');

        $this->assertTrue($client->getResponse()->isOk());
    }
}