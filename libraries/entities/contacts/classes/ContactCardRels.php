<?php

namespace Entities\Contacts\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use Entities\Contacts\Models\ContactCardRelModel;

class ContactCardRels extends AppEntity
{
    public $strEntityName       = "Contacts";
    public $strDatabaseTable    = "contact_card_rel";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = ContactCardRelModel::class;
    public $strMainModelPrimary = "contact_card_rel_id";
}