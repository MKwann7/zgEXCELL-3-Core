<?php

namespace Entities\Media\Models;

use App\Core\AppModel;

class MediaModel extends AppModel
{
    protected string $EntityName = "Media";
    protected string $ModelName = "Media";

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