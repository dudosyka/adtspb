<?php
namespace GraphQL\Application\Type;

use Com\Tecnick\Pdf\Encrypt\Data;
use GraphQL\Application\AppContext;
use GraphQL\Application\Database\DataSource;
use GraphQL\Application\Entity\Proposal;
use GraphQL\Application\Types;
use GraphQL\Server\RequestError;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class ProposalType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Proposal',
            'description' => 'Заявление',
            'fields' => function() {

                return [
                    'id' => Types::id(),
	                'timestamp' => ['type' => Types::date()],
	                'child_id' => ['type' => Types::int()],
	                'parent_id' => ['type' => Types::int()],
	                'association_id' => ['type' => Types::int()],
	                'status_admin_id' => ['type' => Types::int()],
	                'status_parent_id' => ['type' => Types::int()],
	                'status_teacher_id' => ['type' => Types::int()],
	                'status_admin' => ['type' => Types::string()],
	                'status_parent' => ['type' => Types::string()],
	                'status_teacher' => ['type' => Types::string()],
                    'getAssociation' => ['type' => Types::association()],
                    'getChild' => ['type' => Types::user()],
                    'getParent' => ['type' => Types::user()],
                    'parentStatus' => ['type' => Types::string()],
                    'adminStatus' => ['type' => Types::string()],
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


    public function resolveGetAssociation(Proposal $proposal, array $args, AppContext $context, ResolveInfo $info)
    {
        //TODO: право на доступ?
        //TODO: обезопасить?

        // Если не родитель
        if(!$context->viewer->hasAccess(8)) return false; // Не выводим ошибку т.к. необходимо без ошибок завершить запрос при авторизации

        return DataSource::findOne("Association", "id = :association_id", ["association_id" => $proposal->association_id]);
    }

    /**
     * @param Proposal $proposal
     * @param array $args
     * @param AppContext $context
     * @param ResolveInfo $info
     * @return mixed|null
     */
    public function resolveGetChild(Proposal $proposal, array $args, AppContext $context, ResolveInfo $info)
    {
        return DataSource::findOne("User", "id = :id", [':id' => $proposal->child_id]);
    }

    /**
     * @param Proposal $proposal
     * @param array $args
     * @param AppContext $context
     * @param ResolveInfo $info
     * @return mixed|null
     */
    public function resolveGetParent(Proposal $proposal, array $args, AppContext $context, ResolveInfo $info)
    {
        return DataSource::findOne("User", "id = :id", [':id' => $proposal->parent_id]);
    }

    /**
     * @param Proposal $proposal
     * @param array $args
     * @param AppContext $context
     * @param ResolveInfo $info
     * @return mixed
     */
    public function resolveParentStatus(Proposal $proposal, array $args, AppContext $context, ResolveInfo $info)
    {
        return DataSource::findOne("SettingsProposal", 'id = :id', [':id' => $proposal->status_parent_id])->name;
    }

    /**
     * @param Proposal $proposal
     * @param array $args
     * @param AppContext $context
     * @param ResolveInfo $info
     * @return mixed
     */
    public function resolveAdminStatus(Proposal $proposal, array $args, AppContext $context, ResolveInfo $info)
    {
        return DataSource::findOne("SettingsProposal", 'id = :id', [':id' => $proposal->status_admin_id])->name;
    }
}
