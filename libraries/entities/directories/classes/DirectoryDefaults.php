<?php

namespace Entities\Directories\Classes;

use App\Core\AppEntity;
use Entities\Directories\Models\DirectoryDefaultModel;

class DirectoryDefaults extends AppEntity
{
    public string $strEntityName    = "Directories";
    public $strDatabaseTable        = "directory_default";
    public $strMainModelName        = DirectoryDefaultModel::class;
    public $strMainModelPrimary     = "directory_default_id";
    public $strDatabaseName         = "Apps";
}