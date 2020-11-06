<?php

declare(strict_types=1);

namespace Action;

use Context;
use Form;
use DAL\CharacterMapper;
use DAL\UserMapper;

class Character extends BaseAction
{
    /**
     * Whitelist show and browse actions
     */
    const ALLOW = ['show', 'browse'];

    public function get_create()
    {
        return $this->renderFormResponse('edit', new Form());
    }

    public function get_edit()
    {
        return $this->renderFormResponse('edit', new Form());
    }
}
