<?php


namespace GraphQL\Application\Entity;


class SettingsProposal extends EntityBase
{
    public $name;

    public function __getTable()
    {
        return "settings_proposal";
    }
}