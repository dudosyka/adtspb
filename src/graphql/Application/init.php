<?php

require_once __DIR__ . "/Application.php";
use GraphQL\Application\Application;

error_reporting(E_ALL);
//TODO: логгирование запросов
new Application();

