<?php

namespace Entities\Cart\Classes\Helpers;

use App\Core\AppModel;
use App\entities\packages\models\PackageVariationModel;
use App\Utilities\Excell\ExcellCollection;
use Entities\Packages\Models\PackageLineModel;

class PackageSearch
{
    public function loopThroughLinesFromPackageRecord(PackageVariationModel &$currPackage, $callback) : ?AppModel
    {
        if (empty($currPackage->lines) || !is_a($currPackage->lines, ExcellCollection::class)) { return null; }

        $currPackage->lines->Foreach(function(PackageLineModel $currPackageLine) use (&$currPackage, $callback)
        {
            $result = $callback($currPackageLine, $currPackage);

            if ($result === null) { return; }

            return $result;
        });

        return $currPackage;
    }
}