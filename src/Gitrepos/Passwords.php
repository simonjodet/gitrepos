<?php

namespace Gitrepos;

class Passwords
{
    public function password_hash($password, $algo, array $options = array())
    {
        return password_hash($password, $algo, $options);
    }

    public function password_get_info($hash)
    {
        return password_get_info($hash);
    }

    public function password_needs_rehash($hash, $algo, array $options = array())
    {
        return password_needs_rehash($hash, $algo, $options);
    }

    public function password_verify($password, $hash)
    {
        return password_verify($password, $hash);
    }
}