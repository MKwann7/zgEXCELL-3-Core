<?php


namespace Entities\Media\Classes;


class LocalFile
{
    protected string $name;
    protected string $path;
    protected string $fullPathAndName;
    protected string $pathAndName;
    protected string $fileame;
    protected string $extension;
    protected string $type;
    protected string $data;
    protected int $fileSize;
    protected int $width;
    protected int $height;

    public function __construct ($fullPathAndName = null)
    {
        if (empty($fullPathAndName)) { return; }

        $arFullFileName = array_reverse(array_filter(explode("/", $fullPathAndName)));
        unset($arFullFileName[0]);

        if (count($arFullFileName) >= 1)
        {
            $fullPath = array_reverse($arFullFileName);
            makeRecursiveDirectories(APP_TMP, $fullPath);
        }

        $reversedFileNamePath = array_reverse(explode(".", $fullPathAndName));

        $this->extension = $reversedFileNamePath[0];

        $this->fullPathAndName = APP_TMP . $fullPathAndName;
        $this->pathAndName = $fullPathAndName;
        $this->fileame = $reversedFileNamePath[1] . "." . $reversedFileNamePath[0];
    }

    public function getFullFileName() : string
    {
        return $this->fullPathAndName;
    }

    public function getFileExtension() : string
    {
        return $this->extension;
    }

    public function addFileData($data) : bool
    {
        $this->fileSize = file_put_contents($this->getFullFileName(), $data);

        $imageSize = getimagesize($this->getFullFileName());

        $this->width = $imageSize[0];
        $this->height = $imageSize[1];

        if ($this->fileSize === false) {
            return false;
        }

        return true;
    }

    public function getWidth() : int
    {
        return $this->width;
    }

    public function getHeight() : int
    {
        return $this->height;
    }

    public function getFileName() : string
    {
        return $this->fileame;
    }
}