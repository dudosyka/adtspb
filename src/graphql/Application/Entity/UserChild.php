<?php


namespace GraphQL\Application\Entity;


class UserChild extends EntityBase
{
    public $child_id;
    public $parent_id;

    public function __getTable()
    {
        return "user_child";
    }
}