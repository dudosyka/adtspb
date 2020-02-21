<?php
namespace GraphQL\Application\Database;

use Error;
use GraphQL\Application\ConfigManager;
use PDO;
use PDOException;

/**
 * Class DataSource
 *
 * Менеджер по работе с базой данных (используется PDO).
 *
 * @package GraphQL\Application
 */
class DataSource
{

	/**
	 * Адаптер PDO для работы с базой данных
	 *
	 * @var PDO
	 */
	private static PDO $pdo;

	/**
	 * Инициализация адаптера
	 */
	private static function initInstance()
	{

		if(!isset(self::$pdo))
		{
			$db_type = ConfigManager::getField("db_type");
			$db_host = ConfigManager::getField("db_host");
			$db_name = ConfigManager::getField("db_name");
			$db_user = ConfigManager::getField("db_user");
			$db_pass = ConfigManager::getField("db_pass");
			try {
				self::$pdo = new PDO("{$db_type}:dbname={$db_name};host={$db_host};charset=utf8", $db_user, $db_pass,[
					PDO::ATTR_PERSISTENT => true
				]);
			} catch (PDOException $e) {
				throw new Error("Не могу соединиться с базой данных.");
			}
//			self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}

	}

	/**
	 * Получение адаптера
	 *
	 * @return PDO
	 */
	public static function getPDO()
	{
		return self::$pdo;
	}

	/**
	 * Нахождение сущности по ID
	 * Пример использования:
	 *      DataSource::find('User', 1);
	 *
	 * @param string $class
	 * @param int $id
	 * @return mixed|null
	 */
	public static function find(string $class, int $id)
	{
		$bindings = ["id" => (string)$id];
		return self::findOne($class, "id = :id", $bindings);
	}

	/**
	 * Нахождение сущности по первому вхождению
	 * [ Выдаётся первый попавшийся элемент, для нахождения по ID см. метод выше ]
	 *
	 * @param string $class
	 * @param string $query
	 * @param array $bindings
	 * @return mixed|null
	 */
	public static function findOne(string $class, string $query, array $bindings = [])
	{
		$result = self::findAll($class, $query." LIMIT 1", $bindings);
		return (count($result) > 0) ? $result[0] : null;
	}

	/**
	 * Нахождение всех сущностей по запросу
	 *
	 * @param string $class
	 * @param string $query
	 * @param array $bindings
	 * @return array
	 */
	public static function findAll(string $class, string $query, array $bindings = []): array
	{

		self::initInstance();

		$class = "\\GraphQL\\Application\\Entity\\{$class}";
		$assoc_table = (new $class(null))->__getTable();


		$query = self::getPDO()->prepare("SELECT * FROM `{$assoc_table}` WHERE {$query}");
		foreach($bindings as $key => $value)
		{
			$query->bindValue($key, $value);
		}


		$isSuccessful = $query->execute();

		if(!$isSuccessful)
		{
			$arr = print_r($query->errorInfo(), true);
			throw new Error("Не удалось совершить запрос: ".$arr);
		}

		$res = $query->fetchAll();
		$result = [];
		foreach($res as $object_key => $object_value)
		{
			$__ = new $class();

			foreach($object_value as $key => $value)
			{
				$__->$key = $value;
			}

			$result[] = $__;

		}
		return $result;
	}



}
