<?php
namespace Tests\WebTestCases;

class SigninTest extends WebTestCase
{

    /**
     * @var \Symfony\Component\HttpKernel\Client
     */
    private $client;
    private $crawler;
    private $buttonCrawlerNode;

    public function createApplication()
    {
        $app = require __DIR__ . '/../../src/gitrepos.php';
        $app['debug'] = true;
        $app['session.test'] = true;
        unset($app['exception_handler']);
        return $app;
    }

    public function setUp()
    {
        parent::setUp();
        $client = $this->createClient();
        $this->crawler = $client->request('GET', '/signin');
        $this->client = $client;
        $this->buttonCrawlerNode = $this->crawler->selectButton('submit');

        $Database = new \Gitrepos\Database($this->app);
        $Database->reset();
    }

    public function test_the_signin_form_contains_the_needed_fields()
    {
        $crawler = $this->crawler;

        $this->assertEquals(1, $crawler->filter('input[name="form[username]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="form[email]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="form[password]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="form[password2]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="submit"]')->count());
    }

    private function getErrorMessage()
    {
        return $this->client->getCrawler()->filter('li')->first()->text();
    }

    public function test_the_signin_form_does_not_allow_short_username()
    {
        //Minimum is 3 characters
        $form = $this->buttonCrawlerNode->form(array(
            'form[username]' => 'aa',
            'form[email]' => 'mail@domain.com',
            'form[password]' => 'pa$$word',
            'form[password2]' => 'pa$$word',
        ));
        $this->client->submit($form);
        $this->assertEquals('This value is too short. It should have 3 characters or more.', $this->getErrorMessage());
    }

    public function test_the_signin_form_does_not_allow_long_username()
    {
        //Maximum is 64 characters
        $form = $this->buttonCrawlerNode->form(array(
            'form[username]' => 'abcde012345678901234567890123456789012345678901234567890123456789',
            'form[email]' => 'mail@domain.com',
            'form[password]' => 'pa$$word',
            'form[password2]' => 'pa$$word',
        ));
        $this->client->submit($form);
        $this->assertEquals('This value is too long. It should have 64 characters or less.', $this->getErrorMessage());
    }

    public function test_the_signin_form_does_not_allow_non_emails()
    {
        $form = $this->buttonCrawlerNode->form(array(
            'form[username]' => 'username',
            'form[email]' => 'maildomain.com',
            'form[password]' => 'pa$$word',
            'form[password2]' => 'pa$$word',
        ));
        $this->client->submit($form);
        $this->assertEquals('This value is not a valid email address.', $this->getErrorMessage());
    }

    public function test_the_signin_form_does_short_passwords()
    {
        //Minimum is 6 characters
        $form = $this->buttonCrawlerNode->form(array(
            'form[username]' => 'username',
            'form[email]' => 'mail@domain.com',
            'form[password]' => '12345',
            'form[password2]' => '12345',
        ));
        $this->client->submit($form);
        $this->assertEquals('This value is too short. It should have 6 characters or more.', $this->getErrorMessage());
    }

    public function test_the_signin_form_does_long_passwords()
    {
        //Maximum is 128 characters
        $form = $this->buttonCrawlerNode->form(array(
            'form[username]' => 'username',
            'form[email]' => 'mail@domain.com',
            'form[password]' => 'abcdefghi012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789',
            'form[password2]' => 'abcdefghi012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789',
        ));
        $this->client->submit($form);
        $this->assertEquals('This value is too long. It should have 128 characters or less.', $this->getErrorMessage());
    }

    public function test_the_signin_form_does_different_passwords()
    {
        $form = $this->buttonCrawlerNode->form(array(
            'form[username]' => 'username',
            'form[email]' => 'mail@domain.com',
            'form[password]' => '123456',
            'form[password2]' => 'abcdef',
        ));
        $this->client->submit($form);
        $this->assertEquals('The two password fields don\'t match.', $this->getErrorMessage());
    }


    public function test_successful_signin_allow_login()
    {
        $form = $this->buttonCrawlerNode->form(array(
            'form[username]' => 'username',
            'form[email]' => 'mail@domain.com',
            'form[password]' => 'pa$$word',
            'form[password2]' => 'pa$$word',
        ));
        $this->client->submit($form);

        $client = $this->createClient();
        $crawler = $client->request('GET', '/login');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form(array(
            '_username' => 'username',
            '_password' => 'pa$$word',
        ));
        $client->submit($form);

        $this->assertEquals('http://localhost/', $client->getResponse()->getTargetUrl());
    }
}