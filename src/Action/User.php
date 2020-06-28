<?php

declare(strict_types=1);

namespace Action;

use Context;
use Form;
use DAL\CharacterMapper;
use DAL\UserMapper;

class User extends BaseAction
{
    /**
     * Whitelist create action; everything else requires logged in user
     */
    const ALLOW = ['create'];

    /**
     * New user registration form (GET /register)
     */
    public function get_create()
    {
        return $this->renderFormResponse('create', new Form());
    }

    /**
     * Create a new user from form (POST /register)
     */
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
        Context::flash('Account created!');
        return $this->response->redirect('/account');
    }

    /**
     * Display the user's account page (GET /account)
     */
    public function get_account()
    {
        $u = new UserMapper();
        $c = new CharacterMapper();
        $user = $_SESSION['user'];
        $characters = $c->findSet('user_id', $user['id']);
        return $this->renderFormResponse(
            'account',
            new Form(),
            compact('user', 'characters')
        );
    }

    /**
     * Display the user's account editing page (GET /account/edit)
     */
    public function get_edit()
    {
        return $this->renderFormResponse(
            'edit',
            new Form(),
            ['user' => $_SESSION['user']]
        );
    }

    public function post_edit()
    {
        $f = new Form($_POST);
        $u = new UserMapper();
        $user = $u->find($_SESSION['user']['id']);

        // validate the form and re-display if there are errors
        if (!password_verify($_POST['old'], $user['passwd'])) {
            $f->setError('old', 'Invalid password');
        }
        if (mb_strlen($_POST['new']) < 8) {
            $f->setError('new', 'Password must be at least 8 characters');
        } elseif ($_POST['new'] != $_POST['confirm']) {
            $f->setError('confirm', 'Passwords must match!');
        }
        if ($f->hasErrors()) {
            return $this->renderFormResponse('edit', $f, ['user' => $user]);
        }

        // update the password
        $u->update(
            $user['id'],
            ['passwd' => password_hash($_POST['new'], PASSWORD_DEFAULT)]
        );
        Context::flash('Password updated!');
        return $this->response->redirectRoute('User/account');
    }
}
