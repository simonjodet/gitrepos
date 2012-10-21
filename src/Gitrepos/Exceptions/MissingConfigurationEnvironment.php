<?php

namespace Gitrepos\Exceptions;

class MissingConfigurationEnvironment extends \Exception
{
    public function __construct()
    {
        $this->message = 'The configuration file is missing a "default_env" property';
    }
}