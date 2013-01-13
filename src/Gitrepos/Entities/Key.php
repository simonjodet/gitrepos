<?php

namespace Gitrepos\Entities;

class Key
{
    private $title;
    private $value;

    public function getTitle()
    {
        return $this->title;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __construct($data = array())
    {
        if (!isset($data['title'])) {
            throw new \Gitrepos\Exceptions\InvalidKey('Missing key title');
        }
        if (empty($data['title'])) {
            throw new \Gitrepos\Exceptions\InvalidKey('Empty key title');
        }
        if (strlen($data['title']) > 128) {
            throw new \Gitrepos\Exceptions\InvalidKey('Key title too long');
        }
        if (!isset($data['value'])) {
            throw new \Gitrepos\Exceptions\InvalidKey('Missing key value');
        }
        if (empty($data['value'])) {
            throw new \Gitrepos\Exceptions\InvalidKey('Empty key value');
        }
        if (strlen($data['value']) > 512) {
            throw new \Gitrepos\Exceptions\InvalidKey('Key value too long');
        }
        $this->title = $data['title'];
        $this->value = $data['value'];
    }
}