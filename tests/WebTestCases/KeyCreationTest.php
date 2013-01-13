<?php
namespace Tests\WebTestCases;

class KeyCreationTest extends WebTestCase
{

    /**
     * @var \Symfony\Component\HttpKernel\Client
     */
    private $client;
    private $crawler;
    private $buttonCrawlerNode;

    public function createApplication()
    {
        $app = require __DIR__ . '/../../src/bootstrap.php';
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

    private function loginUser()
    {
        $form = $this->buttonCrawlerNode->form(
            array(
                'form[username]' => 'username',
                'form[email]' => 'mail@domain.com',
                'form[password]' => 'pa$$word',
                'form[password2]' => 'pa$$word',
            )
        );
        $this->client->submit($form);
        $this->client = $this->createClient();
        $crawler = $this->client->request('GET', '/login');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form(
            array(
                '_username' => 'username',
                '_password' => 'pa$$word',
            )
        );
        $this->client->followRedirects();
        $this->client->submit($form);
    }

    public function test_key_creation_form_is_complete()
    {
        $this->loginUser();
        $this->crawler = $this->client->request('GET', '/key/add');
        $this->assertEquals(1, $this->crawler->filter('input[name="form[title]"]')->count());
        $this->assertEquals(1, $this->crawler->filter('input[name="form[value]"]')->count());
    }
}