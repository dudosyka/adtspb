<?php
namespace GraphQL\Application\Type;

use GraphQL\Application\AppContext;
use GraphQL\Application\Database\DataSource;
use GraphQL\Application\Data\User;
use GraphQL\Application\Types;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class AssociationType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Association',
            'description' => 'Ассоциация',
            'fields' => function() {

                return [
                    'id' => Types::id(),
	                'name' => ['type' => Types::string()],
	                'min_age' => ['type' => Types::int()],
	                'max_age' => ['type' => Types::int()],
	                'study_years' => ['type' => Types::int()],
	                'study_hours' => ['type' => Types::int()],
	                'study_hours_week' => ['type' => Types::int()],
	                'description' => ['type' => Types::string()],
	                'isClosed' => Types::int(),
                    'isHidden' => Types::int()
                ];
            },
            'interfaces' => [
                Types::node() //объект, имеющий ID
            ],
            'resolveField' => function($value, $args, $context, ResolveInfo $info) {
                $method = 'resolve' . ucfirst($info->fieldName);
                if (method_exists($this, $method)) {
                    return $this->{$method}($value, $args, $context, $info);
                } else {
                    return $value->{$info->fieldName};
                }
            }
        ];
        parent::__construct($config);
    }

}
