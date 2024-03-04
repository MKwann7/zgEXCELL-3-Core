<?php

namespace Entities\Cards\Classes;

use App\Core\AppEntity;
use Entities\Cards\Models\CardUserCredentialModel;

class CardUserCredentials extends AppEntity
{
    public string $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_user_credentials";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardUserCredentialModel::class;
    public $strMainModelPrimary = "card_user_credential_id";
}