<?php

namespace Entities\Directories\Classes;

use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Directories\Models\DirectoryPackageModel;
use Entities\Modules\Models\ModuleAppModel;
use Entities\Packages\Classes\PackageLines;

class DirectoryPackages extends AppEntity
{
    public string $strEntityName    = "Directories";
    public $strDatabaseTable        = "directory_package";
    public $strMainModelName        = DirectoryPackageModel::class;
    public $strMainModelPrimary     = "directory_package_id";
    public $strDatabaseName         = "Apps";

    public function getFullById($id) : ExcellTransaction
    {
        $packageQuery = "SELECT dp.directory_package_id, dp.directory_id, dp.status, dp.permanent_public_viewing, dp.membership, dp.events_discount, dp.events_discount_value, dp.events_free, dp.sys_row_id as dir_sys_row_id, mp.* ".
            "FROM excell_apps.directory_package dp " .
            "JOIN excell_main.package mp ON mp.package_id = dp.package_id " .
            "WHERE dp.directory_package_id = {$id} ORDER BY dp.directory_package_id ASC";

        $directoryPackages = Database::getSimple($packageQuery,"directory_package_id");

        if ($directoryPackages->result->Success === false) {
            return $directoryPackages;
        }

        $directoryPackages->getData()->HydrateModelData(DirectoryPackageModel::class, true);

        $packageIds = $directoryPackages->getData()->FieldsToArray(["package_id"]);

        $packageLines = new PackageLines();
        $packageLineResult = $packageLines->getWhereIn("package_id", $packageIds);

        $directoryPackages->getData()->HydrateChildModelData("__packageLine", ["package_id" => "package_id"], $packageLineResult->getData(), true);

        return $directoryPackages;
    }

    public function getAllByDirectoryId($id) : ExcellTransaction
    {
        $packageQuery = "SELECT dp.directory_package_id, dp.directory_id, dp.status, dp.permanent_public_viewing, dp.membership, dp.events_discount, dp.events_discount_value, dp.events_free, dp.sys_row_id as dir_sys_row_id, mp.* ".
            "FROM excell_apps.directory_package dp " .
            "JOIN excell_main.package mp ON mp.package_id = dp.package_id " .
            "WHERE dp.directory_id = {$id} ORDER BY dp.directory_package_id ASC";

        $directoryPackages = Database::getSimple($packageQuery,"directory_package_id");

        if ($directoryPackages->result->Success === false) {
            return $directoryPackages;
        }

        $directoryPackages->getData()->HydrateModelData(DirectoryPackageModel::class, true);

        $packageIds = $directoryPackages->getData()->FieldsToArray(["package_id"]);

        $packageLines = new PackageLines();
        $packageLineResult = $packageLines->getWhereIn("package_id", $packageIds);

        $directoryPackages->getData()->HydrateChildModelData("__packageLine", ["package_id" => "package_id"], $packageLineResult->getData(), true);

        return $directoryPackages;
    }
}