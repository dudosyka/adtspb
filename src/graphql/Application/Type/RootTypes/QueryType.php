<?php
namespace GraphQL\Application\Type;

use Com\Tecnick\Barcode\Type\Square\PdfFourOneSeven\Data;
use GraphQL\Application\AppContext;
use GraphQL\Application\Database\DataSource;
use GraphQL\Application\Entity\Association;
use GraphQL\Application\Entity\User;
use GraphQL\Application\Entity\UserChild;
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
                'association' => [
                    'type' => Types::association(),
                    'description' => 'Вывод информации об объединении',
                    'args' => [
                        'id' => Types::nonNull(Types::id())
                    ]
                ],
                'associations' => [
                    'type' => Types::listOf(Types::association()),
                    'description' => 'Вывод всех доступных объединений',
                    'args' => [
                        'min_age' => Types::int(),
                        'hidden' => Types::int(),
                        'closed' => Types::int(),
                    ]
                ],

                'associationsExceptSpecials' => [
                    'type' => Types::listOf(Types::association()),
//                    'type' => Types::string(),
                    'description' => 'Вывод всех доступных для записи объединений',
                ],

                'associationsForChild' => [
                    'type' => Types::listOf(Types::association()),
                    'description' => 'Вывод всех доступных объединений для конкретного ребенка',
                    'args' => [
                        'child_id' => Types::int()
                    ]
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
        // Делаем не через isAuthorized() т.к. нам нужно узнать данные о себе (необходимо при авторизации)
        if(!isset($context->viewer->id) || $context->viewer->id == 0){
            throw new RequestError("Требуется авторизация");
        }

        return $context->viewer;
    }


    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return Association|null
     * @throws RequestError
     */
    public function association($rootValue, $args, AppContext $context){
        $context->viewer->hasAccessOrError(5);
        return DataSource::find('Association', $args['id']);
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return array
     * @throws RequestError
     */
    public function associationsExceptSpecials($rootValue, $args, AppContext $context){
        $context->viewer->hasAccessOrError(5);

        return DataSource::findAll("Association", '1');
//        return json_encode(DataSource::_query("SELECT `association_specials`.`association_id` AS `isAvailable`,`association`.`id`, `association`.`name`, `association`.`min_age`, `association`.`max_age`, `association`.`study_hours_week`, `association`.`description` FROM `association_specials` RIGHT JOIN `association` ON `association_specials`.`association_id` = `association`.`id` WHERE 1 ORDER BY `isAvailable` ASC"), JSON_UNESCAPED_UNICODE);
    }

    public function associations($rootValue, $args, AppContext $context){
        // TODO: ограничение по списку ассоциаций?
        $context->viewer->hasAccessOrError(5);

        if(!isset($args["min_age"]) and !isset($args["max_age"]))
            return DataSource::findAll('Association', '1');

        if(!isset($args["min_age"]))
            throw new RequestError("Параметр min_age должен быть задан.");

        if (isset($args["closed"]))
        {
            if ($args["closed"] == 1)
                return DataSource::findAll('Association', '`isClosed` == 1');
            else
                return DataSource::findAll('Association', '`isClosed` == 0');
        }

        if (isset($args["hidden"]))
        {
            if ($args['hidden'] == 1)
                return DataSource::findAll('Association', '`isHidden` == 1');
            else
                return DataSource::findAll('Association', '`isHidden` == 0');
        }

//        if(!isset($args["max_age"]))
//            throw new RequestError("Параметр max_age должен быть задан.");

//        $args["max_age"] = (int)$args["max_age"];
        $args["min_age"] = (int)$args["min_age"];

        // || $args["max_age"] < 0 || $args["max_age"] >= 1000 || $args["max_age"] < $args["min_age"]
        if ($args["min_age"] < 0 || $args["min_age"] >= 1000)
            throw new RequestError("Неверно задан параметр min_age"); //max_age или


        return DataSource::findAll('Association', 'min_age >= :min_age', [ //AND max_age <= :max_age
            ":min_age" => $args["min_age"],
//            ":max_age" => $args["max_age"]
        ]);
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return array
     * @throws RequestError
     * @throws \Exception
     */
    public function associationsForChild($rootValue, $args, AppContext $context) {
//        $context->viewer->hasAccessOrError(5);


        /** @var UserChild $info */
        $info = DataSource::findOne("UserChild", "child_id = :child_id", [
            ":child_id" => $args["child_id"]
        ]);

//        if($info == null || $info->parent_id != $context->viewer->id)
//            throw new RequestError("Ошибка");

        /** @var User $user */
        $user = DataSource::find("User", $args["child_id"]);

        $pass = [];
        $pass["min_age"] = $user->getAge();

        return $this->associations($rootValue, $pass, $context);
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
