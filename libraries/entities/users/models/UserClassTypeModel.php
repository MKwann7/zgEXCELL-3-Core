<?php

namespace Entities\Users\Models;

use App\Core\AppModel;

class UserClassTypeModel extends AppModel
{
    protected $EntityName = "Users";
    protected $ModelName = "UserClassModel";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }
}