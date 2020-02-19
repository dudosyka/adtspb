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
    public string $password;
    public string $surname;
    public string $name;
    public string $midname;
    public int $sex;
    public string $phone_number;
    public string $email;
    public string $registration_adress;
    public string $residence_address;
    public string $job_place;
    public string $job_position;
    public string $relationship;
    public string $childids;
    public string $study_place;
    public string $study_class;
    public string $date_registered;
    public string $birthday;
    public string $status_email;
    public string $verification_key_email;

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
