<?php

namespace Entities\Cart\Classes\Helpers;

use App\Core\AppModel;
use App\Utilities\Excell\ExcellCollection;
use Entities\Packages\Models\PackageLineModel;
use Entities\Packages\Models\PackageModel;

class PackageSearch
{
    public function loopThroughLinesFromPackageRecord(PackageModel &$currPackage, $callback) : ?AppModel
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