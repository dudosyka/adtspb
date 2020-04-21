<?php
namespace GraphQL\Application;

use GraphQL\Application\Entity\User;

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


    //TODO: isAuthed method
    public function isAuthed(){
        return false;
    }

}
