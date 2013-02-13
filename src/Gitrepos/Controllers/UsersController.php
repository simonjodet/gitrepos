<?php

namespace Gitrepos\Controllers;

use \Symfony\Component\Validator\Constraints as Assert;

class UsersController extends Controller
{
    public function postAction()
    {
        try {
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid username', 400);
            }

            if (!isset($this->app['request.body_params']['username'])) {
                throw new \Exception('Invalid username', 400);
            }

            $constraints = new Assert\Length(array('min' => 3, 'max' => 64));
            if (count(
                $this->validator->validateValue($this->app['request.body_params']['username'], $constraints)
            ) > 0
            ) {
                throw new \Exception('Invalid username', 400);
            }

            $constraints = new Assert\Email();
            if (count($this->validator->validateValue($this->app['request.body_params']['email'], $constraints)) > 0) {
                throw new \Exception('Invalid email', 400);
            }

            $constraints = new Assert\Length(array('min' => 6, 'max' => 128));
            if (count(
                $this->validator->validateValue($this->app['request.body_params']['password'], $constraints)
            ) > 0
            ) {
                throw new \Exception('Invalid password', 400);
            }

            $UserModel = $this->app['model.factory']->get('User');
            try {
                $UserModel->create(new \Gitrepos\Entities\User($this->app['request.body_params']));
            } catch (\Gitrepos\Exceptions\DuplicateUsername $e) {
                throw new \Exception('This username is already used', 409);
            } catch (\Gitrepos\Exceptions\DuplicateEmail $e) {
                throw new \Exception('This email is already used', 409);
            }

            $response = $this->app->json(null, 201);
            $response->setContent('');
        } catch (\Exception $e) {
            $error_body =
                array(
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'doc' => '/docs/users.json'
                );
            $response = $this->app->json($error_body, $e->getCode());
        }
        return $response;
    }
}
