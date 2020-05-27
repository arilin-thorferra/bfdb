<?php

declare(strict_types=1);

namespace Action;

use Form;
use DAL\UserMapper;

class User extends BaseAction
{
    // only the create action is allowed to users not logged in
    const ALLOW = ['create'];

    // initial form for creating new users
    public function get_create()
    {
        return $this->renderFormResponse('create', new Form());
    }                
    
    // POST action for creating new users
    public function post_create()
    {
        $f = new Form($_POST);
        $u = new UserMapper();

        // validate the form and re-display if there are errors
        if ($u->exists($_POST['email'])) {
            $f->setError('email', 'Email address already in use!');
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $f->setError('email', 'Invalid email address');
        }
        if (mb_strlen($_POST['password']) < 8) {
            $f->setError('password', 'Password must be at least 8 characters');
        } elseif ($_POST['password'] != $_POST['confirm']) {
            $f->setError('confirm', 'Passwords must match!');
        }
        if ($f->hasErrors()) {
            return $this->renderFormResponse('create', $f);
        }

        // it's okay, go ahead and create the user
        $user = $u->create();
        $_SESSION['user'] = $user;
        return $this->response->redirect('/account');
    }

    // user account page
    public function get_show()
    {
        $u = new UserMapper();
        $u->find($_SESSION['user']);
        return $this->response;
    }
}
