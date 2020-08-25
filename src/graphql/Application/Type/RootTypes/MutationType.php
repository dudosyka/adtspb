<?php
namespace GraphQL\Application\Type;

use Com\Tecnick\Pdf\Encrypt\Data;
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
                        'child_id' => Types::nonNull(Types::int()),
                        'token' => Types::int()
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

                'adminLoadStatistic' => [
                    'type' => Types::string(),
                    'description' => 'Return JSON contains proposal and user statistic',
                    'args' => []
                ],

                'adminSwitchAssociationHidden' => [
                    'type' => Types::boolean(),
                    'description' => 'Switch association state before hidden / not hidden by id',
                    'args' => [
                        'id' => Types::nonNull(Types::int()),
                        'token' => Types::string()
                    ]
                ],

                'adminSwitchAssociationClosed' => [
                    'type' => Types::boolean(),
                    'description' => 'Switch association state before closed / not closed for join by id',
                    'args' => [
                        'id' => Types::nonNull(Types::int())
                    ]
                ],

                'adminSetNewTokenForHiddenAssociation' => [
                    'type' => Types::boolean(),
                    'description' => 'Changed auto-generated token for hidden association by id',
                    'args' => [
                        'id' => Types::nonNull(Types::int()),
                        'token' => Types::nonNull(Types::string())
                    ]
                ],

                'adminCreateAssociation' => [
                    'type' => Types::boolean(),
                    'description' => 'Create new association',
                    'args' => [
                        'name' => Types::string(),
                        'min_age' => Types::nonNull(Types::int()),
                        'max_age' => Types::nonNull(Types::int()),
                        'study_hours' => Types::nonNull(Types::int()),
                        'study_years' => Types::nonNull(Types::int()),
                        'study_hours_week' => Types::nonNull(Types::int()),
                        'group_count' => Types::nonNull(Types::int()),
                        'description' => Types::string(),
                        'isClosed' => Types::nonNull(Types::int()),
                        'isHidden' => Types::nonNull(Types::int())
                    ]
                ],

                'adminChangeProposalStatus' => [
                    'type' => Types::boolean(),
                    'description' => 'Change proposal`s status_admin_id by id',
                    'args' => [
                        'id' => Types::nonNull(Types::int()),
                        'status_admin_id' => Types::nonNull(Types::int()),
                        'comment' => Types::string()
                    ]
                ],

                'teacherChangeProposalStatus' => [
                    'type' => Types::boolean(),
                    'description' => 'Change proposal`s status_teacher_id by id',
                    'args' => [
                        'id' => Types::nonNull(Types::int()),
                        'status' => Types::nonNull(Types::int()),
                    ]
                ],

                'adminEditUserData' => [
                    'type' => Types::boolean(),
                    'description' => 'Edit user`s data by id',
                    'args' => [
                        'id' => Types::nonNull(Types::int()),
                        'name' => Types::nonNull(Types::string()),
                        'surname' => Types::nonNull(Types::string()),
                        'midname' => Types::string(),
                        'email' => Types::string(), //string() вместо email() Иначе пустой не проходит
                        'birthday' => Types::string(), // string() аналогично
                        'phone_number' => Types::string(), //string() аналогично
                        'sex' => Types::nonNull(Types::sex()),
                        'registration_address' => Types::nonNull(Types::string()),
                        'registration_flat' => Types::nonNull(Types::string()),
                        'residence_address' => Types::nonNull(Types::string()),
                        'residence_flat' => Types::nonNull(Types::string()),
                        'study_class' => Types::string(),
                        'study_place' => Types::string()
                    ]
                ],

                'adminSelectChildAssociations' => [
                    'type' => Types::boolean(),
                    'description' => 'Выбрать ассоциацию ребенка',
                    'args' => [
                        'association_id' => Types::nonNull(Types::int()),
                        'child_id' => Types::nonNull(Types::int()),
                        'parent_id' => Types::nonNull(Types::int()),
                        'token' => Types::int()
                    ]
                ],

                'adminCheckChildLoad' => [
                    'type' => Types::int(),
                    'description' => '',
                    'args' => [
                        'child_id' => Types::nonNull(Types::int()),
                        'parent_id' => Types::nonNull(Types::int()),
                        'association_id' => Types::int(),
                    ]
                ],

                'setRecalled' => [
                    'type' => Types::boolean(),
                    'description' => 'Set proposal`s status_parent_id to "recalled"(id = 3)',
                    'args' => [
                        'child_id' => Types::int(),
                        'association_id' => Types::int()
                    ]
                ],

                'checkAssociationSpecialToken' => [
                    'type' => Types::int(),
                    'description' => 'Check if token exists else return RequestError',
                    'args' => [
                        'token' => Types::int()
                    ]
                ],

                'getViewerRights' => [
                    'type' => Types::string(),
                    'description' => 'Return json string which contains current viewer rights',
                    'args' => [
                        'action_list_id' => Types::int()
                    ]
                ],

                'getAssociationDetails' => [
                    'type' => Types::string(),
                    'description' => 'Return json string which contains detail statistic for association by id',
                    'args' => [
                        'id' => Types::int()
                    ]
                ]

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
     * @noinspection PhpUndefinedMethodInspection
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
        $findProposal = DataSource::findOne("Proposal", "parent_id = :parent_id AND child_id = :child_id AND association_id = :association_id ORDER BY `id` DESC", [
            "parent_id" => $context->viewer->id,
            "child_id" => $args["child_id"],
            "association_id" => $args["association_id"]
        ]);

        if($findProposal != null)
        {

            if ($findProposal->status_parent_id == 4)
            {
                $child = DataSource::find("User", $args["child_id"]);

                $name = $child->name;
                $surname = $child->surname;
                $midname = $child->midname;

                $full_name = "{$name} {$surname}";
                if($midname != "") $full_name .= " {$midname}";

                $title = $findAssoc->name;

                throw new RequestError("Заявление в объединение {$title} на {$full_name} уже подано");
            }
        }

        // Проверка на количество часов
        $parent_id = $context->viewer->id;
        $child_id = $args['child_id'];
        $association_id = $args['association_id'];
        if ($this->checkChildLoad($parent_id, $child_id) == -1)
            throw new RequestError("Загруженность ребенка превышает 10 часов");

        $this->createProposal($child_id, $parent_id, $association_id);

        return true;
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws RequestError
     */
    public function adminSelectChildAssociations($rootValue, $args, AppContext $context){
        // Есть ли доступ
        $context->viewer->hasAccessOrError(7);

        // Есть ли такая ассоциация
        /** @var Association $findAssoc */
        $findAssoc = DataSource::find("Association", $args["association_id"]);
        if($findAssoc == null)
            throw new RequestError("Объединение не найдено!");

        // Есть ли такой ребенок у такого родителя
        $findChild = DataSource::findOne("UserChild", "parent_id = :parent_id AND child_id = :child_id", [
            "parent_id" => $args['parent_id'],
            "child_id" => $args["child_id"]
        ]);
        if($findChild == null)
            throw new RequestError("Неверно задан id ребенка");

        // Было ли уже подано заявление
        $findProposal = DataSource::findOne("Proposal", "parent_id = :parent_id AND child_id = :child_id AND association_id = :association_id ORDER BY `id` DESC", [
            "parent_id" => $args['parent_id'],
            "child_id" => $args["child_id"],
            "association_id" => $args["association_id"]
        ]);

        if($findProposal != null)
        {

            if ($findProposal->status_parent_id == 4)
            {
                $child = DataSource::find("User", $args["child_id"]);

                $name = $child->name;
                $surname = $child->surname;
                $midname = $child->midname;

                $full_name = "{$name} {$surname}";
                if($midname != "") $full_name .= " {$midname}";

                $title = $findAssoc->name;

                throw new RequestError("Заявление в объединение {$title} на {$full_name} уже подано");
            }
        }

        $parent_id = $args['parent_id'];
        $child_id = $args['child_id'];
        $association_id = $args['association_id'];

        //Смотрим, если группы уже заполенны, то помечаем заявление как "резерв"
        $broughtCounter = count(DataSource::findAll("Proposal", "`association_id` = :id, `status_admin_id=6`", [':id' => $findProposal->association_id]));
        $max = $findAssoc->group_count * 20;
        if ($broughtCounter >= $max)
            $this->createProposal($child_id, $parent_id, $association_id, 1);
        else
            $this->createProposal($child_id, $parent_id, $association_id);

        return true;
    }

    public function adminCheckChildLoad($rootValue, $args, AppContext $context)
    {
        $hours = 0;
        $findAllProps = DataSource::findAll("Proposal", "parent_id = :parent_id AND child_id = :child_id", [
            "parent_id" => $args['parent_id'],
            "child_id" => $args['child_id']
        ]);
        if($findAllProps != null){
            $child = DataSource::find("User", $args['child_id']);
            $maxHours = 10;
            if ($child->getAge() >= 14)
                $maxHours = 12;
            foreach($findAllProps as $prop){
                /** @var Proposal $prop */
                /** @var Association $info */
                if ($prop->status_parent_id == 3)
                    continue;
                $info = DataSource::find("Association", $prop->association_id);
                $hours += $info->study_hours_week;
                if($hours >= $maxHours) break;
            }
        }
        if (isset($args['association_id']))
        {
            $hours += DataSource::findOne("Association", "id = :id", [':id' => $args['association_id']])->study_hours_week;
        }
        return $hours;
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
     * @return string
     * @throws RequestError
     */
    public function adminLoadStatistic($rootValue, $args, AppContext $context)
    {
        $context->viewer->hasAccessOrError(12);

        $query = DataSource::_query("SELECT COUNT(DISTINCT(`parent_id`)) AS parents FROM `user_child` WHERE 1");
        $parent_statistic = $query[0]->parents;

        $query = DataSource::_query("SELECT COUNT(DISTINCT(`child_id`)) AS children FROM `user_child` WHERE 1");
        $children_statistic = $query[0]->children;

        $result = [
            'proposal_statistic' => DataSource::_query("
                   SELECT association.id                         as id,
                   association.name                              AS `association_name`,
                   COUNT(*)                                      AS `allProposalCount`, 
                   SUM(proposal.status_admin_id = 6)             AS `brought`,
                   association.group_count                       AS `association_group_count`, 
                   association.group_count*20                    AS `planned_numbers`, 
                   COUNT(*) - SUM(proposal.status_parent_id = 3) AS `fact_numbers`, 
                   (100*(COUNT(*) - SUM(proposal.status_parent_id = 3))div(association.group_count*20)) AS `fullness_percent`, 
                   association.isHidden                          AS `special`,
                   association.isCLosed                          AS `isClosed`
                   FROM proposal RIGHT JOIN association ON association.id = proposal.association_id GROUP BY association.id"),
            'parent_statistic' => $parent_statistic,
            'child_statistic' => $children_statistic,
        ];
        $res = json_encode($result, JSON_UNESCAPED_UNICODE);
        return $res ? $res : "";
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws RequestError
     */
    public function adminSwitchAssociationClosed($rootValue, $args, AppContext $context)
    {
        $context->viewer->hasAccessOrError(11);

        $association = DataSource::findOne("Association", "id = :id", [":id" => $args['id']]);
        if ($association == null)
            return false;

        $association->isClosed = $association->isClosed == 1 ? 0 : 1;

        return DataSource::update($association);
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws RequestError
     */
    public function adminSwitchAssociationHidden($rootValue, $args, AppContext $context)
    {
        $context->viewer->hasAccessOrError(11);

        $association = DataSource::findOne("Association", "id = :id", [":id" => $args['id']]);
        if ($association == null)
            return false;

        $association->isHidden = $association->isHidden == 0 ? !isset($args['token']) ? time() : $args['token'] : 0;

        return DataSource::update($association);
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws RequestError
     */
    public function adminSetNewTokenForHiddenAssociation($rootValue, $args, AppContext $context)
    {
        $association = DataSource::findOne("Association", "id = :id", [':id' => $args['id']]);

        if ($association == null)
            return false;

        if ($association->isHidden == 0)
            return false;

        $association->isHidden = $args['token'];

        return DataSource::update($association);
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return string
     * @throws RequestError
     */
    public function adminCreateAssociation($rootValue, $args, AppContext $context)
    {
        $context->viewer->hasAccessOrError(14);

        if ($args['isHidden'] == 1)
            $args['isHidden'] = time();

        return DataSource::insert(new Association($args));
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws RequestError
     */
    public function adminChangeProposalStatus($rootValue, $args, AppContext $context)
    {
        $context->viewer->hasAccessOrError(16);

        //checking if proposal exists
        $proposal = DataSource::findOne("Proposal", "id = :id", [':id' => $args['id']]);

        if ($proposal == null)
            throw new RequestError("Proposal not found");

        //checking if status valid
        $status = DataSource::findOne("SettingsProposal", "id = :id", ['id' => $args['status_admin_id']]);

        if ($status == null)
            throw new RequestError("Invalid status");

        //checking if status = 7 (reject) we must set a reject reason
        if ($status->id == 7 && (!isset($args['comment']) || $args['comment'] == ""))
            throw new RequestError("When rejecting the proposal must set reject reason4");
        if ($status->id == 7)
            $proposal->reject_reason = $args['comment'];

        //checking if we rollback proposal to the "waiting" state, we also must empty it`s reject reason
        if ($status->id == 1)
            $proposal->reject_reason = "";

        $proposal->status_admin_id = $status->id;

        return DataSource::update($proposal);
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws RequestError
     */
    public function teacherChangeProposalStatus($rootValue, $args, AppContext $context)
    {
        $context->viewer->hasAccessOrError(16);

        //checking if proposal exists
        $proposal = DataSource::findOne("Proposal", "id = :id", [':id' => $args['id']]);

        if ($proposal == null)
            throw new RequestError("Proposal not found");

        //checking if status valid
        $status = DataSource::findOne("SettingsProposal", "id = :id", ['id' => $args['status']]);

        if ($status == null)
            throw new RequestError("Invalid status");

        $proposal->status_teacher_id = $status->id;

        return DataSource::update($proposal);
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return bool
     * @throws RequestError
     */
    public function adminEditUserData($rootValue, $args, AppContext $context)
    {
        $context->viewer->hasAccessOrError(16);

        $user = DataSource::findOne("User", "id = :id", [':id' => $args['id']]);

        if ($user == null)
            throw new RequestError("User not found");

        unset($args['id']);

        return DataSource::update(new User(array_merge($user->asArray(), $args)));
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

        $proposal = DataSource::findOne("Proposal", "association_id = :association_id AND child_id = :child_id ORDER BY `proposal`.`id` DESC",
            [
                'child_id' => $child,
                'association_id' => $association_id
            ]
        );

       if ($proposal == null)
           throw new RequestError("Заявление не найдено.");
       else
       {
           $proposal->status_parent_id = 3;
           DataSource::update($proposal);
           return true;
       }
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return mixed
     * @throws RequestError
     */
    public function checkAssociationSpecialToken($rootValue, $args, AppContext $context)
    {
        $context->viewer->hasAccessOrError(7);

        $token = (int)$args['token'];

        $res = DataSource::findOne("Association", "isHidden = :token", ['token'=>$token]);

        if ($res == null)
            throw new RequestError("Объединение не найдено");

        return $res->association_id;
    }

    public function getViewerRights($rootValue, $args, AppContext $context)
    {
        $actions = [];
        $user_role = DataSource::findAll("UserRole", "user_id = :uid", ["uid" => $context->viewer->id]);
        foreach ($user_role as $i){
            /** @var UserRole $i */

            /** @var ActionList $action_data */
            $action_data = DataSource::findAll("ActionList", "role_id = :rid AND list_id = :lid", [
                "rid" => $i->role_id,
                "lid" => $args['action_list_id']
            ]);
            if($action_data != null)
                foreach ($action_data as $action)
                {
                    if($action->sign == "+")
                        $actions[] = $action->action_id;
                }
        }
        return json_encode($actions, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $rootValue
     * @param $args
     * @param AppContext $context
     * @return string
     * @throws RequestError
     */
    public function getAssociationDetails($rootValue, $args, AppContext $context)
    {
        $context->viewer->hasAccessOrError(12);

        $association = DataSource::findOne("Association", 'id = :id', [':id' => $args['id']]);
        if ($association == null)
            throw new RequestError("Association with id ".$args['id']."not found");

        $res = json_encode(DataSource::_query("
        SELECT 
        association.id as association_id,
        proposal.id as id,
        child.name AS child_name, 
        child.midname AS child_midname, 
        child.surname AS child_surname, 
        child.email as child_email, 
        child.phone_number as child_phone,
        child.birthday as child_birthday,
        parent.name AS parent_name, 
        parent.midname AS parent_midname, 
        parent.surname AS parent_surname, 
        parent.email as parent_email, 
        parent.phone_number as parent_phone, 
        association.id AS association_id, 
        association.name AS association_name, 
        proposal.timestamp, parent_s.name AS status_parent, 
        admin_s.name AS status_admin, teacher_s.name AS status_teacher 
        FROM proposal INNER JOIN association ON association.id = proposal.association_id INNER JOIN user AS child ON proposal.child_id = child.id INNER JOIN user AS parent ON proposal.parent_id = parent.id INNER JOIN settings_proposal AS parent_s ON proposal.status_parent_id = parent_s.id INNER JOIN settings_proposal AS admin_s ON proposal.status_admin_id = admin_s.id INNER JOIN settings_proposal AS teacher_s ON proposal.status_teacher_id = teacher_s.id WHERE proposal.association_id = :id", [':id' => $args['id']]), JSON_UNESCAPED_UNICODE);

        return $res ? $res : "";
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

    /**
     * @param int $parent_id
     * @param int $child_id
     * @return bool
     */
    public function checkChildLoad(int $parent_id, int $child_id)
    {
        $findAllProps = DataSource::findAll("Proposal", "parent_id = :parent_id AND child_id = :child_id", [
            "parent_id" => $parent_id,
            "child_id" => $child_id
        ]);
        if($findAllProps != null){
            $child = DataSource::find("User", $child_id);
            $maxHours = 10;
            if ($child->getAge() >= 14)
                $maxHours = 12;
            $hours = 0;
            foreach($findAllProps as $prop){
                /** @var Proposal $prop */
                /** @var Association $info */
                if ($prop->status_parent_id == 3)
                    continue;
                $info = DataSource::find("Association", $prop->association_id);
                $hours += $info->study_hours_week;
                if($hours >= $maxHours) break;
            }
            if($hours >= $maxHours)
                return -1;
        }
        else
        {
            return 0;
        }
        return $hours;
    }

    /**
     * @param int $child_id
     * @param int $parent_id
     * @param int $association_id
     * @throws RequestError
     */
    public function createProposal(int $child_id, int $parent_id, int $association_id, $isReserve = 0)
    {
        DataSource::insert(new Proposal([
            "timestamp" => DataSource::timeInMYSQLFormat(),
            'child_id' => $child_id,
            'parent_id' => $parent_id,
            'association_id' => $association_id,
            'status_admin_id' => 1, //TODO: константы статусов ожидания запроса?
            'status_parent_id' => 4, //TODO: константы статусов ожидания запроса?
            'status_teacher_id' => 1, //TODO: константы статусов ожидания запроса?
            'isReserve' => $isReserve
        ]));
    }

}
