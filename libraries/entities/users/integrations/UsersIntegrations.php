<?php

namespace Entities\Users\Integrations;

use App\Core\AppEntity;
use Entities\Users\Integrations\Models\UsersIntegrationModel;

class UsersIntegrations extends AppEntity
{
    public string $strEntityName       = "Users";
    public $strDatabaseTable    = "integrations_users";
    public $strDatabaseName     = "Integration";
    public $strMainModelName    = UsersIntegrationModel::class;
    public $strMainModelPrimary = "integrations_user_id";
}