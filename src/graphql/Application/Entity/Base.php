<?php


namespace GraphQL\Application\Entity;


class Base extends EntityBase
{
    public int $id;

    public function __construct(array $data = null)
    {
        parent::__construct($data);
    }

    public function __getTable() {
        return "";
    }
}