<?php

declare(strict_types=1);

namespace DAL;

class UserMapper extends Mapper
{
    public const TABLE = 'users';

    public function exists(string $email) : bool
    {
        return (bool)$this->count('email', $email);
    }
    
    public function create()
    {
        $data = [
            'email' => $_POST['email'],
            'passwd' => password_hash($_POST['password'], PASSWORD_DEFAULT)
        ];
        return $this->insert($data);
    }
}
