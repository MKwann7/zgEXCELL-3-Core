<?php

namespace App\Website\Vue\Classes;

use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;

class VueComponentList extends VueComponent
{
    protected $entityTable;
    protected $sortableList;
    protected $vueType = "compList";
    protected $noMount = true;
    protected $noEntitiesWarning = "There are no entities in this module.";

    public function __construct(?AppModel $entity = null, ?VueComponentListTable $entityTable = null, ?VueComponentSortableList $sortableList = null, $props = [])
    {
        $this->sortableList = $sortableList;
        $this->entityTable = $entityTable;
        $this->loadProps($props);
        parent::__construct($entity);

        if ($sortableList !== null) {
            $this->addDynamicComponent($sortableList, true);
        }
        if ($entityTable !== null) {
            $this->addDynamicComponent($entityTable, true);
        }
    }
}