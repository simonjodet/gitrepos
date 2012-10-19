<?php

namespace Gitrepos;

class Database
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $conn;

    public function __construct(\Silex\Application $app)
    {
        $this->conn = $app['db'];
    }

    public function getSchemas()
    {
        $Schema = new \Doctrine\DBAL\Schema\Schema();

        $schemas = array(
            1 => function() use($Schema)
            {
                $systemTable = $Schema->createTable('system');
                $systemTable->addColumn('system_key', 'string', array('length' => 255));
                $systemTable->addColumn('value', 'string', array('length' => 255));
                $systemTable->addUniqueIndex(array('system_key'), 'unique_system_key');

                $usersTable = $Schema->createTable('users');
                $usersTable->addColumn('username', 'string', array('length' => 64));
                $usersTable->addColumn('email', 'string', array('length' => 255));
                $usersTable->addColumn('password', 'string', array('length' => 255));
                $usersTable->addUniqueIndex(array('username'), 'unique_users_nickname');
                $usersTable->addUniqueIndex(array('email'), 'unique_users_email');
                $usersTable->setPrimaryKey(array('username'));

                return $Schema;
            }
        );
        return $schemas;
    }

    public function getSchemaSql($version = null)
    {
        $schema_versions = $this->getSchemas();

        if (is_null($version))
        {
            $version = key(array_slice($schema_versions, -1, 1, TRUE));
        }
        if (!array_key_exists($version, $schema_versions))
        {
            throw new \Exception('Unknown schema version');
        }
        $schema = $schema_versions[$version]();

        $SchemaManager = $this->conn->getSchemaManager();

        if ($SchemaManager->tablesExist('system') && count($this->conn->fetchAll("SELECT * FROM system WHERE system_key = 'database_schema_version';")) > 0)
        {
            $database_schema_version_query = "UPDATE system SET value = " . $version . " WHERE system_key = 'database_schema_version'";
        }
        else
        {
            $database_schema_version_query = "INSERT INTO system (system_key, value) VALUES ('database_schema_version', " . $version . ")";
        }

        $platform = $this->conn->getDatabasePlatform();
        $queries = $schema->toSql($platform);
        $queries[] = $database_schema_version_query;
        return $queries;
    }

    public function listTables()
    {
        $tables = array();
        $SchemaManager = $this->conn->getSchemaManager();

        foreach ($SchemaManager->listTables() as $table)
        {
            $tables[] = $table->getName();
        }
        return $tables;
    }

    public function dropTable($table)
    {
        $this->conn->query("DROP TABLE IF EXISTS '" . $table . "';");
    }

    public function reset($version = null)
    {
        $tables = $this->listTables();
        foreach ($tables as $table)
        {
            $this->dropTable($table);
        }

        $schema = $this->getSchemaSql($version);
        foreach ($schema as $query)
        {
            $this->conn->query($query);
        }

    }
}