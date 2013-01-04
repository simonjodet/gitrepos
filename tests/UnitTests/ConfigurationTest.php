<?php

namespace Tests\UnitTests;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{

    public function test_constructor_throws_exception_on_wrong_file_path()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\MissingConfigurationFile', 'Could not find "missing_file"');

        new \Gitrepos\Configuration('missing_file');
    }

    public function test_constructor_throws_exception_on_bad_configuration_syntax()
    {
        $this->setExpectedException(
            '\Gitrepos\Exceptions\BadConfigurationJSON',
            'JSON parser returned with error "JSON_ERROR_SYNTAX"'
        );

        file_put_contents(sys_get_temp_dir() . '/bad_conf.json', 'invalid JSON');

        new \Gitrepos\Configuration(sys_get_temp_dir() . '/bad_conf.json');
    }

    public function test_constructor_throws_exception_on_missing_configuration_environment()
    {
        $this->setExpectedException(
            '\Gitrepos\Exceptions\MissingConfigurationEnvironment',
            'The configuration file is missing a "default_env" property'
        );

        file_put_contents(sys_get_temp_dir() . '/missing_env_prop.json', '{"conf1":"value1"}');

        new \Gitrepos\Configuration(sys_get_temp_dir() . '/missing_env_prop.json');
    }

    public function test_get_throws_exception_on_missing_env_property()
    {
        $this->setExpectedException(
            '\Gitrepos\Exceptions\BadConfiguration',
            'The configuration is missing "my_env" environment'
        );

        file_put_contents(sys_get_temp_dir() . '/missing_env.json', '{"default_env":"my_env"}');

        $Configuration = new \Gitrepos\Configuration(sys_get_temp_dir() . '/missing_env.json');
        $Configuration->get();
    }

    public function test_get_returns_default_env_conf()
    {
        file_put_contents(
            sys_get_temp_dir() . '/default_env.json',
            '{"default_env":"my_env","my_env":{"conf1":"value1"}}'
        );
        $Configuration = new \Gitrepos\Configuration(sys_get_temp_dir() . '/default_env.json');

        $this->assertEquals(array('conf1' => 'value1'), $Configuration->get());
    }

    public function test_get_returns_requested_env_conf()
    {
        file_put_contents(
            sys_get_temp_dir() . '/default_env.json',
            '{"default_env":"my_env","my_env":{"conf1":"value1"},"prod":{"conf2":"value2"}}'
        );
        $Configuration = new \Gitrepos\Configuration(sys_get_temp_dir() . '/default_env.json');

        $this->assertEquals(array('conf2' => 'value2'), $Configuration->get('prod'));
    }
}
