<?php


namespace GraphQL\Application\Entity;


class AssociationSpecials extends EntityBase
{
    public int $association_id;
    public int $token;


    public function __construct(array $data = null)
    {
        parent::__construct($data);
    }

    public function __getTable() {
        return "association_specials";
    }
}