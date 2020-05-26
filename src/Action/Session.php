<?php

declare(strict_types=1);

namespace Action;

use Form;

class Session extends BaseAction {

    public function get_login()
    {
        return $this->renderFormResponse('login', new Form());
    }

    public function post_login()
    {
        $f = new Form($_POST);
        $f->setError('email', "I don't like your face");
        return $this->renderFormResponse('login', $f);
    }
}
