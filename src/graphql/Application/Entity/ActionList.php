<?php


namespace GraphQL\Application\Entity;


class ActionList extends EntityBase
{

    public $list_id;
    public $role_id;
    public $action_id;
    public $sign;

    public function __construct(array $data = null)
    {
        parent::__construct($data);
    }

    public function __getTable() {
        return "action_list";
    }
}