<?php


namespace GraphQL\Application\File;


class File {

    private string $path;

    private string $filename;

    /**
     * File constructor.
     * @param string $path
     * @param string $filename
     */
    public function __construct(string $path, string $filename) {
        $this->path = $path;
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getPath(): string {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getFilename(): string {
        return $this->filename;
    }


    /**
     * @param bool $newLineReplacementToUtf8
     * @return string
     */
    public function getContents($newLineReplacementToUtf8 = false): string {

        $contents = file_get_contents($this->path."/".$this->filename);
        $newLineReplacementToUtf8 && $contents = implode("\n", mb_split("\r\n", $contents));
        return $contents;
    }

    // TODO: копирование, перемещение, удаление + связка с базой





}