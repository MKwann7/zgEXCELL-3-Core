<?php

namespace Entities\Quotes\Classes;

use App\Core\AppEntity;
use Entities\Quotes\Models\QuoteModel;

class Quotes extends AppEntity
{
    public string $strEntityName       = "posts";
    public $strDatabaseTable    = "post";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = QuoteModel::class;
    public $strMainModelPrimary = "post_id";
    public $isPrimaryModule     = true;
}
