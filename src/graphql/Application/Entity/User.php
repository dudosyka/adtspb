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
}
