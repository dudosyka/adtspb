<?php


namespace GraphQL\Application\Entity;


class Association extends EntityBase
{

    public $name;
    public $min_age;
    public $max_age;
    public $study_years;
    public $study_hours;
    public $study_hours_week;

    public function __construct(array $data = null)
    {
        parent::__construct($data);
    }

    public function __getTable() {
        return "association";
    }
}