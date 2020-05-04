<?php
namespace GraphQL\Application\Type;

use GraphQL\Application\Application;
use GraphQL\Application\Bearer;
use GraphQL\Application\AppContext;
use GraphQL\Application\Database\DataSource;
use GraphQL\Application\Entity\Upload;
use GraphQL\Application\Entity\User;
use GraphQL\Application\Entity\UserToken;
use GraphQL\Application\File\CSVFileHandler;
use GraphQL\Application\File\FileStorage;
use GraphQL\Application\Types;
use GraphQL\Server\RequestError;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL\Upload\UploadType;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class QueryType
 * Корневой тип, содержащий общие методы по нахождению других типов.
 *
 *
 * @package GraphQL\Application\Type
 */
class MutationType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Mutation',
            'description' => 'Действия',
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


                'register' => [
                    'type' => Types::boolean(),
                    'description' => 'Зарегистрировать пользователя',
                    'args' => [
                        'name' => Types::nonNull(Types::string()),
                        'surname' => Types::nonNull(Types::string()),
                        'midname' => Types::nonNull(Types::string()),
                        'email' => Types::nonNull(Types::email()),
                        'password' => Types::nonNull(Types::password()),
                        'phone_number' => Types::nonNull(Types::phoneNumber()),
                        'sex' => Types::nonNull(Types::sex()),
                        'job_position' => Types::nonNull(Types::string()),
                        'job_place' => Types::nonNull(Types::string()),
                        'registration_address' => Types::nonNull(Types::string()),
                        'residence_address' => Types::nonNull(Types::string())
                    ]
                ],

                'login' => [
                    'type' => Types::listOf(Types::string()),
                    'description' => 'Авторизация пользователя',
                    'args' => [
                        'username' => Types::nonNull(Types::string()),
                        'password' => Types::nonNull(Types::string()),
                    ]
                ],

                'logout' => [
                    'type' => Types::boolean(),
                    'description' => 'Выход из аккаунта',
                    'args' => []
                ],

                'adminUploadTeachersList' => [
                    'type' => Types::string(),
                    'description' => 'Загрузить список педагогов на сервер (файл должен быть загружен в поле file0 POST-запроса)',
                    'args' => []
                ],

            ],
            'resolveField' => function($val, $args, $context, ResolveInfo $info) {
                return $this->{$info->fieldName}($val, $args, $context, $info);
            }
        ];
        //
        parent::__construct($config);
    }

    /**
     * Регистрация пользователя
     *
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     */
    public function register($rootValue, $args, AppContext $context){

        //TODO: проверка на уникальность пользователя(?) (ФИО, e-mail, phone_number)
        //TODO: капча(?)
        //TODO: анти-DDOS регистрации
        //TODO: защита от распространенных атак
        //TODO: запрет на регистрацию кириллического пароля

        $instance = new User([
            'name' => $args['name'],
            'surname' => $args['surname'],
            'midname' => $args['midname'],
            'email' => $args['email'],
            'password' => User::hashPassword($args['password']),
            'phone_number' => $args['phone_number'],
            'sex' => $args['sex'],
            'job_position' => $args['job_position'],
            'job_place' => $args['job_place'],
            'registration_address' => $args['registration_address'],
            'residence_address' => $args['residence_address'],
            'relationship_id' => 1, //TODO: поиск родителя из бд (исходя из настроек администратора)
            'study_place' => '',
            'study_class' => '',
            'date_registered' => DataSource::timeInMYSQLFormat(),
            'status_email' => 'ожидание'
        ]);

        return DataSource::insert($instance);
    }

    /**
     * Авторизация пользователя, вывод токена
     *
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return array
     * @throws RequestError
     */
    public function login($rootValue, $args, AppContext $context){

        //TODO: проверка на уникальность пользователя(?) (ФИО, e-mail, phone_number)
        //TODO: капча(?)
        //TODO: анти-DDOS авторизации
        //TODO: защита от распространенных атак
        //TODO: привязывать ли сессию к IP-адресу?

        $found = DataSource::findOne("user", "email = :username OR phone_number = :username", [
            ':username' => $args['username']
        ]);

        if($found == null || !User::validatePassword($args['password'], $found->password))
            throw new RequestError("Неверный логин или пароль");

        // Создание токена пользователя и сохранение в базу данных
        $token = Bearer::generate($context, $found);
        $token_inst = new UserToken([
            "token" => $token,
            "date_created" => DataSource::timeInMYSQLFormat(),
            "user_id" => $found->id
        ]);
        DataSource::insert($token_inst);

        return [
            'token' => $token
        ];
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws \Exception
     */
    public function logout($rootValue, $args, AppContext $context){
        $bearer = $context->getBearerOrError();

        $successful = DataSource::deleteOne("UserToken", "token = :bearer", [
            ':bearer' => $bearer
        ]);

        return $successful;
    }


    /**
     * Получение ссылки на загрузки файла
     *
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws RequestError
     * @throws \Exception
     */
    public function adminUploadTeachersList($rootValue, $args, AppContext $context){
        //TODO: проверка на права администратора

        $file = $context->getFileOrError("file0");
        $tmp_name = $file["tmp_name"];

        move_uploaded_file($tmp_name, FileStorage::getStoragePath()."/t_list.csv");

        // TODO: обработка файла, регистрация списка в базе данных
//        CSVFileHandler::scanFileByRow();

        return 'Получен файл: '.print_r($file, true);
    }


}
