<?php
namespace Gitrepos;

class Configuration
{
    private $conf;

    public function __construct($file)
    {
        if (!file_exists($file)) {
            throw new \Gitrepos\Exceptions\MissingConfigurationFile($file);
        }

        $this->conf = json_decode(file_get_contents($file), true);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Gitrepos\Exceptions\BadConfigurationJSON();
        }

        if (!isset($this->conf['default_env'])) {
            throw new \Gitrepos\Exceptions\MissingConfigurationEnvironment();
        }
    }

    public function get($env = null)
    {
        if (is_null($env)) {
            $env = $this->conf['default_env'];
        }
        if (!isset($this->conf[$env])) {
            throw new \Gitrepos\Exceptions\BadConfiguration('The configuration is missing "' . $env . '" environment');
        }
        return $this->conf[$env];
    }
}