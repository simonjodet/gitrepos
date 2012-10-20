<?php

namespace Gitrepos;

/**
 * TODO: add created date to create()
 *
 */
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
        try
        {
            $this->db->insert('users', array(
                'username' => $User->getUsername(),
                'email' => $User->getEmail(),
                'password' => $User->getPassword()
            ));
        }
        catch (\Exception $e)
        {
            if ($e->getCode() == '23000' && preg_match('%column (?P<constraint>.+) is not unique%', $e->getMessage(), $matches))
            {
                $exceptionClass = '\Gitrepos\Exceptions\Duplicate' . ucfirst($matches['constraint']);
                throw new $exceptionClass();
            }
            throw $e;
        }

        $User->setId($this->db->lastInsertId());

        return $User;
    }
}