<?php

declare(strict_types=1);

namespace Action;

class Index extends BaseAction {

    public function get_index()
    {
        return $this->renderResponse('index');
    }
}
