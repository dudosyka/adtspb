<?php


namespace GraphQL\Application\Entity;


class PasswordRestore extends EntityBase
{
    public $user_id;
    public $key_code;
    public $date_created;
    public $ip;

    public function __getTable()
    {
        return "passwordrestore";
    }
}