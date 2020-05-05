<?php


namespace GraphQL\Application\File;


class File {

    /**
     * @var string
     */
    private string $path;

    /**
     * @var string
     */
    private string $basename;

    /**
     * @var false|int
     */
    private int $size;

    /**
     * @var string
     */
    private string $mime_type;

    /**
     * @var string
     */
    private string $extension;

    /**
     * File constructor.
     * @param string $filename
     */
    public function __construct(string $filename) {

        $info = pathinfo($filename);

        $this->extension = $info["extension"];
        $this->path = $info["dirname"];
        $this->basename = $info["basename"];
        $this->size = filesize($filename);
        $this->mime_type = mime_content_type($filename);
    }

    /**
     * @return string
     */
    public function getFullName(): string{
        return $this->path . "/" . $this->basename;
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
    public function getBasename(): string {
        return $this->basename;
    }

    /**
     * @return int
     */
    public function getSize(): int {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getMimeType(): string {
        return $this->mime_type;
    }


    /**
     * @param bool $newLineReplacementToUtf8
     * @return string
     */
    public function getContents($newLineReplacementToUtf8 = false, $convertFromWindowsEncoding = false): string {

        $contents = file_get_contents($this->getFullName());
//        $convertFromWindowsEncoding && $contents = mb_convert_encoding($contents, 'utf-8', mb_detect_encoding("windows-1251")); // mb_ не работает в этом случае
        $convertFromWindowsEncoding && $contents = iconv( "windows-1251","UTF-8", $contents);

        // TODO: замена переноса строки \r\n на \n
//        $newLineReplacementToUtf8 && $contents = str_replace(array("\n", "\r"), "\n", $contents);
        return $contents;
    }

    // TODO: копирование, перемещение, удаление + связка с базой





}