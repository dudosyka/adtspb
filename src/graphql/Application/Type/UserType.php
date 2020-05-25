<?php
namespace GraphQL\Application\Type;

use GraphQL\Application\AppContext;
use GraphQL\Application\Database\DataSource;
use GraphQL\Application\Entity\User;
use GraphQL\Application\Entity\UserChild;
use GraphQL\Application\Types;
use GraphQL\Server\RequestError;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class UserType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'User',
            'description' => 'Пользователи личного кабинета.',
            'fields' => function() {

                //TODO: запретить вывод некоторых полей для любых пользователей, разрешить просматривать все поля только viewer'у.

                return [
                    'id' => Types::id(),
	                'password' => ['type' => Types::password()],
	                'surname' => ['type' => Types::string()],
	                'name' => ['type' => Types::string()],
	                'midname' => ['type' => Types::string()],

	                'sex' => Types::sex(),

	                'phone_number' => Types::phoneNumber(),

	                'email' => Types::email(),

	                'registration_address' => ['type' => Types::string()],
	                'residence_address' => ['type' => Types::string()],
	                'job_place' => ['type' => Types::string()],
	                'job_position' => ['type' => Types::string()],
	                'relationship_id' => ['type' => Types::int()],
	                'study_place' => ['type' => Types::string()],
	                'study_class' => ['type' => Types::string()],
	                'date_registered' => ['type' => Types::date()],
	                'birthday' => ['type' => Types::date()],
	                'status_email' => ['type' => Types::string()],
//	                'verification_key_email' => ['type' => Types::string()], // публике это поле не надо знать

                    'hasAnyChildrenAdded' => ['type' => Types::boolean()],
                    'hasAnyProposals' => ['type' => Types::boolean()],
                    'getChildren' => ['type' => Types::listOf(Types::user())],
                    'getInProposals' => Types::listOf(Types::proposal())
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

    /**
     * @param User $user
     * @param array $args
     * @param AppContext $context
     * @param ResolveInfo $info
     * @return bool
     * @throws RequestError
     */
    public function resolveHasAnyChildrenAdded(User $user, array $args, AppContext $context, ResolveInfo $info)
    {
        // Если не родитель
        if(!$context->viewer->hasAccess(8)) return false; // Не выводим ошибку т.к. необходимо без ошибок завершить запрос при авторизации

        // Если не локальный пользователь
        if($context->viewer->id != $user->id)
            throw new RequestError("Доступ запрещен");

        return DataSource::findOne("UserChild", "parent_id = :parent_id", ["parent_id" => $user->id]) != null;
    }

    /**
     * @param User $user
     * @param array $args
     * @param AppContext $context
     * @param ResolveInfo $info
     * @return array
     * @throws RequestError
     */
    public function resolveGetChildren(User $user, array $args, AppContext $context, ResolveInfo $info){

        // Если не родитель
        $context->viewer->hasAccessOrError(9);

        // Если не локальный пользователь
        if($context->viewer->id != $user->id)
            throw new RequestError("Доступ запрещен");


        // TODO: оптимизировать вывод всех детей (resolveGetChildren)

        $children = DataSource::findAll("UserChild", "parent_id = :parent_id", [
            "parent_id" => $user->id
        ]);

        $return = [];

        foreach($children as $child){
            /** @var UserChild $child */
            $return[] = DataSource::find("User", $child->child_id);
        }

        return $return;
    }

    /**
     * @param User $user
     * @param array $args
     * @param AppContext $context
     * @param ResolveInfo $info
     * @return bool
     * @throws RequestError
     */
    public function resolveHasAnyProposals(User $user, array $args, AppContext $context, ResolveInfo $info) {
        // Если не родитель
        if(!$context->viewer->hasAccess(8)) return false; // Не выводим ошибку т.к. необходимо без ошибок завершить запрос при авторизации

        // Если не локальный пользователь
        if($context->viewer->id != $user->id)
            throw new RequestError("Доступ запрещен");

        return DataSource::findOne("Proposal", "parent_id = :parent_id", ["parent_id" => $user->id]) != null;
    }

    /**
     * @param User $user
     * @param array $args
     * @param AppContext $context
     * @param ResolveInfo $info
     * @return array
     * @throws RequestError
     */
    public function resolveGetInProposals(User $user, array $args, AppContext $context, ResolveInfo $info) {
        // Если не родитель
        $context->viewer->hasAccessOrError(8);

        // Если не родитель ребенка
        $find = DataSource::findOne("UserChild", "parent_id = :parent_id AND child_id = :child_id", [
           "child_id" => $user->id,
           "parent_id" => $context->viewer->id
        ]);

        if($find == null)
            throw new RequestError("Доступ запрещен");




        return DataSource::findAll("Proposal", "child_id = :child_id", ["child_id" => $user->id]);
    }


}
