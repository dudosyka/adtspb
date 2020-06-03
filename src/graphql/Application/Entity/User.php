<?php
namespace GraphQL\Application\Entity;

use DateTime;
use DateTimeZone;
use GraphQL\Application\AppContext;
use GraphQL\Application\Bearer;
use GraphQL\Application\Database\DataSource;
use GraphQL\Application\Log;
use GraphQL\Server\RequestError;
use GraphQL\Utils\Utils;
use Lcobucci\JWT\Token;

/**
 * Class User
 * Сущность пользователя.
 * (публичные GraphQL-методы см. в /src/graphql/Application/Type/UserType.php)
 * (поля объекта соответствуют полям таблицы, за которой прикреплена сущность - см. метод __getTable())
 *
 * @package GraphQL\Application\Data
 */

class User extends EntityBase
{
    const EMAIL_PENDING = "ожидание";
    const EMAIL_VALIDATED = "подтвержден";

    const PARENT = 2;
    const CHILD = 6;
    const TEACHER = 7;

    public $login;
    public $password;
    public $surname;
    public $name;
    public $midname;
    public $sex;
    public $phone_number;
    public $email;
    public $registration_address;
    public $registration_flat;
    public $residence_address;
    public $residence_flat;
    public $job_position;
    public $job_place;
    public $relationship;
    public $study_place;
    public $study_class;
    public $date_registered;
    public $birthday;
    public $status_email;
    public $verification_key_email;

    public $state;
    public $registration_type;
    public $ovz;

    //TODO: заносить данные в сущность через __construct
	public function __construct(array $data = null)
	{
		parent::__construct($data);
	}

	/**
	 * Ассоциированная таблица
	 * (таблица должна быть создана в базе данных)
	 *
	 * @return string
	 */
	public function __getTable()
    {
    	return "user";
    }


    /* Статические методы */

    /**
     * Настройки хэширования паролей
     *
     * @var array
     */
    public static $password_default_options = [
        "cost" => 12
    ];


    /**
     * Создание хэша пароля
     *
     * @param $password
     * @return string
     */
    public static function hashPassword($password): string{
	    return password_hash($password, PASSWORD_BCRYPT, self::$password_default_options);
    }

    /**
     * Проверка пароля, сравнение пароля с его хэшем
     *
     * @param $password
     * @param $hash
     * @return bool
     */
    public static function validatePassword($password, $hash): bool{
        return password_verify($password, $hash);
    }

    /**
     * Авторизован ли текущий пользователь
     *
     * @return bool
     */
    public function isAuthorized(){
        return isset($this->id) && $this->id != 0 && $this->status_email == User::EMAIL_VALIDATED;
    }


    /**
     * Проверка на доступ к функции
     *
     * @param int $action_id
     * @param string $list_id
     * @return bool
     */
    public function hasAccess(int $action_id, string $list_id = "1"): bool{

        //TODO: следует ли проверять права неавторизованного пользователя?
        if($this->id == 0)
            return false;

        // Если пользователь неавторизован
        //TODO: объединить верхний блок с нижним
        if(!$this->isAuthorized())
            return false;

        /** @var array $user_role */
        $user_role = DataSource::findAll("UserRole", "user_id = :uid", ["uid" => $this->id]);
        foreach ($user_role as $i){
            /** @var UserRole $i */

            /** @var ActionList $action_data */
            $action_data = DataSource::findOne("ActionList", "role_id = :rid AND action_id = :aid AND list_id = :lid", [
                "rid" => $i->role_id,
                "aid" => $action_id,
                "lid" => $list_id
            ]);

            if($action_data != null AND $action_data->sign == "+")
                return true;
            
            if($action_data != null AND $action_data->sign == "-")
                return false;
        }

        return false;
    }

    /**
     * Проверка на доступ к функции. Если доступа нет, то приложение выбрасывает ошибку.
     *
     * @param int $action_id
     * @param string $list_id
     * @return bool
     * @throws RequestError
     */
    public function hasAccessOrError(int $action_id, string $list_id = "1"){
        if(!$this->hasAccess($action_id, $list_id))
            throw new RequestError("Ошибка: нет доступа к функции");

        return true;
    }

    /**
     * Генерация имени пользователя на основе ФИО и уникального ID
     *
     * @param string $surname
     * @param string $name
     * @param string $midname
     * @param int $id
     * @return string
     */
    public static function serializeNickname(string $surname, string $name, string $midname, int $id): string{
        $s = $surname . mb_substr($name, 0, 1) . mb_substr($midname, 0, 1);
        if($id > 1) $s .= $id;

        $s = str_replace(["\n", "\r"], " ", $s); // убираем перевод каретки
        $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
        $s = trim($s); // убираем пробелы в начале и конце строки
        $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр
        $s = strtr($s, [
            'а'=>'a','б'=>'b','в'=>'v','г'=>'g',
            'д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh',
            'з'=>'z','и'=>'i','й'=>'i','к'=>'k',
            'л'=>'l','м'=>'m','н'=>'n','о'=>'o',
            'п'=>'p','р'=>'r','с'=>'s','т'=>'t',
            'у'=>'u','ф'=>'f','х'=>'kh','ц'=>'ts',
            'ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y',
            'э'=>'e','ю'=>'iu','я'=>'ia',
            'ъ'=>'ie','ь'=>''
        ]);



        $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
        return $s; // возвращаем результат
    }

    /**
     * Генерация токена для входа. Используется при авторизации пользователя, ...
     *
     * @param AppContext $context
     * @return Token
     * @throws RequestError
     */
    public function generateLoginToken(AppContext $context){
        $token = Bearer::generate($context, $this);
        $token_inst = new UserToken([
            "token" => $token,
            "date_created" => DataSource::timeInMYSQLFormat(),
            "user_id" => $this->id
        ]);
        DataSource::insert($token_inst);
        return $token;
    }

    /**
     *
     * @throws \Exception
     */
    public function getAge(){

        if($this->birthday == null)
            return 0;

        $tz  = new DateTimeZone('Europe/Moscow');
        $age = DateTime::createFromFormat('Y-m-d', $this->birthday, $tz)
            ->diff(new DateTime('now', $tz))
            ->y;

        return $age;
    }

}
