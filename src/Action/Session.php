<?php

declare(strict_types=1);

namespace Action;

use Form;
use Context;

class Session extends BaseAction
{

    /**
     * Login form (GET /login)
     */
    public function get_login()
    {
        if (isset($_SESSION['user'])) {
            return $this->response->setStatus(418);
        }
        return $this->renderFormResponse('login', new Form());
    }

    /**
     * Process login (POST /login)
     */
    public function post_login()
    {
        $f = new Form($_POST);
        $u = new \DAL\UserMapper();

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $f->setError('email', 'Invalid email address');
        }
        if (mb_strlen($_POST['password']) < 8) {
            $f->setError('password', 'Password must be at least 8 characters');
        }
        if (!$f->hasErrors()) {
            $user = $u->find($_POST['email'], 'email');
            Context::debug(var_export($user, true));
            if (!$user) {
                $f->setError('email', 'Email address not found');
            } else {
                if (!password_verify($_POST['password'], $user['passwd'])) {
                    $f->setError('password', 'Invalid password');
                }
            }
        }
        if ($f->hasErrors()) {
            return $this->renderFormResponse('login', $f);
        }
        $_SESSION['user'] = $user['id'];
        return $this->response->redirect('/account');
    }

    /**
     * Log out (GET /logout)
     */
    public function get_logout()
    {
        unset($_SESSION['user']);
        return $this->response->redirect('/');
    }
}
