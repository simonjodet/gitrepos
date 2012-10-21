<?php

namespace Gitrepos\Exceptions;

class BadConfigurationJSON extends \Exception
{
    public function __construct()
    {
        $possible_errors = array(
            JSON_ERROR_DEPTH => 'JSON_ERROR_DEPTH',
            JSON_ERROR_STATE_MISMATCH => 'JSON_ERROR_STATE_MISMATCH',
            JSON_ERROR_CTRL_CHAR => 'JSON_ERROR_CTRL_CHAR',
            JSON_ERROR_SYNTAX => 'JSON_ERROR_SYNTAX',
            JSON_ERROR_UTF8 => 'JSON_ERROR_UTF8'
        );
        $this->message = 'JSON parser returned with error "' . $possible_errors[json_last_error()] . '"';
    }
}