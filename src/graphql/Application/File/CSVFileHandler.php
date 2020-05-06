<?php

namespace GraphQL\Application\File;

class CSVFileHandler extends FileHandler {

    /**
     * Полное сканирование файла и получение его данных
     *
     * @param File $file
     * @param callable|null $func
     * @return array
     */
    public static function scanFile(File $file, callable $func = null): array {
        $result = [];
        foreach(explode("\n", $file->getContents(true)) as $line_index => $line_value ){
            foreach(explode(";", $line_value) as $row_index => $row_value){
                $result[$line_index][$row_index] = $row_value;
                if($func != null) $func($line_index, $row_index, $row_value, $file);
            }
        }
        return $result;
    }

    /**
     * Сканирование файла по строкам
     *
     * @param File $file
     * @param callable|null $func
     * @return array
     */
    public static function scanFileByRow(File $file, callable $func = null){
        $result = [];
        foreach(explode("\n", $file->getContents(true)) as $line_index => $line_value ){
            $result[$line_index] = explode(";", $line_value);
            if($func != null) $func($line_index, $result[$line_index], $file);
        }
        return $result;
    }

}