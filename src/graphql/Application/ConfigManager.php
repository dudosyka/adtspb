<?php
namespace GraphQL\Application;

use Error;

class ConfigManager
{

	private static string $config_file = __DIR__ . "/../config.json"; // Путь до файла конфигурации

	private static $contents = null;


	public static function getField($field)
	{
		if(self::$contents == null) self::requireConfig();

		if(!isset(self::$contents[$field])){
			throw new Error("Поле '{$field}' не найдено в файле конфигурации (".realpath(self::$config_file).")");
		}

		return self::$contents[$field];
	}

	private static function requireConfig()
	{
		if(!file_exists(self::$config_file)){
			throw new Error("Файл конфигурации (".realpath(self::$config_file).") не найден. Проверьте его наличие, создайте его из его примера - файла config.sample.json");
		}

		$_ = json_decode(file_get_contents(self::$config_file), true);
		if(json_last_error() != JSON_ERROR_NONE){
			throw new Error("Файл конфигурации (".realpath(self::$config_file).") повреждён или имеет неверный JSON формат");
		}

		self::$contents = $_;

		return true;
	}


}