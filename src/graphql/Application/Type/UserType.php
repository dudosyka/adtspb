<?php
namespace GraphQL\Application\Type;

use GraphQL\Application\AppContext;
use GraphQL\Application\Database\DataSource;
use GraphQL\Application\Data\User;
use GraphQL\Application\Types;
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

	            // Не забывайте писать документацию методов и полей GraphQL, иначе они не будут зарегистрированы.

                return [
                    'id' => Types::id(),
	                'password' => ['type' => Types::string()],
	                'surname' => ['type' => Types::string()],
	                'name' => ['type' => Types::string()],
	                'midname' => ['type' => Types::string()],
	                'sex' => ['type' => Types::int()],
	                'phone_number' => ['type' => Types::string()],
	                'email' => Types::email(),
	                'registration_address' => ['type' => Types::string()],
	                'residence_address' => ['type' => Types::string()],
	                'job_place' => ['type' => Types::string()],
	                'job_position' => ['type' => Types::string()],
	                'relationship' => ['type' => Types::string()],
	                'childids' => ['type' => Types::string()],
	                'study_place' => ['type' => Types::string()],
	                'study_class' => ['type' => Types::string()],
	                'date_registered' => ['type' => Types::string()],
	                'birthday' => ['type' => Types::string()],
	                'status_email' => ['type' => Types::string()],
	                'verification_key_email' => ['type' => Types::string()],
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

    /*
     * <b>Как добавить свое GraphQL полё</b>
     * Любой видимый для GraphQL в данном классе метод должен:
     *  1) быть публичной функцией,
     *  2) начинаться со слова 'resolve' (см. код на строчках 34-39), последующее слово должно быть написано с большой буквы (например, resolveMyName или resolveMyname и т.п.)
     * Не стоит забывать, что метод должен вернуть какое-нибудь значение для клиента.
     *
     * Пример объявления:
    public function resolvePhoto(User $user, $args)
    {
        return DataSource::getUserPhoto($user->id, $args['size']);
    }
    */

}
