<?php
namespace Tests\UnitTests;

class KeyModelTest extends \PHPUnit_Framework_TestCase
{
    public function test_add_expects_a_key_object()
    {
        $this->setExpectedException('\Exception');

        $AppMock = \Mockery::mock('\Silex\Application');

        $KeyModel = new \Gitrepos\Models\KeyModel($AppMock);

        $KeyModel->add('Not a Key object');
    }

    public function test_add_method_inserts_the_correct_data_in_db()
    {
        $app = new \Silex\Application();

        $dbMock = \Mockery::mock();
        $dbMock
            ->shouldReceive('insert')
            ->with(
            'keys',
            array(
                'title' => 'key_title',
                'value' => 'key_value'
            )
        )
            ->once();

        $app['db'] = $dbMock;

        $Key = new \Gitrepos\Entities\Key();
        $Key->title = 'key_title';
        $Key->value = 'key_value';

        $KeyModel = new \Gitrepos\Models\KeyModel($app);
        $KeyModel->add($Key);

    }
}
