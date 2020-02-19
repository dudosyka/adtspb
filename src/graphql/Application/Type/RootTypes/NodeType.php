<?php
namespace GraphQL\Application\Type;

use GraphQL\Application\Types;
use GraphQL\Type\Definition\InterfaceType;

/**
 * Class NodeType
 * Интерфейс содержащий поле с ID. Используется у всех сущностей.
 *
 * @package GraphQL\Application\Type
 */
class NodeType extends InterfaceType
{
    public function __construct()
    {
        $config = [
            'name' => 'Node',
            'fields' => [
                'id' => Types::id()
            ],
            'resolveType' => [$this, 'resolveNodeType']
        ];
        parent::__construct($config);
    }

    public function resolveNodeType($object)
    {
    	$__var = mb_strtolower(get_class($object));
        return Types::$$__var;
    }
}
