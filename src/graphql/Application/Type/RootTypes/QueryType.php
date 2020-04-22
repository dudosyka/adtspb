<?php
namespace GraphQL\Application\Type;

use GraphQL\Application\AppContext;
use GraphQL\Application\Data\User;
use GraphQL\Application\Database\DataSource;
use GraphQL\Application\Types;
use GraphQL\Server\RequestError;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

/**
 * Class QueryType
 * Корневой тип, содержащий общие методы по нахождению других типов.
 *
 *
 * @package GraphQL\Application\Type
 */
class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Query',
            'fields' => [

            	// Не забывайте писать документацию методов и полей GraphQL, иначе они не будут зарегистрированы.

	            // Методы
//                'user' => [
//                    'type' => Types::user(), // Тип данных, которые возвращает метод
//                    'description' => 'Returns user by id (in range of 1-5)', // Описание метода
//                    'args' => [ // Аргументы
//                        'id' => Types::nonNull(Types::id())
//                    ]
//                ],

                'viewer' => [
                    'type' => Types::user(),
                    'description' => 'Текущий (локальный) пользователь.'
                ],

//                'hello' => Type::string()


            ],
            'resolveField' => function($val, $args, $context, ResolveInfo $info) {
                return $this->{$info->fieldName}($val, $args, $context, $info);
            }
        ];
        parent::__construct($config);
    }


    /*
     *
     */


	/**
	 * Поиск единичного пользователя из базы данных
	 *
	 * @param $rootValue
	 * @param $args
	 * @return mixed
	 */
	public function user($rootValue, $args, AppContext $context)
    {
        return DataSource::find('User', $args['id']);
    }

    /**
     * Текущий пользователь
     *
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return \GraphQL\Application\Entity\User
     * @throws RequestError
     */
	public function viewer($rootValue, $args, AppContext $context)
    {
        if(!$context->viewer->isAuthorized()){
            throw new RequestError("Требуется авторизация");
        }

        return $context->viewer;
    }


	/**
	 * "Ping" о том, что сервер работает корректно
	 *
	 * @return string
	 */
//	public function hello()
//    {
//        return 'GraphQL сервер успешно работает.';
//    }
}
