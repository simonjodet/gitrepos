<?php

namespace Gitrepos\Models;

/**
 * TODO: add created date to create()
 * TODO: generate random salt
 * TODO: Use https://github.com/ircmaxell/password_compat lib
 */
class UserModel
{
    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    public function create(\Gitrepos\User $User)
    {
        $encodedPassword = $this->app['security.encoder_factory']->getEncoder($User)->encodePassword(
            $User->getPassword(),
            $User->getSalt()
        );

        $User->setPassword($encodedPassword);
        try {
            $this->app['db']->insert(
                'users',
                array(
                    'username' => $User->getUsername(),
                    'email' => $User->getEmail(),
                    'password' => $User->getPassword()
                )
            );
        } catch (\Exception $e) {
            if ($e->getCode() == '23000' && preg_match(
                '%column (?P<constraint>.+) is not unique%',
                $e->getMessage(),
                $matches
            )
            ) {
                $exceptionClass = '\Gitrepos\Exceptions\Duplicate' . ucfirst($matches['constraint']);
                throw new $exceptionClass();
            }
            throw $e;
        }

        $User->setId($this->app['db']->lastInsertId());

        return $User;
    }
}