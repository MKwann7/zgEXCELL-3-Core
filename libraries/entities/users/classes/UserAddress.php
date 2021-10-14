<?php

namespace Entities\Users\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use Entities\Users\Models\UserAddressModel;

class UserAddress extends AppEntity
{
    public $strEntityName       = "Users";
    public $strDatabaseTable    = "user_address";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = UserAddressModel::class;
    public $strMainModelPrimary = "address_id";
}