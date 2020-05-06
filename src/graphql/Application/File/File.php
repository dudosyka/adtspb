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
     * @return false|int
     */
    public function isUTF8(){
        return preg_match('%(?:
            [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
            |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
            |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
            |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
            |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
            |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
            |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
            )+%xs', $this->getContents());
    }

    /**
     * @param bool $newLineReplacementToUtf8
     * @param bool $convertFromWindowsEncoding
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