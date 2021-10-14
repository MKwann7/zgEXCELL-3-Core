<?php

namespace Entities\Contacts\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use Entities\Contacts\Models\ContactUserRelModel;

class ContactUserRels extends AppEntity
{
    public $strEntityName       = "Contacts";
    public $strDatabaseTable    = "contact_user_rel";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = ContactUserRelModel::class;
    public $strMainModelPrimary = "contact_user_rel_id";
}