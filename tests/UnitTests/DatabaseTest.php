<?php

namespace Tests\UnitTests;

class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    public function test_getSchema_throws_error_on_unknown_version()
    {
        $this->setExpectedException('\Exception', 'Unknown schema version');

        $app = new \Silex\Application();
        $app['db'] = '';
        $Database = new \Gitrepos\Database($app);

        $Database->getSchemaSql(0);
    }

    public function test_getSchema_gives_the_latest_version_by_default()
    {
        $app = new \Silex\Application();

        $ConnectionMock = \Mockery::mock('\Doctrine\DBAL\Connection');
        $ConnectionMock
            ->shouldReceive('getSchemaManager->tablesExist');
        $ConnectionMock
            ->shouldReceive('getDatabasePlatform')
            ->andReturn(new \Doctrine\DBAL\Platforms\SqlitePlatform());
        $ConnectionMock
            ->shouldReceive('toSql');
        $app['db'] = $ConnectionMock;
        $Database = \Mockery::mock('\Gitrepos\Database[getSchemas]', array($app));
        $Database
            ->shouldReceive('getSchemas')
            ->once()
            ->andReturn(
            array(
                1 => function () {
                    $SchemaMock1 = \Mockery::mock();
                    $SchemaMock1
                        ->shouldReceive('toSql')
                        ->andReturn(array('SQL query 1'));
                    return $SchemaMock1;
                },
                2 => function () {
                    $SchemaMock2 = \Mockery::mock();
                    $SchemaMock2
                        ->shouldReceive('toSql')
                        ->once()
                        ->andReturn(array('SQL query 2'));
                    return $SchemaMock2;
                }

            )
        );

        $this->assertEquals(
            array(
                'SQL query 2',
                "INSERT INTO system (system_key, value) VALUES ('database_schema_version', 2)"
            ),
            $Database->getSchemaSql()
        );
    }

    public function test_getSchemas_return_an_array_of_callables()
    {
        $app = new \Silex\Application();
        $app['db'] = '';

        $Database = new \Gitrepos\Database($app);
        $schemas = $Database->getSchemas();

        $this->assertTrue(is_array($schemas));
        $this->assertTrue(count($schemas) > 0);

        foreach ($schemas as $schema) {
            if (!is_callable($schema)) {
                $this->fail('All schemas should be callable');
            }
        }
    }

    public function test_listTables_returns_tables_name()
    {
        $app = new \Silex\Application();

        $ConnectionMock = \Mockery::mock('\Doctrine\DBAL\Connection');

        $TableMock = \Mockery::mock('\Doctrine\DBAL\Schema\Table');
        $TableMock
            ->shouldReceive('getName')
            ->andReturn('system', 'users');

        $ConnectionMock
            ->shouldReceive('getSchemaManager->listTables')
            ->once()
            ->andReturn(
            array(
                $TableMock,
                $TableMock
            )
        );

        $app['db'] = $ConnectionMock;
        $Database = new \Gitrepos\Database($app);
        $this->assertEquals(
            array('system', 'users'),
            $Database->listTables()
        );
    }

    public function test_dropTable_execute_the_right_query()
    {
        $app = new \Silex\Application();

        $ConnectionMock = \Mockery::mock('\Doctrine\DBAL\Connection');
        $ConnectionMock
            ->shouldReceive('query')
            ->with("DROP TABLE IF EXISTS 'my_table';")
            ->once();
        $app['db'] = $ConnectionMock;

        $Database = new \Gitrepos\Database($app);

        $Database->dropTable('my_table');
    }

    public function test_reset_list_existing_tables_deletes_them_then_create_a_new_schema()
    {
        $app = new \Silex\Application();

        $ConnectionMock = \Mockery::mock('\Doctrine\DBAL\Connection');
        $ConnectionMock
            ->shouldReceive('query')
            ->with('SQL query 1')
            ->once()
            ->ordered('reset');
        $ConnectionMock
            ->shouldReceive('query')
            ->with('SQL query 2')
            ->once()
            ->ordered('reset');
        $app['db'] = $ConnectionMock;

        $Database = \Mockery::mock('\Gitrepos\Database[listTables,dropTable,getSchemaSql]', array($app));
        $Database
            ->shouldReceive('listTables')
            ->once()
            ->ordered('reset')
            ->andReturn(array('system', 'users'));
        $Database
            ->shouldReceive('dropTable')
            ->with('system')
            ->once()
            ->ordered('reset');
        $Database
            ->shouldReceive('dropTable')
            ->with('users')
            ->once()
            ->ordered('reset');
        $Database
            ->shouldReceive('getSchemaSql')
            ->with(1)
            ->once()
            ->ordered('reset')
            ->andReturn(array('SQL query 1', 'SQL query 2'));

        $Database->reset(1);
    }
}
