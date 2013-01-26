<?php
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Validator\Constraints as Assert;

$app->before(
    function (Request $request) use ($app) {
        $app['request.body_params'] = json_decode($request->getContent(), true);
    }
);


$app->post(
    '/v1/users',
    function (Request $request) use ($app) {
        try {
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid username', 400);
            }

            if (!isset($app['request.body_params']['username'])) {
                throw new \Exception('Invalid username', 400);
            }

            $constraints = new Assert\Length(array('min' => 3, 'max' => 64));
            if (count($app['validator']->validateValue($app['request.body_params']['username'], $constraints)) > 0) {
                throw new \Exception('Invalid username', 400);
            }

            $constraints = new Assert\Email();
            if (count($app['validator']->validateValue($app['request.body_params']['email'], $constraints)) > 0) {
                throw new \Exception('Invalid email', 400);
            }

            $constraints = new Assert\Length(array('min' => 6, 'max' => 128));
            if (count($app['validator']->validateValue($app['request.body_params']['password'], $constraints)) > 0) {
                throw new \Exception('Invalid password', 400);
            }

            $UserModel = $app['model.factory']->get('User');
            try {
                $UserModel->create(new \Gitrepos\Entities\User($app['request.body_params']));
            } catch (\Gitrepos\Exceptions\DuplicateUsername $e) {
                throw new \Exception('This username is already used', 409);
            } catch (\Gitrepos\Exceptions\DuplicateEmail $e) {
                throw new \Exception('This email is already used', 409);
            }

            $response = $app->json(null, 201);
            $response->setContent('');
        } catch (\Exception $e) {
            $error_body =
                array(
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'doc' => '/docs/users.json'
                );
            $response = $app->json($error_body, $e->getCode());
        }
        return $response;
    }
);

$app->post(
    '/v1/sessions',
    function (Request $request) use ($app) {
        $UserModel = $app['model.factory']->get('User');

        /**
         * @var $User \Gitrepos\Entities\User
         */
        $User = $UserModel->authenticate(
            $app['request.body_params']['username'],
            $app['request.body_params']['password']
        );
        if ($User !== false) {
            session_name('SESSION');
            session_start();
            $_SESSION['username'] = $User->getUsername();

            $response = $app->json('', 230);
            $response->setContent('{"session":"' . session_id() . '"}');
        } else {
            $error_body =
                array(
                    'code' => 401,
                    'message' => 'Bad credentials',
                    'doc' => '/docs/sessions.json'
                );
            $response = $app->json($error_body, 401);
        }

        return $response;
    }
);

$app->delete(
    '/v1/sessions/current',
    function (Request $request) use ($app) {
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
            $response = $app->json($error_body, 401);
            return $response;
        } else {
            session_destroy();
            setcookie(session_name(), '', 0, '/');
            session_regenerate_id(true);
            return '';
        }
    }
);

return $app;