<?php

namespace Gitrepos\Models;

class KeyModel
{
    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    public function add(\Gitrepos\Entities\Key $Key)
    {
            $this->app['db']->insert(
                'keys',
                array(
                    'title' => $Key->getTitle(),
                    'value' => $Key->getValue()
                )
            );
    }
}