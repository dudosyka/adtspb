<?php
namespace GraphQL\Application\Type;

use GraphQL\Application\Application;
use GraphQL\Application\Bearer;
use GraphQL\Application\AppContext;
use GraphQL\Application\Database\DataSource;
use GraphQL\Application\Entity\Association;
use GraphQL\Application\Entity\Cooldown;
use GraphQL\Application\Entity\PasswordRestore;
use GraphQL\Application\Entity\Proposal;
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

                // TODO: повторная отправка письма на подтверждение кодов?


                'register' => [
                    'type' => Types::boolean(),
                    'description' => 'Зарегистрировать пользователя',
                    'args' => [
                        'name' => Types::nonNull(Types::string()),
                        'surname' => Types::nonNull(Types::string()),
                        'midname' => Types::string(),
                        'email' => Types::nonNull(Types::email()),
                        'password' => Types::nonNull(Types::password()),
                        'phone_number' => Types::nonNull(Types::phoneNumber()),
                        'sex' => Types::nonNull(Types::sex()),
//                        'job_position' => Types::nonNull(Types::string()),
//                        'job_place' => Types::nonNull(Types::string()),
                        'registration_address' => Types::nonNull(Types::string()),
                        'registration_flat' => Types::nonNull(Types::string()),
                        'residence_address' => Types::nonNull(Types::string()),
                        'residence_flat' => Types::nonNull(Types::string()),
//                        'birthday' => Types::nonNull(Types::date()),
                    ]
                ],

                'registerChild' => [
                    'type' => Types::id(),
                    'description' => 'Зарегистрировать ребенка (от родителя)',
                    'args' => [
                        'relationship' => Types::nonNull(Types::string()),
                        'name' => Types::nonNull(Types::string()),
                        'surname' => Types::nonNull(Types::string()),
                        'midname' => Types::string(),
                        'sex' => Types::nonNull(Types::sex()),
                        'residence_address' => Types::nonNull(Types::string()),
                        'residence_flat' => Types::nonNull(Types::string()),
                        'study_place' => Types::nonNull(Types::string()),
                        'study_class' => Types::nonNull(Types::string()),
                        'birthday' => Types::nonNull(Types::date()),
                        'registration_address' => Types::nonNull(Types::string()),
                        'registration_flat' => Types::nonNull(Types::string()),
                        'email' => Types::email(),
                        'phone_number' => Types::phoneNumber(),
//                        'password' => Types::nonNull(Types::password()),

                        "state" => Types::nonNull(Types::string()),
                        "registration_type" => [
                            "description" => "да = постоянная, нет = временная",
                            "type" => Types::nonNull(Types::yesNo())
                        ],
                        "ovz" => Types::nonNull(Types::yesNo())
                    ]
                ],

                'selectChildAssociations' => [
                    'type' => Types::boolean(),
                    'description' => 'Выбрать ассоциацию ребенка',
                    'args' => [
                        'association_id' => Types::nonNull(Types::int()),
                        'child_id' => Types::nonNull(Types::int())
                    ]
                ],


                'validateRegistration' => [
                    'type' => Types::string(),
                    'description' => 'Проверка кода на подтверждение аккаунта после регистрации, изменение статуса аккаунта на "подтвержден" при успешной проверке',
                    'args' => [
                        "key_code" => Types::nonNull(Types::string()),
                        'email' => Types::nonNull(Types::email())
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

                "restorePasswordRequest" => [
                    'type' => Types::boolean(),
                    'description' => 'Запрос на восстановление пароля',
                    'args' => [
                        "username" => Types::nonNull(Types::string())
                    ]
                ],

                "validateCode" => [
                    "type" => Types::boolean(),
                    'description' => 'Проверка кода на запрос на восстановление пароля',
                    'args' => [
                       "key_code" => Types::nonNull(Types::string())
                    ]
                ],

                "resendCode" => [
                    "type" => Types::string(),
                    'description' => 'Повторная отправка кода регистрации',
                    'args' => [
                       "email" => Types::nonNull(Types::email())
                    ]
                ],

                "restorePasswordSaveNew" => [
                    "type" => Types::boolean(),
                    'description' => 'Проверка кода на запрос на восстановление пароля',
                    "args" => [
                        "new_password" => Types::nonNull(Types::password()),
                        "key_code" => Type::nonNull(Types::string())
                    ]
                ],
                "proposalCreatingNotification" => [
                    "type" => Types::boolean(),
                    "description" => "Уведомление о создании заявки (отправляет только 1 раз конкретному пользователю)",
                    "args" => []
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

                'adminUploadStaff' => [
                    'type' => Types::string(),
                    'description' => 'Загрузить список адм. сотрудников на сервер (файл должен быть загружен в поле file0 POST-запроса)',
                    'args' => []
                ],

                'setRecalled' => [
                    'type' => Types::boolean(),
                    'description' => 'Set proposal`s status_parent_id to "recalled"(id = 3)',
                    'args' => [
                        'child_id' => Types::int(),
                        'association_id' => Types::int()
                    ]
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

        $key_code = Application::generateValidationCode();

        // TODO: уникальность e-mailа в базе mysql сразу там прописать

        /** @var User $found */
        $found = DataSource::findOne("User", "email = :email OR phone_number = :phone_number", [
            ":email" => $args["email"],
            ":phone_number" => $args["phone_number"]
        ]);
        if($found != null)
//            throw new RequestError("Данный e-mail или телефон уже зарегистрирован в базе. Пожалуйста, введите другой e-mail или телефон.");
            throw new RequestError("Данный e-mail или телефон был указан ранее при регистрации.
Если Вы уже регистрировались, то войдите в аккаунт на <a href=\"/login\">странице авторизации</a>");









        $email = $args['email'];

        $instance = new User([
            'name' => $args['name'],
            'surname' => $args['surname'],
            'midname' => $args['midname'] ?? "",
            'email' => $email,
            'password' => User::hashPassword($args['password']),
            'phone_number' => $args['phone_number'],
            'sex' => $args['sex'],
            'job_position' => "", //$args['job_position'],
            'job_place' => "", //$args['job_place'],
            'registration_address' => $args['registration_address'],
            "registration_flat" => $args["registration_flat"],
            'residence_address' => $args['residence_address'],
            "residence_flat" => $args["residence_flat"],
            'relationship' => "",
            'study_place' => '',
            'study_class' => '',
            'date_registered' => DataSource::timeInMYSQLFormat(),
            'verification_key_email' => $key_code,
            'status_email' => User::EMAIL_PENDING,
            "birthday" => null, //$args["birthday"],

            "state" => "",
            "registration_type" => "-",
            "ovz" => "-"
        ]);

        DataSource::registerUser($instance, $context, User::PARENT);

        $this->sendRegistrationEmail($email, $key_code);

        return true;
    }

    /**
     * Регистрация пользователя
     *
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws RequestError
     * @throws \Exception
     */
    public function registerChild($rootValue, $args, AppContext $context){

        //TODO: проверка на уникальность пользователя(?) (ФИО, e-mail, phone_number)
        //TODO: капча(?)
        //TODO: анти-DDOS регистрации
        //TODO: защита от распространенных атак
        //TODO: запрет на регистрацию кириллического пароля
        //TODO: генерация особенного логина для ребенка логина
        // TODO: провека на дубликаты по ФИО и ID родителя?

        $context->viewer->hasAccessOrError(4);

        $email = $args['email'] ?? "";

        /** @var User $instance */
        $instance = new User([
            'name' => $args['name'],
            'surname' => $args['surname'],
            'midname' => $args['midname'],
            'email' => $email,
//            'password' => User::hashPassword($args['password']),
            "password" => "",
            'phone_number' => $args['phone_number'] ?? "",
            'sex' => $args['sex'],
            'job_position' => "",
            'job_place' => "",
            'registration_address' => $args['registration_address'],
            "registration_flat" => $args["registration_flat"],
            'residence_address' => $args['residence_address'],
            "residence_flat" => $args["residence_flat"],
            'relationship' => $args['relationship'],
            'study_place' => $args['study_place'],
            'study_class' => $args['study_class'], //TODO: парсер класса
            'date_registered' => DataSource::timeInMYSQLFormat(),
            'verification_key_email' => "",
            'status_email' => User::EMAIL_PENDING,
            "birthday" => $args["birthday"],

            "state" => $args["state"],
            "registration_type" => $args["registration_type"],
            "ovz" => $args["ovz"]
        ]);

        if($instance->getAge() < 6 || $instance->getAge() > 18){
            throw new RequestError("Дата рождения не соответствует возрастным ограничениям");
        }

        $id = DataSource::registerUser($instance, $context, User::CHILD);

        /*
        $html = <<<HTML
<p>Ваш код для подтверждения аккаунта:</p>
<pre>{$key_code}</pre>
HTML;
//        <p>Или Вы можете воспользоваться <a href="//lk.adtspb.ru/register/form?code={$key_code}&email={$email}">ссылкой для восстановления</a>.</p>
*/


//        Application::sendMail($email, "Подтверждение аккаунта", $html);

        return $id;
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @throws RequestError
     */
    public function resendCode($rootValue, $args, AppContext $context){
        $email = $args["email"];



        /** @var User $find */
        $find = DataSource::findOne("User", "email = :email", [
            ":email" => $email
        ]);
        if($find == null || $find->status_email != User::EMAIL_PENDING)
            throw new RequestError("Некорректный пользователь");



        /** @var Cooldown $findCooldown */
        $findCooldown = DataSource::findOne("Cooldown", "user_id = :user_id AND type = :type ORDER BY id DESC", [
            ":user_id" => $find->id,
            ":type" => "1"
        ]);
        if($findCooldown != null && strtotime($findCooldown->date_created) + 1800 >= strtotime("now"))
            throw new RequestError("Перед повторной отправкой запроса, пожалуйста, подождите 30 минут.");



        $key_code = Application::generateValidationCode();

        $find->verification_key_email = $key_code;
        DataSource::update($find);

        $this->sendRegistrationEmail($email, $key_code);



        DataSource::insert(new Cooldown([
            "user_id" => $find->id,
            "type" => "1",
            "date_created" => DataSource::timeInMYSQLFormat()
        ]));

        return "Код подтверждения отправлен повторно.";
    }


    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws RequestError
     */
    public function selectChildAssociations($rootValue, $args, AppContext $context){
        // Есть ли доступ
        $context->viewer->hasAccessOrError(7);

        // Есть ли такая ассоциация
        /** @var Association $findAssoc */
        $findAssoc = DataSource::find("Association", $args["association_id"]);
        if($findAssoc == null)
            throw new RequestError("Объединение не найдено!");

        // Есть ли такой ребенок у такого родителя
        $findChild = DataSource::findOne("UserChild", "parent_id = :parent_id AND child_id = :child_id", [
            "parent_id" => $context->viewer->id,
            "child_id" => $args["child_id"]
        ]);
        if($findChild == null)
            throw new RequestError("Неверно задан id ребенка");

        // Было ли уже подано заявление
        $findProposal = DataSource::findOne("Proposal", "parent_id = :parent_id AND child_id = :child_id AND association_id = :association_id", [
            "parent_id" => $context->viewer->id,
            "child_id" => $args["child_id"],
            "association_id" => $args["association_id"]
        ]);
        if($findProposal != null){

            /** @var User $child */
            /*
            $child = DataSource::find("User", $args["child_id"]);

            $name = $child->name;
            $surname = $child->surname;
            $midname = $child->midname;

            $full_name = "{$name} {$surname}";
            if($midname != "") $full_name .= " {$midname}";

            $title = $findAssoc->name;

            throw new RequestError("Заявление в объединение {$title} на {$full_name} уже подано");
            */
            // Ничего не говорим, всё ок.
            return true;
        }



        // Проверка на количество часов
        // TODO: оптимизировать механизм проверки количества часов
        $findAllProps = DataSource::findAll("Proposal", "parent_id = :parent_id AND child_id = :child_id", [
            "parent_id" => $context->viewer->id,
            "child_id" => $args["child_id"]
        ]);
        if($findAllProps != null){
            $hours = 0;
            foreach($findAllProps as $prop){
                /** @var Proposal $prop */
                /** @var Association $info */
                $info = DataSource::find("Association", $prop->association_id);
                $hours += $info->study_hours_week;
                if($hours >= 10) break;
            }
            if($hours >= 10)
                throw new RequestError("Загруженность ребенка превышает 10 часов");
        }






        DataSource::insert(new Proposal([
            "timestamp" => DataSource::timeInMYSQLFormat(),
            'child_id' => $args["child_id"],
            'parent_id' => $context->viewer->id,
            'association_id' => $args["association_id"],
            'status_admin_id' => 1, //TODO: константы статусов ожидания запроса?
            'status_parent_id' => 4, //TODO: константы статусов ожидания запроса?
            'status_teacher_id' => 1 //TODO: константы статусов ожидания запроса?
        ]));

        return true;
    }


    /**
     * Проверка кода на подтверждение аккаунта после регистрации
     *
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return \Lcobucci\JWT\Token
     * @throws RequestError
     */
    public function validateRegistration($rootValue, $args, AppContext $context){
        //TODO: кулдаун 30 минут?
        //TODO: умножающийся кулдаун (в 1й раз 30 минут, во 2й раз 1 (30мин*2) час, в 3й раз 2(1*2) часа, ...)?
        //TODO: анти-ддос, анти-брутфорс

        /** @var User $found */
        $found = DataSource::findOne("User", "email = :email AND verification_key_email = :key", [
            ":email" => $args["email"],
            ':key' => $args['key_code']
        ]);
        if($found == null ||
            $found->status_email == User::EMAIL_VALIDATED || // E-mail уже подтвержден
            $found->verification_key_email == null ||
            $found->verification_key_email == ""
        )
            throw new RequestError("Неверный код");

        $found->status_email = User::EMAIL_VALIDATED;
        $found->verification_key_email = "";
        DataSource::update($found);

        return $found->generateLoginToken($context);
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
        //TODO: редирект на добавление детей у регистрации, если пользователь их еще не добавлял

        /** @var User $found */
        $found = DataSource::findOne("user", "email = :username OR phone_number = :username OR login = :username", [
            ':username' => $args['username']
        ]);

        if($found == null || $found->password == "" || $found->email == "" || $found->phone_number == "" || !User::validatePassword($args['password'], $found->password))
            throw new RequestError("Неверный логин или пароль");

//        $found->status_email = User::EMAIL_VALIDATED;

//         if(!$found->hasAccess(10)){
//             throw new RequestError("Личный кабинет обучающегося в настоящее время находится в разработке ".print_r($found->hasAccess(10), true));
//         }

        // Создание токена пользователя и сохранение в базу данных
        return [
            'token' => $found->generateLoginToken($context)
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
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws \Exception
     */
    public function restorePasswordRequest($rootValue, $args, AppContext $context){
        //TODO: кулдаун 30 минут?
        //TODO: умножающийся кулдаун (в 1й раз 30 минут, во 2й раз 1 (30мин*2) час, в 3й раз 2(1*2) часа, ...)?
        //TODO: удалять больше 10 записей о восстановлении пароля у пользователя
        //TODO: запрет на изменение пароля непроверненного пользователя (у которого не подтверждены email или другие данные)
        //TODO: анти-брутфорс

        // Поиск пользователя
        /** @var User $found */
        $found = DataSource::findOne("user", "email = :username OR phone_number = :username OR login = :username", [
            ':username' => $args['username']
        ]);
        if($found == null)
            throw new RequestError("Неверный логин, e-mail или телефон");

        $email = $found->email;
        $key_code = Application::generateValidationCode();

        $insert_request = DataSource::insert(new PasswordRestore([
            "user_id" => $found->id,
            "key_code" => $key_code,
            "ip" => $context->ip,
            "date_created" => DataSource::timeInMYSQLFormat()
        ]));

        if(!$insert_request)
            throw new \Exception("Ошибка создания запроса на восстановление пароля: невозможно записать запрос в базу");


        $html = <<<HTML
<p>Ваш код для восстановления пароля:</p>
<pre>{$key_code}</pre>
<p>Или Вы можете воспользоваться <a href="//lk.adtspb.ru/login/restore-password?username={$email}&key_code={$key_code}">ссылкой для восстановления</a>.</p>
HTML;

        Application::sendMail($email, "Восстановление пароля", $html);



        return $insert_request;
    }
    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     */
    public function proposalCreatingNotification($rootValue, $args, AppContext $context)
    {
        $found = DataSource::findOne("user", "id= :id", [":id" => $context->viewer->id]);

        if($found == null)
            throw new RequestError("Неверный логин, e-mail или телефон");
        if($found->proposal_notify == 1)
            return;

        $found->proposal_notify = 1;
        DataSource::update($found);

        $email = $found->email;

        $html = "
        Регистрация успешно пройдена.<br>
        Ваше заявление принято к рассмотрению.<br>
        Очный (обязательный) прием заявлений пройдет с 24 по 31 августа 2020 года в ГБНОУ Академии цифровых технологий по адресу: Санкт-Петербург, Большой проспект П.С., д.29/2 (ориентир черные ворота).<br>";

        Application::sendMail($email, "Ваша заявка была получена", $html);

        return true;
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws RequestError
     */
    public function restorePasswordSaveNew($rootValue, $args, AppContext $context) {
        //TODO: кулдаун 30 минут?
        //TODO: умножающийся кулдаун (в 1й раз 30 минут, во 2й раз 1 (30мин*2) час, в 3й раз 2(1*2) часа, ...)?
        //TODO: удалять больше 10 записей о восстановлении пароля у пользователя
        //TODO: анти-ддос
        //TODO: запрет на изменение пароля непроверненного пользователя (у которого не подтверждены email или другие данные)

        /** @var PasswordRestore $found */
        $found = DataSource::findOne("PasswordRestore", "key_code = :key", [
            ':key' => $args['key_code']
        ]);
        if($found == null)
            throw new RequestError("Неверный код подтверждения");

        /** @var User $user */
        $user = DataSource::findOne("User", "id = :user", [
            ':user' => $found->user_id
        ]);
        $user->password = User::hashPassword($args["new_password"]);
        $res = DataSource::update($user);

        // Очистка записи запроса
        DataSource::delete('PasswordRestore', $found->id);

        return true;
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     */
    public function validateCode($rootValue, $args, AppContext $context){
        //TODO: кулдаун 30 минут?
        //TODO: умножающийся кулдаун (в 1й раз 30 минут, во 2й раз 1 (30мин*2) час, в 3й раз 2(1*2) часа, ...)?
        //TODO: удалять больше 10 записей о восстановлении пароля у пользователя
        //TODO: анти-ддос
        //TODO: запрет на изменение пароля непроверненного пользователя (у которого не подтверждены email или другие данные)

        /** @var PasswordRestore $found */
        $found = DataSource::findOne("PasswordRestore", "key_code = :key", [
            ':key' => $args['key_code']
        ]);
        if($found == null)
            return false;

        return true;
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
        $context->viewer->hasAccessOrError(1); //TODO: adminUploadAssociations action_id в константу
        $file = $context->getFileOrError("file0"); //TODO: биндинг файла через graphql

        throw new RequestError("TODO");

        /*
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
        */

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
        //TODO: проверить
        $context->viewer->hasAccessOrError(2); //TODO: adminUploadTeachersList action_id в константу
        $file = $context->getFileOrError("file0"); //TODO: биндинг файла через graphql

        if(!$file->isUTF8())
            throw new RequestError("Неверная кодировка файла: файл должен иметь кодировку UTF-8.");

        $registered = [];

        CSVFileHandler::scanFileByRow($file, function($line_index, $data) use(&$registered, &$context){

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
                'midname' => $midname ?? "",
                'email' => $data[2],
                'password' => User::hashPassword($password),
                'phone_number' => "", // TODO: регистрация номера телефона у педагога
                'sex' => "м",  // Временно! // TODO: регистрация пола у педагога
                'job_position' => $data[2],
                'job_place' => "", // TODO: регистрация места работы у педагога
                'registration_address' => "", // TODO: регистрация адреса регистрации у педагога
                'residence_address' => "", // TODO: регистрация адреса проживания у педагога
                'relationship' => "", // TODO: регистрация роли у педагога
                'study_place' => '',
                'study_class' => '',
                'date_registered' => DataSource::timeInMYSQLFormat(),
                'status_email' => 'подтвержден'
            ]);

            DataSource::registerUser($instance, $context, User::TEACHER);

            $registered[] = "Педагог {$name} {$surname} {$midname} зарегистрирован. Пароль: {$password}";
        });

        return implode("\n", $registered);
    }


    /**
     * Загрузка списка административных сотрудников в базу данных
     *
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws RequestError
     * @throws \Exception
     */
    public function adminUploadStaff($rootValue, $args, AppContext $context){
        //TODO: проверить
        $context->viewer->hasAccessOrError(2); //TODO: отдельное право
        $file = $context->getFileOrError("file0"); //TODO: биндинг файла через graphql

        if(!$file->isUTF8())
            throw new RequestError("Неверная кодировка файла: файл должен иметь кодировку UTF-8.");

        $registered = [];

        CSVFileHandler::scanFileByRow($file, function($line_index, $data) use(&$registered, &$context){

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

            // TODO: регистрация прав у адм. сотрудника

            $instance = new User([
                'name' => $name,
                'surname' => $surname,
                'midname' => $midname ?? "",
                'email' => $data[2],
                'password' => User::hashPassword($password),
                'phone_number' => "", // TODO: регистрация номера телефона у адм. сотрудника
                'sex' => "м",  // Временно! // TODO: регистрация пола у адм. сотрудника
                'job_position' => "",
                'job_place' => "", // TODO: регистрация места работы у адм. сотрудника
                'registration_address' => "", // TODO: регистрация адреса регистрации у адм. сотрудника
                'residence_address' => "", // TODO: регистрация адреса проживания у адм. сотрудника
                'relationship' => "", // TODO: регистрация роли у адм. сотрудника
                'study_place' => '',
                'study_class' => '',
                'date_registered' => DataSource::timeInMYSQLFormat(),
                'status_email' => 'подтвержден'
            ]);

            DataSource::registerUser($instance, $context, User::TEACHER); //TODO: новая роль

            $registered[] = "Сотрудник {$name} {$surname} {$midname} зарегистрирован. Пароль: {$password}";
        });

        return implode("\n", $registered);
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws RequestError
     */
    public function setRecalled($rootValue, $args, AppContext $context)
    {
        $context->viewer->hasAccessOrError(9);

        $child = $args['child_id'];
        $association_id = $args['association_id'];

        $find = DataSource::findOne("UserChild", "parent_id = :parent_id AND child_id = :child_id",
            [
               "child_id" => $child,
               "parent_id" => $context->viewer->id
            ]
        );

        if($find == null)
            throw new RequestError("Доступ запрещен");

        $proposal = DataSource::findOne("Proposal", "association_id = :association_id AND child_id = :child_id",
            [
                'child_id' => $child,
                'association_id' => $association_id
            ]
        );

       if ($proposal == null)
            return false;
       else
       {
           $proposal->status_parent_id = 3;
           DataSource::update($proposal);
           return true;
       }
    }


    public function sendRegistrationEmail(string $email, string $code){
        $html = <<<HTML
<p>Ваш код для подтверждения аккаунта:</p>
<pre>{$code}</pre>
HTML;
//        <p>Или Вы можете воспользоваться <a href="//lk.adtspb.ru/register/form?code={$key_code}&email={$email}">ссылкой для восстановления</a>.</p>

        //TODO: реализовать переход по ссылке


        Application::sendMail($email, "Подтверждение аккаунта", $html);
    }



}
