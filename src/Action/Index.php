<?php

declare(strict_types=1);

namespace Bfdb\Action;

class Index extends BaseAction
{

    public function get_index()
    {
        return $this->renderResponse('index');
    }
}
