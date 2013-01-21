<?php
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Validator\Constraints as Assert;

$app->post(
    '/v1/users',
    function (Request $request) use ($app) {
        try {
            $params = json_decode($request->getContent(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid username', 400);
            }

            if (!isset($params['username'])) {
                throw new \Exception('Invalid username', 400);
            }

            $constraints = new Assert\Length(array('min' => 3, 'max' => 64));
            if (count($app['validator']->validateValue($params['username'], $constraints)) > 0) {
                throw new \Exception('Invalid username', 400);
            }

            $constraints = new Assert\Email();
            if (count($app['validator']->validateValue($params['email'], $constraints)) > 0) {
                throw new \Exception('Invalid email', 400);
            }

            $constraints = new Assert\Length(array('min' => 6, 'max' => 128));
            if (count($app['validator']->validateValue($params['password'], $constraints)) > 0) {
                throw new \Exception('Invalid password', 400);
            }

            $UserModel = $app['model.factory']->get('User');
            try {
                $UserModel->create(new \Gitrepos\Entities\User($params));
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
        $response = $app->json('', 230);
        $response->setContent('{"session":"azeqsdqsd","ttl":"3600"}');
        return $response;
    }
);


return $app;