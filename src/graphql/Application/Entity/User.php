<?php
namespace GraphQL\Application\Entity;

use GraphQL\Utils\Utils;

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
    public $password;
    public $surname;
    public $name;
    public $midname;
    public $sex;
    public $phone_number;
    public $email;
    public $registration_address;
    public $residence_address;
    public $job_position;
    public $job_place;
    public $relationship_id;
    public $study_place;
    public $study_class;
    public $date_registered;
    public $birthday;
    public $status_email;
    public $verification_key_email;

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
        return isset($this->id) && $this->id != 0;
    }

    /*
     * //TODO реализовать проверку на доступ к правам (https://habr.com/ru/post/51327/)
    function include_right($grp){
        $clone=clone $this;
        $clone->group[]=$grp;

        $result=mysql_query("SELECT * FROM `action_rights` WHERE `action_rights`.groupID IN ({$this->usrID}) AND `action_rights`.rightsID IN (".implode(",",$clone->group).")");
        $tmp=array();
        while ($t=mysql_fetch_assoc($result)){
            $tmp[]=$t;
        }
        mysql_free_result($result);

        $clone->temptable=$tmp;

        return $clone;
    }

    function check($action){
        $tmp=array();
        foreach ($this->temptable as $t){
            if ($t['action']==$action){
                if (!isset($tmp[$t['groupID']]))
                    $tmp[$t['groupID']]=$t['sign'];
                else
                    $tmp[$t['groupID']]|=$t['sign'];
            }
        }

        if ($tmp){
            return (array_search(0,$tmp)!==FALSE);
        }
        return false;
    }
    */

}
