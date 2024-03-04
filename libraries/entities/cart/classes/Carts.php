<?php

namespace Entities\Cart\Classes;

use App\Core\AppEntity;
use Entities\Cart\Models\CartModel;

class Carts extends AppEntity
{
    public function __construct()
    {
        parent::__construct();
    }

    public string $strEntityName       = "Carts";
    public $strDatabaseTable    = "cart";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CartModel::class;
    public $strMainModelPrimary = "cart_id";
    public $isPrimaryModule     = true;
}