<?php

namespace App\Website\Vue\Classes;

use App\Core\App;
use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueBase;
use App\Website\Vue\Classes\Base\VueComponent;

class VueModal extends VueComponent
{
    protected VueProps $titleProp;
    protected string $id = "15de09a6-6120-4f82-a6bc-bdbfc839834e";
    protected $user;
    protected $width;
    protected $height;
    protected $body;
    protected string $vueType = "modal";
    protected $props   = [];

    public function __construct($title = "My Dialog", $width = 250, $height = 250, $body = null)
    {
        global $app;

        $this->instanceId   = getGuid();
        $this->instanceName = preg_replace("/[^A-Za-z0-9]/", '', $this->instanceId);
        $this->titleProp        = new VueProps("ModalTitle", "string", $title);
        $this->width        = $width;
        $this->height       = $height;
        $this->body         = $body;
        $this->user         = $app->getActiveLoggedInUser();
    }

    public function renderModalRefLoad(VueComponent $component, $action, $title, $entity = "null", $entities = []): string
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

    protected function renderComponentDataAssignments() : string
    {
        return "
            vc: null,
            hasParent: false,
            activeComponentId: '',
            modal_title: '" . str_replace('\'', '\\\'', $this->title) . "',
            modalDefaultWidth: '" . $this->width . "',
            modalWidth: '" . $this->width . "',
            showModal: false,
            hideModal: true,
        ";
    }

    protected function renderTemplate() : string
    {
        return '
            <div class="universal-float-shield vue-float-shield" v-bind:class="{activeModal: showModal == true, closedModal: hideModal == true}">
            <div class="vue-float-shield-inner">
                <div class="zgpopup-dialog-box dialog-theme-default" 
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
        </div>
        ';
    }

    protected function renderComponentMountedScript(): string
    {
        return '
            this.vc = new vueComponents(this, document.getElementById("vue-modal-body-' . $this->getInstanceName() . '"), "modal");
            ' . $this->loadRegisteredComponents() . '
        ';
    }

    protected function renderDispatchEvents(): string
    {
        return parent::renderDispatchEvents() . '
            dispatch.register("close_modal", this, "close")
        ';
    }

    protected function renderComponentMethods(): string
    {
        global $app;
        /** @var App $app */
        $appType = $app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label", "application_type")->value ?? "default";
        return '
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
                ' . ($appType !== 'default' ? '
                show: function()
                {
                    self = this
                    this.hideModal = false
                    setTimeout(function() {
                        self.showModal = true
                    }, 200);
                },
                close: function($event)
                {
                    self = this
                    this.showModal = false
                    setTimeout(function() {
                        self.hideModal = true
                        self.vc.runComponentDismissalScript()
                        self.vc.hideComponents()
                    }, 200);
                },
                ' : '
                show: function()
                {
                    this.hideModal = false
                    this.showModal = true
                },
                close: function($event)
                {
                    this.vc.runComponentDismissalScript()
                    this.vc.hideComponents()
                    this.showModal = false
                    this.hideModal = true
                },
                '). '
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
                    let bodyDialogBox = document.getElementsByClassName("zgpopup-dialog-body")
                    bodyDialogBox[bodyDialogBox.length - 1].classList.add("ajax-loading-anim")
                    this.$forceUpdate();
                },
                removeAjaxClass: function()
                {
                    let bodyDialogBox = document.getElementsByClassName("zgpopup-dialog-body")
                    bodyDialogBox[bodyDialogBox.length - 1].classList.remove("ajax-loading-anim")
                    this.$forceUpdate()
                },
                setWidth: function(width)
                {
                    if (width !== "auto") { this.modalWidth = width; return; }
                    this.modalWidth = this.modalDefaultWidth;
                },
        ';
    }

    protected function buildHelpers (): string
    {
        $helper = '';
        if (!empty($this->components)) {
            foreach ($this->components as $currComponent) {
                $helper .= $currComponent->installJavascriptComponent();
            }
        }

        return $helper;
    }
}