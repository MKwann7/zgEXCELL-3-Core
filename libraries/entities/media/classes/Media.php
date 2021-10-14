<?php

namespace Entities\Media\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use Entities\Media\Models\MediaModel;

class Media extends AppEntity
{
    public $strEntityName       = "Media";
    public $strDatabaseTable    = "media";
    public $strDatabaseName     = "Media";
    public $strMainModelName    = MediaModel::class;
    public $strMainModelPrimary = "media_id";
    public $isPrimaryModule     = true;
}
