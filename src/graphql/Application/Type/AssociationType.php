<?php
namespace GraphQL\Application\Type;

use GraphQL\Application\AppContext;
use GraphQL\Application\Database\DataSource;
use GraphQL\Application\Data\User;
use GraphQL\Application\Entity\Association;
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
                    'group_count' => Types::int(),
	                'study_hours_week' => ['type' => Types::int()],
	                'description' => ['type' => Types::string()],
	                'isClosed' => Types::int(),
                    'isHidden' => Types::int(),
                    'statistic' => Types::string(),

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

    public function resolveStatistic(Association $association, array $args, AppContext $context, ResolveInfo $info)
    {
        $max = $association->group_count * 20;
        $proposal_count = DataSource::_query("SELECT COUNT(*) AS `all`, SUM(`proposal`.`status_admin_id` = 6) AS `brought`, (SUM(`proposal`.`status_parent_id` = 3) + SUM(`proposal`.`status_admin_id` = 7)) AS `reject`  FROM `proposal` WHERE `association_id` = :id", [':id' => $association->id]);
        $reject = $proposal_count[0]->reject;
        $all = $proposal_count[0]->all;
        $brought = $proposal_count[0]->brought;
        return json_encode([
            'fullness_percent' => floor(100 * $reject / $max),
            'brought_percent' => floor(100 * $brought / $all)
        ]);
    }

}
