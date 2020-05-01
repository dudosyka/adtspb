<?php


namespace GraphQL\Application\Entity;


class Upload extends EntityBase
{

    public $type;
    public $file;
    public $status;
    public $url;
    public $ip;

    public function __construct(array $data = null)
    {
        parent::__construct($data);
    }

    public function __getTable() {
        return "upload";
    }
}