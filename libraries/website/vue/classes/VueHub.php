<?php

namespace App\website\Vue\Classes;

use App\Website\Vue\Classes\Base\VueComponent;

class VueHub extends VueComponent
{
    protected $id = "f0dd1eab-d55f-47c0-aeae-eb17937d8f82";
    protected $vueType = "hub";
    protected $user;
    protected $title = "My Hub";
    protected $noMount = true;

    public function __construct($props = null)
    {
        global $app;

        $this->instanceId   = getGuid();
        $this->instanceName = preg_replace("/[^A-Za-z0-9]/", '', $this->instanceId);
        $this->user         = $app->getActiveLoggedInUser();

        parent::__construct();
    }

    protected function renderComponentDataAssignments() : string
    {
        return '
            vc: null,
            hasParent: false,
            activeComponentId: \'' . $this->getDefaultComponentInstanceId() . '\',
            userId: \'' . $this->user->sys_row_id . '\',
            userNum: \'' . $this->user->user_id . '\',
        ';
    }

    protected function buildPropsJavaScriptObject($props = NULL) : array
    {
        $arProps = [];

        if ($this->props !== null && is_array($this->props))
        {
            foreach($this->props as $currProp)
            {
                /** @var $currProp VueProps */
                if (is_a($currProp, VueProps::class))
                {
                    $arProps[] = $currProp->getName() . ': { type: '. $currProp->getType() . " }";
                }
            }
        }

        return $arProps;
    }

    protected function renderTemplate() : string
    {
        return '
        <div class="formwrapper-outer">
            <section id="vue-hub-body-' . $this->getInstanceName() . '" class="vue-app-body formwrapper-control">
                <div class="vue-modal-wrapper formwrapper-control">
                    ' . $this->buildComponentList() . '
                </div>
            </section>
        </div>
        ';
    }

    protected function renderComponentMethods (): string
    {
        return '
        ';
    }

    protected function mountLoadedComponents() : string
    {
        $mountingScript = "let unMountedComponent = null;";

        foreach ($this->components as $currInstanceId => $currComponent)
        {
            /** @var VueComponent $currComponent */
            $mountingScript .= "
                unMountedComponent = rootVc.getComponentByInstanceId('".$currComponent->getInstanceId()."');
                
                if (typeof unMountedComponent.instance === 'undefined') 
                {
                    let ComponentClass = Vue.extend(unMountedComponent.rawInstance);
                    
                    newComponent = new ComponentClass({
                        parent: this
                    }).\$mount(document.getElementById('sub_' +unMountedComponent.instanceId));
                    
                    unMountedComponent.instance = newComponent;
                    
                    ".(($this->getDefaultComponent()->getInstanceId() !== $currComponent->getInstanceId()) ? "this.vc.hydrate('{$currComponent->getInstanceId()}', '{$currComponent->getParentId()}', '{$currComponent->getDefaultAction()}', this.entity, this.mainEntityList, ' . $this->renderCustomPropsJavascriptObject() . ', false)" : "" )."
                }
            
            " . PHP_EOL;
        }

        return $mountingScript;
    }

    protected function loadDefaultComponentScript() : string
    {
        if ($this->getDefaultComponent() === null)
        {
            return '
                this.vc.loadComponentByStaticId( "' . $this->getDefaultComponentId() .'", "", "' . $this->getDefaultComponentAction() .'", this.entity, this.mainEntityList, ' . $this->renderCustomPropsJavascriptObject() . ',  true, true, function(component) {
                    
                });           
            ';

        }

        return 'this.vc.loadComponent("'.$this->getDefaultComponent()->getInstanceId().'", "'.$this->getDefaultComponent()->getId().'", "'.$this->getDefaultComponent()->getParentId().'", "'.$this->getDefaultComponent()->getDefaultAction().'", "", this.entity, this.mainEntityList, ' . $this->renderCustomPropsJavascriptObject() . ', true, true);';
    }

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentHydrationScript() . '
            let self = this;
            this.$forceUpdate();
        ';
    }

    protected function renderComponentMountedScript(): string
    {
        return '
            this.vc = new vueComponents(this, document.getElementById("vue-hub-body-' . $this->getInstanceName() . '"), "hub");    
            ' . $this->loadRegisteredComponents() . '
            this.vc.hideComponents();
            this.vc.setInitialComponentLoad(); 
            ' . $this->mountLoadedComponents() . '
            ' . $this->loadDefaultComponentScript() . '';
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