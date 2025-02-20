<?php
namespace GraphQL\Application;

use GraphQL\Application\Database\DataSource;
use GraphQL\Application\Entity\User;
use GraphQL\Application\File\File;
use GraphQL\Server\RequestError;

/**
 * Class AppContext
 * Контекст, поля/методы которого могут быть использованы в методах сущностей (User, Comment и т.п.)
 * Контекст задаётся в /src/graphql/Application/init.php
 *
 * @package GraphQL\Application
 */
class AppContext
{
    /**
     * Базовый URL
     * @var string
     */
    public string $rootUrl;

    /**
     * Текущий пользователь-просмоторщик
     * @var User
     */
    public User $viewer;

    /**
     * Тело запроса пользователя
     * @var \mixed
     */
    public $request;


    /**
     * Приложение
     * @var Application
     */
    public Application $app;


    /**
     * IP-клиента
     * @var string
     */
    public string $ip;


    /**
     * Проверка на аторизованность клиента
     * @return bool
     */
    public function isAuthorized(){
        return $this->viewer->isAuthorized();
    }

    /**
     * Проверить, авторизован ли пользователь, если нет - выкинуть исключение
     * @return bool
     * @throws RequestError
     * @throws \Exception
     */
    public function getBearerOrError(){
        if(!$this->isAuthorized())
            throw new RequestError("Требуется авторизация");

        $bearer = Bearer::getBearerFromHeader($this->app->getRequestHeaderValue("Authorization"));
        $user_id = $this->viewer->id;

        $found = DataSource::findOne("UserToken", "token = :token AND user_id = :uid", [
            "token" => $bearer,
            "uid" => $user_id
        ]);

        if($found == null)
            throw new RequestError("Требуется авторизация");

        return $bearer;
    }

    /**
     * Получение файла (если его нет - выдача ошибки пользователю).
     *
     * @param string $fileField
     * @return File
     * @throws RequestError
     */
    public function getFileOrError(string $fileField){

        // TODO: проверка типа файла по его расширению + mime типу

        if(!isset($_FILES[$fileField]) or $_FILES[$fileField] == null or strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
            throw new RequestError("Файл не прикреплён к запросу.");

        return new File($_FILES[$fileField]["tmp_name"]);
    }

}
