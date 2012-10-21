<?php

namespace Gitrepos\Exceptions;

class BadConfiguration extends \Exception
{
    public function __construct($message)
    {
        $this->message = $message;
    }
}