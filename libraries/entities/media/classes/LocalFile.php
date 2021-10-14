<?php


namespace Entities\Media\Classes;


class LocalFile
{
    protected $name;
    protected $path;
    protected $fullPathAndName;
    protected $extension;
    protected $type;
    protected $data;

    public function __construct ($fullPathAndName = null)
    {
        if (empty($fullPathAndName)) { return; }

        $arFullFileName = array_reverse(array_filter(explode("/", $fullPathAndName)));
        unset($arFullFileName[0]);

        if (count($arFullFileName) >= 1)
        {
            $fullPath = array_reverse($arFullFileName);
            makeRecursiveDirectories(AppTmp, $fullPath);
        }

        $this->extension = array_reverse(explode(".", $fullPathAndName))[0];

        $this->fullPathAndName = AppTmp . $fullPathAndName;
    }

    public function getFullFileName() : string
    {
        return $this->fullPathAndName;
    }

    public function getFileExtension() : string
    {
        return $this->extension;
    }

    public function addFileData($data) : void
    {
        file_put_contents($this->getFullFileName(), $data);
    }
}