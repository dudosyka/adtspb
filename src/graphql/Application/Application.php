<?php
namespace GraphQL\Application;

use Bearer;
use ErrorException;
use Exception;
use GraphQL\Application\AppContext;
use GraphQL\Application\ConfigManager;
use GraphQL\Application\Entity\User;
use \GraphQL\Application\Types;
use \GraphQL\Application\Database\DataSource;
use \GraphQL\Type\Schema;
use \GraphQL\GraphQL;
use \GraphQL\Error\FormattedError;
use \GraphQL\Error\Debug;
use Request;

class Application
{

    private $debug = false;
    private array $applicationHeaders = [];


    /**
     * Application constructor.
     */
    public function __construct(){
        $this->applicationHeaders = $this->getRequestHeaders();
        $this->requireModules();
        $this->initDebug();
        $this->handleRequest();
    }


    public function requireModules(){
        require_once __DIR__ . '/../vendor/autoload.php';

        require_once __DIR__ . '/AppContext.php';
        require_once __DIR__ . '/ConfigManager.php';
        require_once __DIR__ . '/Bearer.php';
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
    }

    public function echoHeaders(){
        $http_origin = $_SERVER['HTTP_ORIGIN'];
        if ($http_origin == "https://lk.adtspb.ru/" || ConfigManager::getField("debug")) {
            header("Access-Control-Allow-Origin: ".$http_origin);
        }

        header('Access-Control-Allow-Methods: GET,POST');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, Bearer");
        header("Access-Control-Allow-Credentials: true");
    }

    public function initDebug(){
        // Disable default PHP error reporting - we have better one for debug mode (see bellow)
        ini_set('display_errors', 0);

        $this->debug = false;

        // Подлкючение отладчика PHP кода, вывод ошибок серверного кода пользователю:
        // [ВАЖНО!] данный блок используется ТОЛЬКО во время тестирования продукта, при выходе на "продакшн" (финальной сборке проекта) данный блок (33-38 строки, блок if) следует закомменитровать или удалить.

        if(ConfigManager::getField("debug")){
            set_error_handler(function($severity, $message, $file, $line) use (&$phpErrors) {
                throw new ErrorException($message, 0, $severity, $file, $line);
            });
            $this->debug = Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE;
        }
    }


    public function handleRequest(){
        try {
            $this->echoHeaders();

            $appContext = $this->generateAppContext();
            $data = $this->parseData();


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

            $output = $result->toArray($this->debug);
            $httpStatus = 200;

        } catch (\Exception $error) {
            $httpStatus = 500;
            $output['errors'] = [
                FormattedError::createFromException($error, $this->debug)
            ];
        }

        $this->sendJSON($output, $httpStatus);
    }


    public function sendJSON($output, int $httpStatus = 200){
        header('Content-Type: application/json', true, $httpStatus);
        die(json_encode($output));
    }


    /**
     * @return \GraphQL\Application\AppContext
     * @throws Exception
     */
    private function generateAppContext(): AppContext{

        //TODO: вывод Exception пользователю (не как Internal Server Error)

        // Инициализация текущего пользователя
        $uid = $this->getCurrentUserID();
        $current_user = DataSource::find('User', $uid);
        if($current_user == null){
            if($uid == 0){
                throw new \Exception("Объект незарегистрированного пользователя (id=0) не найден в базе. Добавьте в базу пользователя с ID = 0 для работы приложения");
            }
            throw new \Exception("Пользователь не найден в базе (однако bearer-token в базе найден)");
        }

        // Объявление контекста
        $appContext = new AppContext();
        $appContext->viewer = $current_user; // Симуляция текущего пользователя
        $appContext->rootUrl = 'http://localhost:8080'; //TODO: изменить rootUrl
        $appContext->request = $_REQUEST;
        $appContext->app = $this;

        return $appContext;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function parseData(): array{

        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $raw = file_get_contents('php://input') ?: '';
            $data = json_decode($raw, true) ?: [];
        } else {
            $data = $_REQUEST;
        }

        $data += ['query' => null, 'variables' => null];

        if (null === $data['query'] || trim($data["query"]) == ""){
            throw new \Exception("Задан пустой запрос");
        }

        return $data;
    }

//    private string $generic_query = '{viewer}';
//
//    public function getGenericQuery(){
//        return $this->generic_query;
//    }











    public function getRequestHeaders() {
        if($this->applicationHeaders == null){
            $headers = [];
            foreach($_SERVER as $key => $value) {
                if (substr($key, 0, 5) <> 'HTTP_') {
                    continue;
                }
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$header] = $value;
            }
        } else {
            $headers = $this->applicationHeaders;
        }

        return $headers;
    }

    public function getRequestHeaderValue($header){
        return (isset($this->getRequestHeaders()[$header])) ? $this->getRequestHeaders()[$header] : null;
    }

    private int $current_uid;

    public function getCurrentUserID(): int{

        if(!isset($this->current_uid)){
            $uid = 0;

            $bearer = $this->getRequestHeaderValue("Bearer");
            if($bearer == null){
                $uid = 0;
            } else {
                try {
                    $uid = Bearer::getUserIDFromBearer($bearer);
                } catch (Exception $e) {
                    $uid = 0;
                }
            }
        } else {
            $uid = $this->current_uid;
        }

        return $uid;

    }







}