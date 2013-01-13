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
                    'value' => 'key_value',
                    'user_id' => '1'
                )
            )
            ->once();

        $app['db'] = $dbMock;

        $Key = new \Gitrepos\Entities\Key(array(
            'title' => 'key_title',
            'value' => 'key_value',
            'user_id' => '1'
        ));

        $KeyModel = new \Gitrepos\Models\KeyModel($app);
        $KeyModel->add($Key);
    }

    public function test_enumerate_retrieves_the_user_keys()
    {
        $app = new \Silex\Application();

        $statementMock = \Mockery::mock();
        $statementMock
            ->shouldReceive('bindValue')
            ->with('user_id', 42)
            ->once();

        $statementMock
            ->shouldReceive('execute')
            ->once();

        $statementMock
            ->shouldReceive('fetchAll')
            ->once()
            ->andReturn(array('key1', 'key2'));

        $dbMock = \Mockery::mock();
        $dbMock
            ->shouldReceive('prepare')
            ->with('SELECT * FROM keys WHERE user_id = :user_id')
            ->once()
            ->andReturn($statementMock);

        $app['db'] = $dbMock;
        $KeyModel = new \Gitrepos\Models\KeyModel($app);
        $this->assertEquals(array('key1', 'key2'), $KeyModel->enumerate(42));
    }
}
