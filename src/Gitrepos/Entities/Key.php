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
            throw new \Gitrepos\Exceptions\MissingKeyTitle();
        }
        if (empty($data['title'])) {
            throw new \Gitrepos\Exceptions\EmptyKeyTitle();
        }
        if (strlen($data['title']) > 128) {
            throw new \Gitrepos\Exceptions\KeyTitleTooLong();
        }
        if (!isset($data['value'])) {
            throw new \Gitrepos\Exceptions\MissingKeyValue();
        }
        if (empty($data['value'])) {
            throw new \Gitrepos\Exceptions\EmptyKeyValue();
        }
        if (strlen($data['value']) > 512) {
            throw new \Gitrepos\Exceptions\KeyValueTooLong();
        }
        $this->title = $data['title'];
        $this->value = $data['value'];
    }
}