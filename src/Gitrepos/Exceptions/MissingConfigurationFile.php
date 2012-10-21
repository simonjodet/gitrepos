<?php

namespace Gitrepos\Exceptions;

class MissingConfigurationFile extends \Exception
{
    public function __construct($file)
    {
        $this->message = 'Could not find "' . $file . '"';
    }
}