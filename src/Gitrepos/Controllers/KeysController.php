<?php

namespace Gitrepos\Controllers;

class KeysController extends Controller
{
    public function postAction()
    {
        session_name('SESSION');
        try {
            session_start();
        } catch (\Exception $e) {
        }
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != '') {
            $Key = new \Gitrepos\Entities\Key(
                array(
                    'title' => $this->app['request.body_params']['title'],
                    'value' => $this->app['request.body_params']['value'],
                    'user_id' => $_SESSION['user_id']
                )
            );
            /**
             * @var $KeyModel \Gitrepos\Models\KeyModel
             */
            $KeyModel = $this->app['model.factory']->get('Key');
            $KeyModel->add($Key);

            $response = $this->app->json(
                array(
                    'id' => 42,
                    'title' => $this->app['request.body_params']['title'],
                    'value' => $this->app['request.body_params']['value']
                ),
                201
            );
        } else {
            $error_body =
                array(
                    'code' => 401,
                    'message' => 'Requires authentication',
                    'doc' => '/docs/keys.json'
                );
            $response = $this->app->json($error_body, 401);
        }
        return $response;
    }
}
