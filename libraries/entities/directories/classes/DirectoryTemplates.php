<?php

namespace Entities\Directories\Classes;

use App\Core\AppEntity;
use Entities\Directories\Models\DirectoryTemplateModel;

class DirectoryTemplates extends AppEntity
{
    public string $strEntityName    = "Directories";
    public $strDatabaseTable        = "directory_template";
    public $strDatabaseName         = "Apps";
    public $strMainModelName        = DirectoryTemplateModel::class;
    public $strMainModelPrimary     = "directory_template_id";
}