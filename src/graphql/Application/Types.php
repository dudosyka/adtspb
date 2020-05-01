<?php
namespace GraphQL\Application;

use GraphQL\Application\Type\AdminMutationType;
use GraphQL\Application\Type\MutationType;
use GraphQL\Application\Type\NodeType;
use GraphQL\Application\Type\QueryType;
use GraphQL\Application\Type\Scalar\DateType;
use GraphQL\Application\Type\Scalar\EmailType;
use GraphQL\Application\Type\Scalar\PasswordType;
use GraphQL\Application\Type\Scalar\PhoneNumberType;
use GraphQL\Application\Type\Scalar\SexType;
use GraphQL\Application\Type\Scalar\UrlType;
use GraphQL\Application\Type\UserType;
use GraphQL\Type\Definition\CustomScalarType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;

/**
 * Class Types
 * Регистратор GraphQL типов данных.
 *
 * @package GraphQL\Application
 */
class Types
{
	/* Типы данных сущностей */
    private static $user;


    /**
     * Пользователь
     *
     * @return UserType
     */
    public static function user()
    {
        return self::$user ?: (self::$user = new UserType());
    }









    /* Корневые типы */
    private static $node;
	private static $query;
    private static $mutation;
    private static $adminMutation;
    /**
     * Тип объекта, имеющего ID
     *
     * @return NodeType
     */
    public static function node()
    {
        return self::$node ?: (self::$node = new NodeType());
    }

	/**
	 * Тип объекта с методами получения данных
	 *
	 * @return QueryType
	 */
	public static function query()
	{
		return self::$query ?: (self::$query = new QueryType());
	}

    /**
     * Тип объекта с методами дейстийвий
     *
     * @return MutationType
     */
    public static function mutation()
    {
        return self::$mutation ?: (self::$mutation = new MutationType());
    }

    /**
     * Тип объекта с методами дейстийвий для администратора
     *
     * @return AdminMutationType
     */
    public static function adminMutation()
    {
        return self::$adminMutation ?: (self::$adminMutation = new AdminMutationType());
    }









    /* Объектные типы данных */
    private static $urlType;
    private static $emailType;
    private static $dateType;
    private static $passwordType;
    private static $phoneNumberType;
    private static $sexType;

	/**
	 * Тип e-mail
	 *
	 * @return CustomScalarType
	 */
	public static function email()
    {
        return self::$emailType ?: (self::$emailType = EmailType::create());
    }

    /**
     * Тип ссылкок
     *
     * @return UrlType
     */
    public static function url()
    {
        return self::$urlType ?: (self::$urlType = new UrlType());
    }

    /**
     * Тип даты
     *
     * @return DateType
     */
    public static function date()
    {
        return self::$dateType ?: (self::$dateType = new DateType());
    }

    /**
     * Тип пароля
     *
     * @return PasswordType
     */
    public static function password()
    {
        return self::$passwordType ?: (self::$passwordType = new PasswordType());
    }

    /**
     * Тип телефонного номера
     *
     * @return PhoneNumberType
     */
    public static function phoneNumber()
    {
        return self::$phoneNumberType ?: (self::$phoneNumberType = new PhoneNumberType());
    }

    /**
     * Тип пола
     *
     * @return SexType
     */
    public static function sex()
    {
        return self::$sexType ?: (self::$sexType = new SexType());
    }













    /* Базовые типы данных */

    public static function boolean()
    {
        return Type::boolean();
    }

    /**
     * @return \GraphQL\Type\Definition\FloatType
     */
    public static function float()
    {
        return Type::float();
    }

    /**
     * @return \GraphQL\Type\Definition\IDType
     */
    public static function id()
    {
        return Type::id();
    }

    /**
     * @return \GraphQL\Type\Definition\IntType
     */
    public static function int()
    {
        return Type::int();
    }

    /**
     * @return \GraphQL\Type\Definition\StringType
     */
    public static function string()
    {
        return Type::string();
    }

    /**
     * @param Type $type
     * @return ListOfType
     */
    public static function listOf($type)
    {
        return new ListOfType($type);
    }

    /**
     * @param Type $type
     * @return NonNull
     */
    public static function nonNull($type)
    {
        return new NonNull($type);
    }
}
