<?php

namespace Entities\Directories\Classes;

use App\Core\AppEntity;
use Entities\Directories\Models\DirectoryMemberFieldModel;

class DirectoryMemberFields extends AppEntity
{
    public string $strEntityName    = "Directories";
    public $strDatabaseTable        = "directory_member_field";
    public $strDatabaseName         = "Apps";
    public $strMainModelName        = DirectoryMemberFieldModel::class;
    public $strMainModelPrimary     = "directory_member_field_id";
}