<?php

namespace Entities\Cards\Components\Vue\DigitalCardWidget\V5;

use Entities\Cards\Components\Vue\DigitalCardWidget\Assets\DigitalPageComponent;
use Entities\Cards\Components\Vue\DigitalCardWidget\Assets\SharedVuePageMethods;

class DigitalCardPageWidget extends DigitalPageComponent
{
    protected string $id = "c4dfc50d-f6f0-40a2-abdc-bfccf9f076b4";

    protected function renderComponentDataAssignments() : string
    {
        return SharedVuePageMethods::methods($this) . "
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
                    <div v-show="page != null && noTitle === false && customPage === false" class="app-page-title app-page-editor-text-transparent" v-on:click="backToComponent()">
                        <a v-show="hasParent" class="back-to-entity-list pointer"></a>
                        <span v-if="editor === false">{{ page.title }}</span>
                        <span v-if="editor === true"><span class="fas fa-edit"></span><input v-model="page.title" class="app-page-title app-page-editor-text-transparent" v-on:blur="updatePageTitle" /></span>
                    </div>
                    <div v-show="customPage === false" class="app-page-content">
                        <div class="app-page-content-inner" v-html="renderCardContent"></div>
                    </div>
                    <div v-show="customPage === true" class="app-page-content">
                        <component ref="dynPageWidgetComponentRef"  :is="dynPageWidgetComponent"></component>
                    </div>
                </div>
            </div>
                ';
    }
}