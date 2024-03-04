<?php

namespace Entities\Quotes\Models;

use App\Core\AppModel;

class QuoteModel extends AppModel
{
    protected string $EntityName = "Quotes";
    protected string $ModelName = "Quote";

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