<?php

namespace GraphQL\Application\File;

class FileStorage {

    /**
     * Путь к папке с хранилищем данных
     *
     * @var string
     */
    private static string $storagePath = __DIR__ . "/../storage/";

    /**
     * Получение пути к папке с хранилищем всех данных
     *
     * @return string
     */
    public static function getStoragePath(){
        return realpath(self::$storagePath);
    }

    /*
    public static function getTeachersListFolder(){
        return self::getStoragePath() . "/" . "Загруженные списки педагогов";
    }
    */

    public static function createNewFile(File $file){

    }


}