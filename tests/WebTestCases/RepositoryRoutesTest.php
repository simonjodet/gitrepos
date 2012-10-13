<?php
namespace Tests\WebTestCases;

class RepositoryRoutesTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../../src/gitrepos.php';
        $app['debug'] = true;
        $app['session.test'] = true;

        unset($app['exception_handler']);
        return $app;
    }

    public function test_repositories_list_route_exists()
    {
        $client = $this->authenticateUser();
        $client->request('GET', '/simon/');
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function test_repositories_create_form_route_exists()
    {
        $client = $this->authenticateUser();
        $client->request('GET', '/simon/add');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function test_repositories_create_route_exists()
    {
        $client = $this->authenticateUser();
        $client->request('POST', '/simon/create');

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

        $client = $this->authenticateUser();
        $client->request('GET', '/simon/create/');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function test_repository_edit_form_route_exists()
    {
        $client = $this->authenticateUser();
        $client->request('GET', '/simon/reponame/edit');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function test_repository_update_route_exists()
    {
        $client = $this->authenticateUser();
        $client->request('POST', '/simon/reponame/update');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function test_repository_deletion_route_exists()
    {
        $client = $this->authenticateUser();
        $client->request('POST', '/simon/reponame/delete');

        $this->assertTrue($client->getResponse()->isOk());
    }
}