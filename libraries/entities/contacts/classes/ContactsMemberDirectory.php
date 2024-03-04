<?php

namespace Entities\Contacts\Classes;

use App\Core\AppEntity;
use Entities\Contacts\Models\EzcardMemberDirectoryRecordModel;

class ContactsMemberDirectory extends AppEntity
{
    public string $strEntityName       = "MemberDirectory";
    public $strDatabaseTable    = "directory_page_rel";
    public $strMainModelName    = EzcardMemberDirectoryRecordModel::class;
    public $strMainModelPrimary = "directory_page_rel_id";
    public $strDatabaseName     = "Modules";
}