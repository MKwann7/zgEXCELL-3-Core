<?php

namespace Entities\Contacts\Classes;

use App\Core\AppEntity;
use Entities\Contacts\Models\ContactGroupModel;

class ContactGroups extends AppEntity
{
    public $strEntityName       = "Contacts";
    public $strDatabaseTable    = "contact_group";
    public $strMainModelName    = ContactGroupModel::class;
    public $strMainModelPrimary = "contact_group_id";
    public $strDatabaseName     = "Main";
}