<?php
namespace Tests\WebTestCases;

class KeyRoutesTest extends WebTestCase
{
    public function test_key_creation_route_exists()
    {
        $client = $this->authenticateUser();
        $client->request('GET', '/key/add');
        $this->assertTrue($client->getResponse()->isOk());
    }

}