<?php
namespace GraphQL\Application;

class Log {

    private static string $log_dir = __DIR__ . "/../../logs";

    public static function info($any){
        return self::writeWithFormat("INFO", print_r($any, true));
    }

    public static function warning($any){
        return self::writeWithFormat("WARN", print_r($any, true));
    }

    public static function error($any){
        return self::writeWithFormat("ERROR", print_r($any, true));
    }

    public static function fatal($any){
        return self::writeWithFormat("FATAL", print_r($any, true));
    }


    private static function writeWithFormat(string $type, string $str){
        return self::write("[".date("Y.m.d H:i:s")."] [{$type}] ".$str);
    }

    private static function write(string $str){
        return file_put_contents(self::$log_dir."/application.log", $str.PHP_EOL, FILE_APPEND) !== false;
    }

}