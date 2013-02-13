<?php

namespace Gitrepos\Controllers;

class SessionsController extends Controller
{
    public function postAction()
    {
        $UserModel = $this->app['model.factory']->get('User');

        /**
         * @var $User \Gitrepos\Entities\User
         */
        $User = $UserModel->authenticate(
            $this->app['request.body_params']['username'],
            $this->app['request.body_params']['password']
        );
        if ($User !== false) {
            session_name('SESSION');
            session_start();
            $_SESSION['username'] = $User->getUsername();
            $_SESSION['user_id'] = $User->getId();

            $response = $this->app->json('', 200);
            $response->setContent('{"session":"' . session_id() . '"}');
        } else {
            $error_body =
                array(
                    'code' => 401,
                    'message' => 'Bad credentials',
                    'doc' => '/docs/sessions.json'
                );
            $response = $this->app->json($error_body, 401);
        }

        return $response;
    }

    public function deleteCurrentAction()
    {
        session_name('SESSION');
        try {
            session_start();
        } catch (\Exception $e) {
        }
        if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
            $error_body =
                array(
                    'code' => 401,
                    'message' => 'Requires authentication',
                    'doc' => '/docs/sessions.json'
                );
            $response = $this->app->json($error_body, 401);
            return $response;
        } else {
            session_destroy();
            setcookie(session_name(), '', 0, '/');
            session_regenerate_id(true);
            return '';
        }
    }
}
