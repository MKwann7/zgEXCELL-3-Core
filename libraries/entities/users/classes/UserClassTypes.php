<?php

namespace Entities\Users\Classes;

use App\Core\AppEntity;
use Entities\Users\Models\UserClassTypeModel;

class UserClassTypes extends AppEntity
{
    public $strEntityName       = "Users";
    public $strDatabaseTable    = "user_class_type";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = UserClassTypeModel::class;
    public $strMainModelPrimary = "user_class_type_id";

}