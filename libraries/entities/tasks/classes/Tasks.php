<?php

namespace Entities\Tasks\Classes;

use App\Core\AppEntity;
use Entities\Tasks\Models\TaskModel;

class Tasks extends AppEntity
{
    public string $strEntityName       = "Tasks";
    public $strDatabaseTable    = "task";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = TaskModel::class;
    public $strMainModelPrimary = "task_id";
    public $isPrimaryModule     = true;
}
