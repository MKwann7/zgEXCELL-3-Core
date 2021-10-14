<?php

namespace Entities\Mobiniti\Classes;

use App\Core\AppEntity;
use Entities\Mobiniti\Models\MobinitiCouponModel;

class MobinitiCoupons extends AppEntity
{
    public $strEntityName       = "Mobiniti";
    public $strDatabaseTable    = "mobiniti_coupon";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = MobinitiCouponModel::class;
    public $strMainModelPrimary = "id";
}
