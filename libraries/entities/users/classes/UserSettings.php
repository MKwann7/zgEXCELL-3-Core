<?php

namespace Entities\Users\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Users\Models\UserSettingModel;

class UserSettings extends AppEntity
{
    public $strEntityName       = "Users";
    public $strDatabaseTable    = "user_setting";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = UserSettingModel::class;
    public $strMainModelPrimary = "user_setting_id";

    public function getByUserId($userId) : ExcellTransaction
    {
        return $this->getWhere(["user_id" => $userId]);
    }
}
