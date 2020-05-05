<?php


namespace GraphQL\Application\Entity;


class Group extends EntityBase
{

    public $association_id;
    public $teacher_id;

    public function __construct(array $data = null)
    {
        parent::__construct($data);
    }

    public function __getTable() {
        return "group";
    }
}