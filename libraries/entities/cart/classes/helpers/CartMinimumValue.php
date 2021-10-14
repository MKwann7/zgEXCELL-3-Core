<?php

namespace Entities\Cart\Classes\Helpers;

use Entities\Packages\Models\PackageModel;

class CartMinimumValue
{
    private $packageSearch;

    public function __construct ()
    {
        $this->packageSearch = new PackageSearch();
    }

    public function process(&$packages, $packageQuantities) : void
    {
        $this->assignMininumPackageValuesToPackagesIfApplicable($packages, $packageQuantities);

        $packages->Foreach(function (PackageModel $currPackage)
        {
            if (!empty($currPackage->min_package_value) && $currPackage->min_package_value > 0)
            {
                $this->mutateTargetPackageLinePriceOverrideIfBelowMinPackageValue($currPackage);
            }
        });
    }

    private function mutateTargetPackageLinePriceOverrideIfBelowMinPackageValue(&$currPackage): void
    {
        $ezDigitalProductsTotalRecurringValue = 0;
        $ezDigitalProductsTotalInitialValue = 0;

        $totalProductRecurringValueWithoutMinValue = 0;
        $totalProductInitialValueWithoutMinValue = 0;

        $this->packageSearch->loopThroughLinesFromPackageRecord($currPackage, function($currPackageLine) use (&$ezDigitalProductsTotalRecurringValue, &$ezDigitalProductsTotalInitialValue, &$totalProductRecurringValueWithoutMinValue, &$totalProductInitialValueWithoutMinValue)
        {
            $ezDigitalProductRecurringValue = ($currPackageLine->product_price_override ?? $currPackageLine->product->value);
            $ezDigitalProductInitialValue = ($currPackageLine->product_promo_price_override ?? $currPackageLine->product->promo_value);

            $ezDigitalProductRecurringValue *= $currPackageLine->quantity;
            $ezDigitalProductInitialValue *= $currPackageLine->quantity;

            $ezDigitalProductsTotalRecurringValue += $ezDigitalProductRecurringValue;
            $ezDigitalProductsTotalInitialValue += $ezDigitalProductInitialValue;

            if (empty($currPackageLine->product->min_package_value))
            {
                $totalProductRecurringValueWithoutMinValue += $ezDigitalProductRecurringValue;
                $totalProductInitialValueWithoutMinValue += $ezDigitalProductInitialValue;
            }
        });

        if ($ezDigitalProductsTotalRecurringValue < $currPackage->min_package_value)
        {
            $this->processMinPackageValueAdjustment($currPackage, $totalProductRecurringValueWithoutMinValue, "product_price_override");
        }

        if ($ezDigitalProductsTotalInitialValue < $currPackage->min_package_value)
        {
            $this->processMinPackageValueAdjustment($currPackage, $totalProductInitialValueWithoutMinValue, "product_promo_price_override");
        }
    }

    private function assignMininumPackageValuesToPackagesIfApplicable(&$packages, $packageQuantities): void
    {
        $packages->Foreach(function (PackageModel $currPackage) use ($packageQuantities)
        {
            $currPackage->AddUnvalidatedValue("cart_quantity", $packageQuantities[$currPackage->package_id]);

            return $this->packageSearch->loopThroughLinesFromPackageRecord($currPackage, static function($currPackageLine, $currPackage)
            {
                if (!empty($currPackageLine->product->min_package_value) && $currPackageLine->product->min_package_value > 0)
                {
                    $currPackage->AddUnvalidatedValue("min_package_value", $currPackageLine->product->min_package_value);
                    $currPackage->AddUnvalidatedValue("min_package_value_line_id", $currPackageLine->package_line_id);
                }
            });
        });
    }

    protected function processMinPackageValueAdjustment(PackageModel $currPackage, $totalValueWithoutMinValue, $adjustmentField) : void
    {
        $newPackageLinePrice = $currPackage->min_package_value - $totalValueWithoutMinValue;

        $this->packageSearch->loopThroughLinesFromPackageRecord($currPackage, function($currPackageLine, $currPackage) use ($newPackageLinePrice, $adjustmentField)
        {
            if ($currPackageLine->package_line_id === $currPackage->min_package_value_line_id)
            {
                $currPackageLine->{$adjustmentField} = $newPackageLinePrice;
                return $currPackageLine;
            }
        });
    }

}