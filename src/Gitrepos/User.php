<?php
/**
 * Gitrepos User
 * @package Gitrepos
 */
namespace Gitrepos;

/**
 * User
 */
class User
{
    /**
     * @var int User's database ID
     */
    private $id;

    /**
     * @var string User's username
     */
    private $username;

    /**
     * @var string User's email
     */
    private $email;

    /**
     * @var string User's password
     */
    private $password;


    /**
     * Constructor
     * @param array $data User data for quick initialization - Optional
     */
    public function __construct($data = array())
    {
        if (is_array($data))
        {
            foreach ($data as $property => $value)
            {
                $setter = 'set' . ucfirst($property);
                if (method_exists($this, $setter))
                {
                    $this->$setter($value);
                }
            }
        }
    }

    /**
     * Email setter
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Email getter
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * ID setter
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * ID getter
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Password setter
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Password getter
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Username setter
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Username getter
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}