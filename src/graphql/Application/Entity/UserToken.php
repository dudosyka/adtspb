<?php


namespace GraphQL\Application\Entity;


class UserToken extends EntityBase
{
    public $token;
    public $date_created;
    public $user_id;

    public function __getTable()
    {
        return "user_token";
    }
}