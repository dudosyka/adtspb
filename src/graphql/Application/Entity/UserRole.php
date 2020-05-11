<?php


namespace GraphQL\Application\Entity;


class UserRole extends EntityBase
{
    public $role_id;
    public $user_id;

    public function __getTable()
    {
        return "user_role";
    }
}