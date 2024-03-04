<?php

namespace Entities\Posts\Models;

use App\Core\AppModel;

class PostModel extends AppModel
{
    protected string $EntityName = "Posts";
    protected string $ModelName = "Post";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [];
    }
}