<?php

namespace App\Website\Vue\Classes;

use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueBase;
use App\Website\Vue\Classes\Base\VueComponent;

class VueModal extends VueBase
{
    protected $title;
    protected $id = "15de09a6-6120-4f82-a6bc-bdbfc839834e";
    protected $user;
    protected $width;
    protected $height;
    protected $body;
    protected $vueType = "modal";
    protected $props   = [];

    public function __construct($title = "My Dialog", $width = 250, $height = 250, $body = null)
    {
        global $app;

        $this->instanceId   = getGuid();
        $this->instanceName = preg_replace("/[^A-Za-z0-9]/", '', $this->instanceId);
        $this->title        = new VueProps("ModalTitle", "string", $title);
        $this->width        = $width;
        $this->height       = $height;
        $this->body         = $body;
        $this->user         = $app->getActiveLoggedInUser();
        $mainEntityList     = new VueProps("mainEntityList", "array", "mainEntityList");
        $this->addProp($mainEntityList);
    }

    public function renderModalRefLoad(VueComponent $component, $action, $title, $entity = "null", $entities): string
    {
        return 'this.$refs.' . $this->getRef() . '.$children[0].loadModal(\'' . $action . '\', this, \'' . $component->getInstanceId() . '\',  \'' . $component->getId() . '\', null, \'' . $title . '\',  ' . (!empty($entity) ? $entity : "null") . ', ' . $entities . ', null, true);' . PHP_EOL;
    }

    public function addComponent(VueComponent $component, $render = false)
    {
        $component->setModal($this);
        return parent::addComponent($component, $render);
    }

    public function activateRegisteredComponentById(string $componentId, string $action, $hasParent = true, $entity = "entity", $entityList = "entities", ?array $props = null, $source = "this", $callback = "false", $hydrate = true): string
    {
        $component = $this->getComponentById($componentId);

        if ($component === null)
        {
            return '';
        }

        $strProps = $this->buildProps($props);

        return $source . '.loadComponent(\'' . $component->getInstanceId() . '\', \'' . $component->getId() . '\', \'' . ($hasParent === true ? ($component->getParentId() ?? $this->getInstanceId()) : "") . '\', \'' . $action . '\',  \'Async Component\', ' . $source . '.entity, ' . $source . '.entities, ' . $strProps . ', true, ' . ($hydrate === true ? "true" : "false") . ');';
    }

    protected function buildTemplate (): string
    {
        return 'const ' . $this->getInstanceName() . ' = {
            name: \'' . $this->getInstanceName() . '\',
            props: { ' . (implode(", ", $this->buildPropsJavaScriptObject())) . ' },
            data: function() {
                return {
                    vc: null,
                    hasParent: false,
                    activeComponentId: \'' . $this->getDefaultComponentInstanceId() . '\',
                    userId: \'' . $this->user->sys_row_id . '\',
                    userNum: \'' . $this->user->user_id . '\',
                    modal_title: \'' . str_replace('\'', '\\\'', $this->title->getValue()) . '\',
                    modalDefaultWidth: \'' . $this->width . '\',
                    modalWidth: \'' . $this->width . '\',
                    showModal: false,
                };
            },
            template: `
    <div class="universal-float-shield vue-float-shield" v-show="showModal">
        <div class="vue-float-shield-inner">
            <div class="zgpopup-dialog-box" 
                role="dialog"
                aria-labelledby="modalTitle"
                aria-describedby="modalDescription"
                v-bind:style="{ width: modalWidth + \'px\' }"
                >
                <div class="zgpopup-dialog-box-inner">
                    <header class="zgpopup-dialog-header">
                        <slot name="header">
                            <h2 class="offset-slidedown-box pop-up-dialog-main-title">
                                <a v-show="componentHasParent()" v-on:click="backToComponent()" id="back-to-entity-list" class="back-to-entity-list pointer"></a>
                                <span class="pop-up-dialog-main-title-text">{{ modal_title }}</span>
                                <div style="right:16px;top:16px !important;display:block;"
                                    type="button"
                                    class="general-dialog-close"
                                    @click="close"
                                  >
                                </div>
                            </h2>
                        </slot>
                    </header>
                    <section id="vue-modal-body-' . $this->getInstanceName() . '" class="zgpopup-dialog-body">
                        <div class="vue-modal-wrapper">
                        ' . $this->buildComponentList() . '
                        </div>
                        <div style="clear:both"></div>
                    </section>
                    <footer class="zgpopup-dialog-footer">
                        <slot name="footer"></slot>
                    </footer>
                </div>
            </div>
        </div>
    </div>  `,
            mounted() {
                this.vc = new vueComponents(this, document.getElementById("vue-modal-body-' . $this->getInstanceName() . '"), "modal");
                ' . $this->loadRegisteredComponents() . '
            },
            methods: {
                loadModal: function(action, self, instanceId, id, parentInstanceId, title, entity, entities, props, show, hydrate)
                {                    
                    if (typeof id === "undefined" || id === null)
                    {
                        console.log("No Widget id handed in to modal.");
                    }
                    
                    if (typeof instanceId === "undefined" || instanceId === null)
                    {
                        console.log("Instance Id for widget id " + id + " missing for modal.");
                    }
                    
                    this.vc.loadComponent(instanceId, id, parentInstanceId, action, title, entity, entities, props, show, hydrate);
                },
                show: function()
                {      
                    this.showModal = true;
                },
                close: function($event)
                {
                    this.vc.runComponentDismissalScript();
                    this.vc.hideComponents();
                    this.showModal = false;
                },
                backToComponent: function(methodCall)
                {
                    this.vc.backToComponent(methodCall);
                },
                componentHasParent: function()
                {
                    if (this.vc === null) { return false; }
                    return this.vc.componentHasParent(this.activeComponentId);
                },
                addAjaxClass: function()
                {
                    let bodyDialogBox = document.getElementsByClassName("zgpopup-dialog-body");
                    bodyDialogBox[bodyDialogBox.length - 1].classList.add("ajax-loading-anim");
                    this.$forceUpdate();
                },
                removeAjaxClass: function()
                {
                    let bodyDialogBox = document.getElementsByClassName("zgpopup-dialog-body");
                    bodyDialogBox[bodyDialogBox.length - 1].classList.remove("ajax-loading-anim");
                    this.$forceUpdate();
                },
                setWidth: function(width)
                {
                    if (width !== "auto") { this.modalWidth = width; return; }
                    this.modalWidth = this.modalDefaultWidth;
                },
            }
        };';
    }

    protected function buildHelpers (): string
    {
        $helper = '';

        foreach ($this->components as $currComponent)
        {
            $helper .= $currComponent->installJavascriptComponent();
        }

        return $helper;
    }
}