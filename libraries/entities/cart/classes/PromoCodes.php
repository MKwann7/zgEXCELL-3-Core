<?php

namespace Entities\Cart\Classes;

use App\Core\AppEntity;
use Entities\Cart\Models\PromoCodeModel;

class PromoCodes extends AppEntity
{
    public function __construct()
    {
        parent::__construct();
    }

    public $strEntityName       = "Carts";
    public $strDatabaseTable    = "promo_code";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = PromoCodeModel::class;
    public $strMainModelPrimary = "promo_code_id";
}