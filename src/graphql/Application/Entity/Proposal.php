<?php


namespace GraphQL\Application\Entity;


class Proposal extends EntityBase
{
    public $timestamp;
    public $child_id;
    public $parent_id;
    public $association_id;
    public $status_admin_id;
    public $status_parent_id;
    public $status_teacher_id;
    public $isReserve;

    public function __getTable()
    {
        return "proposal";
    }
}