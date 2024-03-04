<?php

namespace App\website\vue\classes;

use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;

class VueComponentSortableList extends VueComponent
{
    protected string $id = "d97ed0b0-d044-4b06-a51b-061a945185ed";
    protected string $vueType = "compSortList";
    protected string $mountType = "no_mount";

    /**
     * VueComponentSortableList constructor.
     * @param AppModel|null $entity
     * @param VueComponentList|null $entityTable
     * @param array $props
     */
    public function __construct(?AppModel $entity = null, $props = [])
    {
        $this->setMixin("ContainerMixin");
        parent::__construct($entity);
    }

    protected function renderComponentDataAssignments() : string
    {
        return '
            thisData: "hello!",
        ';
    }

    protected function renderTemplate() : string
    {
        return '
            <table class="table table-striped no-top-border table-shadow" v-cloak>
                <tbody>
                      <slot />
                </tbody>
            </table>
        ';
    }
}