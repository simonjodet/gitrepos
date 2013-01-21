<?php
/**
 * Gitrepos User
 * @package Gitrepos
 */
namespace Gitrepos\Entities;

/**
 * User
 */
class User
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;


    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $id;

    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * @var bool
     */
    private $accountNonExpired = true;

    /**
     * @var bool
     */
    private $credentialsNonExpired = true;

    /**
     * @var bool
     */
    private $accountNonLocked = true;

    /**
     * @var array
     */
    private $roles = array('ROLE_USER');


    /**
     * @param array $data
     */
    public function __construct($data = array())
    {
        if (is_array($data)) {
            foreach ($data as $property => $value) {
                $setter = 'set' . ucfirst($property);
                if (method_exists($this, $setter)) {
                    $this->$setter($value);
                }
            }
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return $this->accountNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return $this->accountNonLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return $this->credentialsNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

}