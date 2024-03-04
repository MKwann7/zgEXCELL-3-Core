<?php

namespace Entities\Pages\Models;

use App\Core\AppModel;

class PageBlockModel extends AppModel
{
    protected string $EntityName = "Pages";
    protected string $ModelName = "PageBlock";

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