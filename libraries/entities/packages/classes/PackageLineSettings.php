<?php

namespace Entities\Packages\Classes;

use App\Core\AppEntity;
use Entities\Packages\Models\PackageLineSettingModel;

class PackageLineSettings extends AppEntity
{
    public string $strEntityName       = "packages";
    public $strDatabaseTable    = "package_line_setting";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = PackageLineSettingModel::class;
    public $strMainModelPrimary = "package_line_setting_id";
}
