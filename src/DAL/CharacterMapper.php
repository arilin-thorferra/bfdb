<?php

declare(strict_types=1);

namespace DAL;

class CharacterMapper extends Mapper
{
    public const TABLE = 'characters';

    /**
     * Make a unique lookup ID for a character, ensuring there is no
     * duplication in the database
     *
     * @return string
     */
    public function makeUniqid(): string
    {
        $id = bin2hex(random_bytes(6));
        $count = $this->count('uniqid', $id);
        if ($count) {
            $id = $this->makeUniqid();
        }
        return $id;
    }

    /**
     * Create a character, assigning a unique lookup ID
     *
     * @param array $data
     * @return void
     */
    public function create(array $data)
    {
        $data['uniqid'] = $this->makeUniqid();
        return $this->insert($data);
    }
}
