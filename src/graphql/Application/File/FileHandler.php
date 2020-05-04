<?php

namespace GraphQL\Application\File;

abstract class FileHandler {

    abstract public static function scanFile(File $file, callable $func): array;

}