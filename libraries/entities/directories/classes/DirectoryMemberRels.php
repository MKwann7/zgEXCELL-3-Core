<?php

namespace Entities\Directories\Classes;

use App\Core\AppEntity;
use Entities\Directories\Models\DirectoryMemberRelModel;

class DirectoryMemberRels extends AppEntity
{
    public string $strEntityName    = "Directories";
    public $strDatabaseTable        = "directory_member_rel";
    public $strDatabaseName         = "Apps";
    public $strMainModelName        = DirectoryMemberRelModel::class;
    public $strMainModelPrimary     = "directory_member_rel_id";
}