<?php
namespace Tests\WebTestCases;

class AuthenticationTest extends WebTestCase
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

    private function createUser()
    {
        $form = $this->buttonCrawlerNode->form(array(
            'form[username]' => 'username',
            'form[email]' => 'mail@domain.com',
            'form[password]' => 'pa$$word',
            'form[password2]' => 'pa$$word',
        ));
        $this->client->submit($form);
    }

    public function test_user_domain_is_not_accessible_by_non_users()
    {
        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function test_successful_login_redirects_to_root_route()
    {
        $this->createUser();
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

    public function test_bad_login_display_error_to_user()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/login');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $client->followRedirects();
        $client->submit($form);
        $this->assertEquals('Bad credentials', trim($client->getCrawler()->filter('form')->first()->text()));
    }
}