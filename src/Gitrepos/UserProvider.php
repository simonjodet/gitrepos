<?php

namespace Gitrepos;

use \Symfony\Component\Security\Core\User\UserProviderInterface,
\Symfony\Component\Security\Core\User\UserInterface,
\Symfony\Component\Security\Core\Exception\UnsupportedUserException,
\Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserProvider implements UserProviderInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $conn;

    public function __construct(\Silex\Application $app)
    {
        $this->conn = $app['db'];
    }

    public function loadUserByUsername($username)
    {
        /**
         * @var $stmt \Doctrine\DBAL\Statement
         */
        $stmt = $this->conn->executeQuery('SELECT * FROM users WHERE username = ?', array(strtolower($username)));

        if (!$user = $stmt->fetch())
        {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return new User($user);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User)
        {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}