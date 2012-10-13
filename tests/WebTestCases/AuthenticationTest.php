<?php
namespace Tests\WebTestCases;

class AuthenticationTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../../src/gitrepos.php';
        $app['debug'] = true;
        $app['session.test'] = true;
        unset($app['exception_handler']);
        return $app;
    }

    public function test_user_domain_is_not_accessible_by_non_users()
    {
        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}