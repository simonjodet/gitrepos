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
                'value' => $Key->getValue(),
                'user_id' => $Key->getUserId()
            )
        );
    }

    public function enumerate($userId)
    {
        $sql = 'SELECT * FROM keys WHERE user_id = :user_id';
        $stmt = $this->app['db']->prepare($sql);
        $stmt->bindValue('user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}