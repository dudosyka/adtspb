<?php
namespace GraphQL\Application\Type;

use GraphQL\Application\Application;
use GraphQL\Application\Bearer;
use GraphQL\Application\AppContext;
use GraphQL\Application\Database\DataSource;
use GraphQL\Application\Entity\Association;
use GraphQL\Application\Entity\Upload;
use GraphQL\Application\Entity\User;
use GraphQL\Application\Entity\UserToken;
use GraphQL\Application\Entity\Group;
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

                'adminUploadAssociations' => [
                    'type' => Types::string(),
                    'description' => 'Загрузить список объединений на сервер (файл должен быть загружен в поле file0 POST-запроса)',
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
     * @throws RequestError
     */
    public function register($rootValue, $args, AppContext $context){

        //TODO: проверка на уникальность пользователя(?) (ФИО, e-mail, phone_number)
        //TODO: капча(?)
        //TODO: анти-DDOS регистрации
        //TODO: защита от распространенных атак
        //TODO: запрет на регистрацию кириллического пароля
        //TODO: генерация логина


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

        return DataSource::registerUser($instance);
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
     * Загрузка списка объединений в базу данных
     *
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws RequestError
     * @throws \Exception
     */
    public function adminUploadAssociations($rootValue, $args, AppContext $context){
        //TODO: проверка на права администратора

        $file = $context->getFileOrError("file0");

        if(!$file->isUTF8())
            throw new RequestError("Неверная кодировка файла: файл должен иметь кодировку UTF-8.");

        CSVFileHandler::scanFileByRow($file, function($line_index, $data){
            // Игнорируем пустые линии
            if(count($data) <= 1 && trim($data[0]) == "") return; //TODO: игнорирование пустых строк в классе обработчика CSV?

            if (
                (count($data) < 6) or
                (!isset($data[0]) || !isset($data[1]) || !isset($data[2]) || !isset($data[3]) || !isset($data[4]) || !isset($data[5])) // or
                // TODO: проверка на значения (парсятся ли они)
//                (!is_string($data[0]) || !is_int($data[1]) || !is_int($data[2]) || !is_int($data[3]) || !is_int($data[4]) || !is_string($data[5]))
            )
                throw new RequestError("Файл имеет неверный формат");

            $association_name = $data[0];
            $min_age = $data[1];
            $max_age = $data[2];
            $years_study = $data[3];
            $hours_per_year = $data[4];
            $study_hours_week = 0; // TODO: реализация подсчета часов обучения в неделю

            $association = new Association([
                "name" => $association_name,
                "min_age" => $min_age,
                "max_age" => $max_age,
                "study_years" => $years_study,
                "study_hours" => $hours_per_year,
                "study_hours_week" => $study_hours_week
            ]);

            DataSource::insert($association);


            // TODO: не применять изменения, если произошла ошибка
            // TODO: DataSource::insert вывод id?
            // TODO: оптимизировать
            $association = DataSource::findOne("Association",
                "name = :name AND min_age = :min_age AND max_age = :max_age AND study_years = :study_years AND study_hours = :study_hours AND study_hours_week = :study_hours_week",
                [
                    "name" => $association_name,
                    "min_age" => $min_age,
                    "max_age" => $max_age,
                    "study_years" => $years_study,
                    "study_hours" => $hours_per_year,
                    "study_hours_week" => $study_hours_week
                ]
            );

            for($i = 5; $i < count($data); $i++){

                if(trim($data[$i]) == "") continue;

                $full_name_data = explode(" ", $data[$i]);

                $surname = trim($full_name_data[0], "\r\n!@#$%^&*()\"№;%:?',;{}/\\. ");
                $name = trim($full_name_data[1], "\r\n!@#$%^&*()\"№;%:?',;{}/\\. ");
                $midname = trim($full_name_data[2], "\r\n!@#$%^&*()\"№;%:?',;{}/\\. ");

                // TODO: фикс проверки в бд

                $user = DataSource::findOne("User", "name = :name AND surname = :surname AND midname = :midname", [
                    "name" => $name,
                    "surname" => $surname,
                    "midname" => $midname
                ]);

                if($user == null)
                    // TODO: безопасный вывод?
                    throw new RequestError("Педагог '{$surname} {$name} {$midname}' не найден в базе данных");

                $group = new Group([
                    "association_id" => $association->id,
                    "teacher_id" => $user->id
                ]);

                DataSource::insert($group);
            }
        });

        return true;
    }


    /**
     * Загрузка списка педагогов в базу данных
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

        if(!$file->isUTF8())
            throw new RequestError("Неверная кодировка файла: файл должен иметь кодировку UTF-8.");

        $registered = [];

        CSVFileHandler::scanFileByRow($file, function($line_index, $data) use(&$registered){

            // Игнорируем пустые линии
            if(count($data) <= 1 && trim($data[0]) == "") return; //TODO: игнорирование пустых строк в классе обработчика CSV?

            if (
                (count($data) != 3) or
                (!isset($data[0]) || !isset($data[1]) || !isset($data[2])) // or
                // TODO: проверка на значения (парсятся ли они)
//                (!is_string($data[0]) || !is_string($data[1]) || !is_string($data[2]))
            )
                throw new RequestError("Файл имеет неверный формат");


            $_ = explode(" ", $data[0]);

            $surname = trim($_[0], "\r\n!@#$%^&*()\"№;%:?',;{}/\\. ");
            $name = trim($_[1], "\r\n!@#$%^&*()\"№;%:?',;{}/\\. ");
            $midname = trim($_[2], "\r\n!@#$%^&*()\"№;%:?',;{}/\\. ");

            $password = Application::getRandomString();

            // TODO: регистрация прав у педагога

            $instance = new User([
                'name' => $name,
                'surname' => $surname,
                'midname' => $midname,
                'email' => $data[2],
                'password' => User::hashPassword($password),
                'phone_number' => "", // TODO: регистрация номера телефона у педагога
                'sex' => "м",  // Временно! // TODO: регистрация пола у педагога
                'job_position' => $data[2],
                'job_place' => "", // TODO: регистрация места работы у педагога
                'registration_address' => "", // TODO: регистрация адреса регистрации у педагога
                'residence_address' => "", // TODO: регистрация адреса проживания у педагога
                'relationship_id' => 1, // TODO: регистрация роли у педагога
                'study_place' => '',
                'study_class' => '',
                'date_registered' => DataSource::timeInMYSQLFormat(),
                'status_email' => 'подтвержден'
            ]);

            DataSource::registerUser($instance);

            $registered[] = "Педагог {$name} {$surname} {$midname} зарегистрирован. Пароль: {$password}";
        });

        return implode("\n", $registered);
    }


}
