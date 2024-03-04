<?php

namespace Entities\Cards\Components\Vue\DigitalCardWidget\V1;

use Entities\Cards\Components\Vue\DigitalCardWidget\Assets\DigitalPageComponent;
use Entities\Cards\Components\Vue\DigitalCardWidget\Assets\SharedVuePageMethods;

class DigitalCardPageWidget extends DigitalPageComponent
{
    protected string $id = "3efc2dc7-ae52-4373-bbe0-350903204c39";

    protected function renderComponentDataAssignments() : string
    {
        return SharedVuePageMethods::dataAssignments($this) . "
        ";
    }

    protected function renderComponentHydrationScript() : string
    {
        return SharedVuePageMethods::hydration($this) . '
        ';
    }

    protected function renderComponentMethods() : string
    {
        return SharedVuePageMethods::methods($this) . '
        ';
    }

    protected function renderComponentComputedValues(): string
    {
        return SharedVuePageMethods::computed($this) . '
        ';
    }

    protected function renderTemplate(): string
    {
        return '
            <div>
                <div class="app-page">
                    <div v-show="customPage === false" class="app-page-content">
                        <div class="app-page-content-inner" v-html="renderCardContent"></div>
                    </div>
                    <div v-show="customPage === true" class="app-page-content">
                        <component ref="dynPageWidgetComponentRef" :is="dynPageWidgetComponent"></component>
                    </div>
                </div>
            </div>
                ';
    }
}