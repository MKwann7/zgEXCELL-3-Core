<?php

namespace Entities\Posts\Classes;

use App\Core\AppEntity;
use Entities\Posts\Models\PostModel;

class Posts extends AppEntity
{
    public string $strEntityName       = "posts";
    public $strDatabaseTable    = "post";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = PostModel::class;
    public $strMainModelPrimary = "post_id";
    public $isPrimaryModule     = true;
}
