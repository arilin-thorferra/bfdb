<?php

declare(strict_types=1);

namespace DAL;

class UserMapper extends Mapper
{
    public const TABLE = 'users';

    /**
     * Check to see whether an email address is already registered
     *
     * @param string $email
     * @return boolean
     */
    public function exists(string $email): bool
    {
        return (bool) $this->count('email', $email);
    }

    /**
     * Create a user from form data, hashing the password
     *
     * @return void
     */
    public function create()
    {
        $data = [
            'email' => $_POST['email'],
            'passwd' => password_hash($_POST['password'], PASSWORD_DEFAULT)
        ];
        $id = $this->insert($data);
        return [
            'id' => $id,
            'email' => $_POST['email'],
            'is_admin' => false,
            'adult_ok' => false
        ];
    }
}
