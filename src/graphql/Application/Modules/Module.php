<?php


namespace GraphQL\Application\Modules;


use GraphQL\Application\AppContext;

interface Module {

    // Генерация результата
    public function result(AppContext $context): array;

}