<?php

namespace Gitrepos\Models;

class ModelFactory
{
    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    public function get($model)
    {
        $classname = '\Gitrepos\Models\\' . $model . 'Model';
        return new $classname($this->app);
    }
}