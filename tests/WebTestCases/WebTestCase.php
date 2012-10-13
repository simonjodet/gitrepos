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
}