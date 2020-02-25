<?php
require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/AppContext.php';
require_once __DIR__ . '/ConfigManager.php';
require_once __DIR__ . '/Types.php';

require_once __DIR__ . '/Entity/EntityBase.php';

foreach (["Database", "Entity", "Type", "Type/RootTypes", "Type/Scalar"] as $value){
	$dir_content = scandir(__DIR__ . '/' . $value . '/');

	foreach($dir_content as $key => $file)
		if($file != '.' && $file != '..' && !is_dir(__DIR__ . '/' . $value . '/' . $file)){
			require_once __DIR__ . '/' . $value . '/' . $file;
		}

}


use GraphQL\Application\Entity\User;
use \GraphQL\Application\Types;
use \GraphQL\Application\AppContext;
use \GraphQL\Application\Database\DataSource;
use \GraphQL\Type\Schema;
use \GraphQL\GraphQL;
use \GraphQL\Error\FormattedError;
use \GraphQL\Error\Debug;

// Disable default PHP error reporting - we have better one for debug mode (see bellow)
ini_set('display_errors', 0);

$debug = false;

// Подлкючение отладчика PHP кода, вывод ошибок серверного кода пользователю:
// [ВАЖНО!] данный блок используется ТОЛЬКО во время тестирования продукта, при выходе на "продакшн" (финальной сборке проекта) данный блок (33-38 строки, блок if) следует закомменитровать или удалить.
//if (!empty($_GET['debug'])) {
	set_error_handler(function($severity, $message, $file, $line) use (&$phpErrors) {
		throw new ErrorException($message, 0, $severity, $file, $line);
	});
	$debug = Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE;
//}




try {

	// Симуляция текущего пользователя
	$current_user = DataSource::find('User', 2);
	if($current_user == null){
		throw new Exception("Текущий пользователь не найден.");
	}

	// Объявление контекста
	$appContext = new AppContext();
	$appContext->viewer = $current_user; // Симуляция текущего пользователя
	$appContext->rootUrl = 'http://localhost:8080';
	$appContext->request = $_REQUEST;

	// Parse incoming query and variables
	if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
		$raw = file_get_contents('php://input') ?: '';
		$data = json_decode($raw, true) ?: [];
	} else {
		$data = $_REQUEST;
	}

	$data += ['query' => null, 'variables' => null];

	if (null === $data['query']) {
		$data['query'] = '{hello}';
	}

	// Генерация GraphQL-схемы
	$schema = new Schema([
		'query' => Types::query()
	]);


	// Выполнение GraphQL-запроса
	$result = GraphQL::executeQuery(
		$schema,
		$data['query'],
		null,
		$appContext,
		(array) $data['variables']
	);
	$output = $result->toArray($debug);
	$httpStatus = 200;
} catch (\Exception $error) {
	$httpStatus = 500;
	$output['errors'] = [
		FormattedError::createFromException($error, $debug)
	];
}

header('Content-Type: application/json', true, $httpStatus);
echo json_encode($output);
