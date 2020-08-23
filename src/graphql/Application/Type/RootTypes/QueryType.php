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

                'associationsForChild' => [
                    'type' => Types::listOf(Types::association()),
                    'description' => 'Вывод всех доступных объединений для конкретного ребенка',
                    'args' => [
                        'child_id' => Types::int()
                    ]
                ],

                'proposals' => [
                    'type' => Types::string(),
                    'description' => 'Получение всех заявлений',
                    'args' => [
                        'search' => Types::string()
                    ]
                ],

                'getAllChildren' => [
                    'type' => Types::listOf(Types::user()),
                    'description' => 'Получение всех детей',
                    'args' => []
                ],

                'getAllParents' => [
                    'type' => Types::listOf(Types::user()),
                    'description' => 'Получение всех родителей',
                    'args' => []
                ]


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

    public function associations($rootValue, $args, AppContext $context){
        // TODO: ограничение по списку ассоциаций?
        $context->viewer->hasAccessOrError(5);

        if (isset($args["closed"]) && isset($args["hidden"]))
        {
            if ($args['closed'] == 1 && $args['hidden'] == 1)
                return DataSource::findAll('Association', '`isClosed` = 1 AND `isHidden` != 0');
            if ($args['closed'] == 0 && $args['hidden'] == 0)
                return DataSource::findAll('Association', '`isClosed` = 0 AND `isHidden` = 0');
        }

        if (isset($args["closed"]))
        {
            if ($args["closed"] == 1)
                return DataSource::findAll('Association', '`isClosed` = 1');
            else
                return DataSource::findAll('Association', '`isClosed` = 0');
        }

        if (isset($args["hidden"]))
        {
            if ($args['hidden'] == 1)
                return DataSource::findAll('Association', '`isHidden` != 0');
            else
                return DataSource::findAll('Association', '`isHidden` = 0');
        }

        if(!isset($args["min_age"]) and !isset($args["max_age"]))
            return DataSource::findAll('Association', '1');

        if(!isset($args["min_age"]))
            throw new RequestError("Параметр min_age должен быть задан.");

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
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return array
     * @throws RequestError
     */
    public function proposals($rootValue, $args, AppContext $context)
    {
        $context->viewer->hasAccessOrError(17);

        $search = $args['search'];

          $data = DataSource::_query("
                SELECT
                 `association`.`name` AS `associationName`,
                 `proposal`.*,
                 `child`.`name` AS `childName`,
                 `child`.`surname` AS `childSurname`,
                 `child`.`midname` AS `childMidname`,
                 `child`.`sex` AS `childSex`,
                 `child`.`email` AS `childEmail`,
                 `child`.`phone_number` AS `childPhone`,
                 `child`.`registration_address` AS `childRegistrationAddress`,
                 `child`.`registration_flat` AS `childRegistrationFlat`,
                 `child`.`registration_flat` AS `childResidenceAddress`,
                 `child`.`residence_flat` AS `childResidenceFlat`,
                 `parent`.`name` AS `parentName`,
                 `parent`.`surname` AS `parentSurname`,
                 `parent`.`midname` AS `parentMidname`,
                 `parent`.`sex` AS `parentSex`,
                 `parent`.`email` AS `parentEmail`,
                 `parent`.`phone_number` AS `parentPhone`,
                 `parent`.`registration_address` AS `parentRegistrationAddress`,
                 `parent`.`registration_flat` AS `parentRegistrationFlat`,
                 `parent`.`residence_address` AS `parentResidenceAddress`,
                 `parent`.`residence_flat` AS `parentResidenceFlat`,
                 `status_admin`.`name` AS `statusAdminName`,
                 `status_parent`.`name` AS `statusParentName`
                FROM `proposal`
                LEFT JOIN `association` ON `proposal`.`association_id` = `association`.`id`
                LEFT JOIN `settings_proposal` as `status_admin` ON `proposal`.`status_admin_id` = `status_admin`.`id`
                LEFT JOIN `settings_proposal` as `status_parent` ON `proposal`.`status_parent_id` = `status_parent`.`id`
                LEFT JOIN `user` as `child` ON `proposal`.`child_id` = `child`.`id`
                LEFT JOIN `user` as `parent` ON `proposal`.`parent_id` = `parent`.`id` 
                ");

        $res = [];
        foreach ($data as $item)
        {
            $item->childFullname = trim($item->childSurname) . " " . trim($item->childName) . ( $item->childMidname != "" ? " " . trim($item->childMidname) : "");
            $item->parentFullname = trim($item->parentSurname) . " " . trim($item->parentName) . ( $item->parentMidname != "" ? " " . trim($item->parentMidname) : "");
            if (
                preg_match("/.{0,}".$search.".{0,}/um", trim($item->childFullname)) ||
                preg_match("/.{0,}".$search.".{0,}/um", trim($item->parentFullname))
            )
            {
                $res[] = $item;
            }
        }

        return json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $rotValue
     * @param $args
     * @param AppContext $context
     * @return array
     */
    public function getAllChildren($rotValue, $args, AppContext  $context)
    {
        return DataSource::findAll("User", "password = ''");
    }

    /**
     * @param $rotValue
     * @param $args
     * @param AppContext $context
     * @return array
     */
    public function getAllParents($rotValue, $args, AppContext  $context)
    {
        return DataSource::findAll("User", "password != '' AND password != '1'");
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
