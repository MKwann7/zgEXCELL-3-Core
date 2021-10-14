<?php

namespace App\website\vue\classes;

use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;

class VueComponentListTable extends VueComponent
{
    protected $id = "6293492c-21f5-489d-b88c-9138edfeaee7";
    protected $entityRow;
    protected $vueType = "compListTable";
    protected $noMount = true;

    public function __construct(?AppModel $entity = null, ?VueComponentListRow $entityRow = null, ?array $props = [])
    {
        $this->entityRow = $entityRow;
        $this->setMixin("ElementMixin");
        $this->setDirective(["handle"=>"HandleDirective"]);
        if (!empty($props)) { $this->loadProps($props); }
        parent::__construct($entity);
    }

    protected function renderTemplate() : string
    {
        return '
            <div class="entityDetailsInner">
                here!!
            </div>
        ';
    }
}