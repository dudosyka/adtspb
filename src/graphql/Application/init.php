<?php
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/AppContext.php';
require_once __DIR__ . '/ConfigManager.php';
require_once __DIR__ . '/Types.php';

require_once __DIR__ . '/Entity/EntityBase.php';

foreach (["Database", "Entity", "Type", "Type/RootTypes", "Type/Scalar"] as $value){
	$dir_content = scandir(__DIR__ . '/' . $value . '/');

	foreach($dir_content as $key => $file) {
        if ($file != '.' && $file != '..' && !is_dir(__DIR__ . '/' . $value . '/' . $file)) {
            require_once __DIR__ . '/' . $value . '/' . $file;
        }
    }
}
error_reporting(E_ALL);

use GraphQL\Application\ConfigManager;
use GraphQL\Application\Entity\User;
use \GraphQL\Application\Types;
use \GraphQL\Application\AppContext;
use \GraphQL\Application\Database\DataSource;
use \GraphQL\Type\Schema;
use \GraphQL\GraphQL;
use \GraphQL\Error\FormattedError;
use \GraphQL\Error\Debug;



$http_origin = $_SERVER['HTTP_ORIGIN'];
if ($http_origin == "https://lk.adtspb.ru/" || ConfigManager::getField("debug")) {
    header("Access-Control-Allow-Origin: ".$http_origin);
}

header('Access-Control-Allow-Methods: GET,POST');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, Bearer");
header("Access-Control-Allow-Credentials: true");



// Disable default PHP error reporting - we have better one for debug mode (see bellow)
ini_set('display_errors', 0);

$debug = false;

// Подлкючение отладчика PHP кода, вывод ошибок серверного кода пользователю:
// [ВАЖНО!] данный блок используется ТОЛЬКО во время тестирования продукта, при выходе на "продакшн" (финальной сборке проекта) данный блок (33-38 строки, блок if) следует закомменитровать или удалить.

if(ConfigManager::getField("debug")){
    set_error_handler(function($severity, $message, $file, $line) use (&$phpErrors) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    });
    $debug = Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE;
}




try {

	// Симуляция текущего пользователя
    //TODO: неавторизованный пользователь с ID = 0;
	$current_user = DataSource::find('User', 0);
	//TODO: устанавливать пользователя при получении Bearer-токена
    //TODO: проверка isAuthed

	// Объявление контекста
	$appContext = new AppContext();
	$appContext->viewer = $current_user; // Симуляция текущего пользователя
	$appContext->rootUrl = 'http://localhost:8080'; //TODO: изменить rootUrl
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
		$data['query'] = '{viewer}';
	}

	// Генерация GraphQL-схемы
	$schema = new Schema([
		'query' => Types::query(),
		'mutation' => Types::mutation()
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
