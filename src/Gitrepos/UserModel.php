<?php

namespace Gitrepos;

class UserModel
{
    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory
     */
    private $encoderFactory;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $db;

    public function __construct(\Silex\Application $app)
    {
        $this->encoderFactory = $app['security.encoder_factory'];
        $this->db = $app['db'];
    }

    public function create(\Gitrepos\User $User)
    {
        $encodedPassword = $this->encoderFactory->getEncoder($User)->encodePassword($User->getPassword(), $User->getSalt());

        $User->setPassword($encodedPassword);

        $this->db->insert('users', array(
            'username' => $User->getUsername(),
            'email' => $User->getEmail(),
            'password' => $User->getPassword()
        ));

        $User->setId($this->db->lastInsertId());

        return $User;
    }
}