<?php


namespace GraphQL\Application\Entity;


class Cooldown extends EntityBase
{
    public $user_id;
    public $type;
    public $date_created;

    public function __getTable()
    {
        return "cooldown";
    }
}