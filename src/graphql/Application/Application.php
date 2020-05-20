<?php
namespace GraphQL\Application;

use GraphQL\Application\Bearer;
use ErrorException;
use Exception;
use GraphQL\Application\AppContext;
use GraphQL\Application\ConfigManager;
use GraphQL\Application\Entity\User;
use \GraphQL\Application\Types;
use \GraphQL\Application\Database\DataSource;
use GraphQL\Server\RequestError;
use \GraphQL\Type\Schema;
use \GraphQL\GraphQL;
use \GraphQL\Error\FormattedError;
use \GraphQL\Error\Debug;

class Application
{

    private $debug = false;
    private array $applicationHeaders = [];


    /**
     * Инициализация запроса
     */
    public function __construct(){
        $this->applicationHeaders = $this->getRequestHeaders();
        $this->requireModules();
        $this->initDebug();
        $this->handleRequest();
    }

    /**
     * Инициализация и подгрузка модулей
     */
    private function requireModules(){
        require_once __DIR__ . '/../vendor/autoload.php';

        require_once __DIR__ . '/AppContext.php';
        require_once __DIR__ . '/ConfigManager.php';
        require_once __DIR__ . '/Bearer.php';
        require_once __DIR__ . '/Types.php';

        require_once __DIR__ . '/Entity/EntityBase.php';

        require_once __DIR__ . '/File/FileHandler.php'; // Во избежание ошибок импорта

        foreach (["Database", "Entity", "Type", "Type/RootTypes", "Type/Scalar", "Log", "File"] as $value){
            $dir_content = scandir(__DIR__ . '/' . $value . '/');

            foreach($dir_content as $key => $file) {
                if ($file != '.' && $file != '..' && !is_dir(__DIR__ . '/' . $value . '/' . $file)) {
                    require_once __DIR__ . '/' . $value . '/' . $file;
                }
            }
        }
    }

    /**
     * Вывод заголовков ответа
     */

    private function echoHeaders(){
        $http_origin = $_SERVER['HTTP_ORIGIN'] ?? "error";
        if ($http_origin == "https://lk.adtspb.ru/") {
            header("Access-Control-Allow-Origin: ".$http_origin);
        } elseif(ConfigManager::getField("debug")){
            header("Access-Control-Allow-Origin: *");
        }

        header('Access-Control-Allow-Methods: GET,POST,OPTIONS');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        header("Access-Control-Allow-Credentials: true");
    }

    /**
     * Инициализация модуля отладки
     *
     */
    private function initDebug(){
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





    /**
     * Обработка запроса, вывод схемы пользователю, мониторинг ошибок
     *
     * @throws \Throwable
     */
    private function handleRequest(){
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
            $httpStatus = 200; //TODO: не отправляется CORS-запрос при выводе ошибки 500 (Причина: неудача ответа CORS preflight)
            $output['errors'] = [
                FormattedError::createFromException($error, $this->debug)
            ];
        }

        $this->sendJSON($output, $httpStatus);
    }

    /**
     * Вывод текста в формате JSON, завершение обработки запроса
     *
     * @param $output
     * @param int $httpStatus
     */
    public function sendJSON($output, int $httpStatus = 200){
        header('Content-Type: application/json', true, $httpStatus);
        die(json_encode($output));
    }


    /**
     * Создание контекста запроса (используется в методах схемы GraphQL)
     *
     * @return \GraphQL\Application\AppContext
     * @throws Exception
     */
    private function generateAppContext(): AppContext {

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
        $appContext->viewer = $current_user;
        $appContext->rootUrl = 'http://localhost:8085'; //TODO: изменить rootUrl
        $appContext->request = $_REQUEST;
        $appContext->app = $this;
        $appContext->ip = $this->getClientIP();

        return $appContext;
    }

    /**
     * Получение IP клиента
     * @return mixed
     */
    private function getClientIP(){
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ip_address = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            $ip_address = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ip_address = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ip_address = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ip_address = $_SERVER['REMOTE_ADDR'];
        else
            $ip_address = '127.0.0.1';
        return $ip_address;
    }

    /**
     * Обработка данных запроса
     *
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
            throw new RequestError("Задан пустой запрос");
        }

        return $data;
    }

//    private string $generic_query = '{viewer}';
//
//    public function getGenericQuery(){
//        return $this->generic_query;
//    }


    /**
     * Получение списка всех заголовков запроса, кеширование в поле класса
     *
     * @return array
     */
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

    /**
     * Получение определённого заголовка
     *
     * @param $header
     * @return mixed|null
     */
    public function getRequestHeaderValue($header){
        return (isset($this->getRequestHeaders()[$header])) ? $this->getRequestHeaders()[$header] : null;
    }

    private int $current_uid;

    /**
     * Получение ID текущего пользователя из его Bearer-токена
     *
     * @return int
     */

    public function getCurrentUserID(): int{

        if(!isset($this->current_uid)){

            try{
                $uid = Bearer::getUserIDFromBearer(Bearer::getBearerFromHeader($this->getRequestHeaderValue("Authorization")));
            } catch(\Exception $e){
                $uid = 0;
            }

        } else {
            $uid = $this->current_uid;
        }

        return $uid;
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function getRandomString(){
        return md5(serialize(bin2hex(random_bytes(16))));
    }

    /**
     * @return false|mixed|string|string[]|null
     */
    public static function generateValidationCode(){
        try {
            $str = self::getRandomString();
        } catch (Exception $e) {
            $str = md5(date('U'));
        }
        $str = mb_strtoupper(mb_substr($str, 0, 8));
        return (string)$str;
    }

    /**
     * @param string $recipient
     * @param string $subject
     * @param string $html_body
     */
    public static function sendMail(string $recipient, string $subject, string $html_body){

        $headers = [];
        $headers["From"] = "webmaster@example.com";
//      $headers["Reply-To"] = "webmaster@example.com";
        $headers["X-Mailer"] = 'PHP/' . phpversion();

        mail($recipient, $subject, $html_body, $headers);
    }



}