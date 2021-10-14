<?php

namespace Entities\Activities\Classes;

use App\Core\App;
use App\Core\AppController;
use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Activities\Models\UserLogModel;

class UserLogs extends AppEntity
{
    public $strEntityName       = "Activities";
    public $strDatabaseName     = "Activity";
    public $strDatabaseTable    = "log_user";
    public $strMainModelName    = UserLogModel::class;
    public $strMainModelPrimary = "log_user_id";

    public function RegisterActivity($intUserId, $strAction = "NA", $strNote, $strEntityName = null, $intEntityID = null) : ExcellTransaction
    {
        $objLoggedInUser = $this->app->getActiveLoggedInUser();
        $objUserActivity = new UserLogModel();

        $objUserActivity->user_id = $intUserId;
        $objUserActivity->action = $strAction;
        $objUserActivity->note = $strNote;
        $objUserActivity->entity_name = $strEntityName;
        $objUserActivity->entity_id = $intEntityID;
        $objUserActivity->created_on = date("Y-m-d H:i:s");
        $objUserActivity->created_by = $objLoggedInUser->user_id;

        return static::createNew($objUserActivity);
    }

    public function GetUserActivity($intUserId, $dtmCutOffDate = null) : ExcellTransaction
    {
        $objConnectionResult = new ExcellTransaction();

        if ( empty($intUserId) || !isInteger($intUserId))
        {
            $objConnectionResult->Result->Success = false;
            $objConnectionResult->Result->Count = 0;
            $objConnectionResult->Result->Message = "You must supply a valid user id.";
            return $objConnectionResult;
        }

        return $this->getWhere(["user_id" => $intUserId],"created_on.DESC");
    }
}

