<?php

namespace Entities\Notes\Classes;

use App\Core\AppEntity;
use Entities\Notes\Models\NoteModel;

class Notes extends AppEntity
{
    public $strEntityName       = "Notes";
    public $strDatabaseTable    = "note";
    public $strDatabaseName     = "Crm";
    public $strMainModelName    = NoteModel::class;
    public $strMainModelPrimary = "note_id";
    public $isPrimaryModule     = true;

    public function __construct()
    {
        parent::__construct();
    }
}