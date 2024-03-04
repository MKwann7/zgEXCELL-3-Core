<?php

namespace Entities\Directories\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Directories\Models\DirectoryModel;
use Entities\Directories\Models\DirectorySettingModel;

class DirectorySettings extends AppEntity
{
    public string $strEntityName    = "Directories";
    public $strDatabaseTable        = "directory_setting";
    public $strDatabaseName         = "Apps";
    public $strMainModelName        = DirectorySettingModel::class;
    public $strMainModelPrimary     = "directory_setting_id";
}