<?php

namespace App\Website\Vue\Classes\Base;

use App\Core\AppModel;
use App\Utilities\Excell\ExcellCollection;
use App\website\Vue\Classes\VueHub;
use App\Website\Vue\Classes\VueProps;
use Entities\Users\Components\Vue\ConnectionWidget\ManageUserConnectionsListWidget;

abstract class VueBase
{
    protected $instanceId;
    protected $instanceName;
    protected $parentId;
    protected $parentLinkActions = [];
    protected $vueType;
    /** @var $defaultEntityModel AppModel */
    protected $defaultEntityModel;
    /** @var $defaultComponentInstanceId string */
    protected $defaultComponentInstanceId;
    /** @var $defaultComponentId string */
    protected $defaultComponentId;
    /** @var $defaultComponentAction string */
    protected $defaultComponentAction = "view";
    /** @var $defaultComponentProps VueProps[] */
    protected $defaultComponentProps = [];
    protected $props = [];
    protected $customProps = null;
    /** @var $components VueComponent[] */
    protected $components;
    protected $dynamicComponents = [];
    protected $selfAsApp = false;
    protected $helpers;
    protected $templateSource = "inline";
    protected $endpointUriAbstract = "";
    protected $defaultAction = "view";
    protected $template = "";

    public function getInstanceId() : string
    {
        return $this->instanceId;
    }

    public function getId()
    {
        return $this->id;
    }

    public static function getStaticId() : string
    {
        return (new static)->getId();
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function setParentId($parentId, $parentLinkActions = []) : self
    {
        $this->parentId = $parentId;
        $this->parentLinkActions = $parentLinkActions;
        return $this;
    }

    public function getInstanceName() : string
    {
        return $this->vueType . $this->instanceName;
    }

    public function getRef() : string
    {
        return $this->getInstanceName() . "Ref";
    }

    public function setDefaultAction($action) : self
    {
        $this->defaultAction = $action;
        return $this;
    }

    public function getDefaultAction() : string
    {
        return $this->defaultAction;
    }

    public function getTemplateSource()
    {
        return $this->templateSource;
    }

    public function getUriAbstract() : string
    {
        return $this->endpointUriAbstract;
    }

    public function activateRegisteredComponentByIdInModal(string $componentId, string $action, $hasParent = true,  $entity = "entity", $entityList = "entities", ?array $props = null, $source = "this", $show = true, $callback = "false", $hydrate = true): string
    {
        $component = $this->getComponentById($componentId);

        if ($component === null)
        {
            return 'ezLog("No component by Id: ' . $componentId . '");';
        }

        $strProps = $this->buildProps($props);

        return $source . '.loadComponentInModal(\'' . $component->getInstanceId() . '\', \'' . $component->getId() . '\', \'' . ($hasParent === true ? ($component->getParentId() ?? $this->getInstanceId()) : "") . '\', \'' . $action . '\',  \'Async Component\', ' . $entity . ', ' . $entityList . ', ' . $strProps . ', '. ($show === true ? "true" : "false") . ', '. ($hydrate === true ? "true" : "false") . ', '. $callback . ');';
    }

    public function activateDynamicComponentByIdInModal(string $componentId, string $componentParentId, string $action, $entity = "entity", $entityList = "entities", ?array $props = null, $source = "this", $show = true, $callback = "false", $hydrate = true): string
    {
        $strProps = $this->buildProps($props);

        return '
            let vueModal = '.$source.'.findModal('.$source.');
            vueModal.vc.loadComponentByStaticId(\'' . $componentId . '\', \'' . $componentParentId . '\', \'' . $action . '\', ' . $entity . ', ' . $entityList . ', ' . $strProps . ', ' . ($show === true ? "true" : "false") . ', '. ($hydrate === true ? "true" : "false") . ', '. $callback . ');
        ';
    }

    public function activateDynamicComponentById(string $componentId, string $componentParentId, string $action, $entity = "entity", $entityList = "entities", ?array $props = null, $source = "this", $show = true, $callback = "false", $hydrate = true): string
    {
        $strProps = $this->buildProps($props);

        return '
            let vc = '.$source.'.findVc('.$source.'); vc.loadComponentByStaticId(\'' . $componentId . '\', \'' . $componentParentId . '\', \'' . $action . '\', ' . $entity . ', ' . $entityList . ', ' . $strProps . ', ' . ($show === true ? "true" : "false") . ', '. ($hydrate === true ? "true" : "false") . ', '. $callback . ');
        ';
    }

    public function addDefaultComponent(VueComponent $component): self
    {
        $this->defaultEntityModel = $component->getComponentEntityModel();
        $this->defaultComponentInstanceId = $component->getInstanceId();
        $this->addComponent($component, true);
        return $this;
    }

    public function getDefaultComponent(): ?VueComponent
    {
        return $this->components[$this->defaultComponentInstanceId];
    }

    public function getComponents() : ?array
    {
        return $this->components;
    }

    public function getDefaultComponentInstanceId() : ?string
    {
        return $this->defaultComponentInstanceId;
    }

    public function getDefaultComponentId() : ?string
    {
        return $this->defaultComponentId;
    }

    public function setDefaultComponentId(string $componentId) : self
    {
        $this->defaultComponentId = $componentId;
        return $this;
    }

    public function getDefaultComponentAction() : ?string
    {
        return $this->defaultComponentAction;
    }

    public function setDefaultComponentAction(string $action) : self
    {
        $this->defaultComponentAction = $action;
        return $this;
    }

    public function setDefaultComponentProps(array $props) : self
    {
        $this->defaultComponentProps = $props;
        return $this;
    }

    public function setTemplateSource($source)
    {
        $this->templateSource = $source;
    }

    public function activateRegisteredComponentById(string $componentId, string $action, $hasParent = true, $entity = "entity", $entityList = "entities", ?array $props = null, $source = "this", $callback = "false", $hydrate = true): string
    {
        $component = $this->getComponentById($componentId);

        if ($component === null)
        {
            return '// No component found: ' . $componentId;
        }

        $strProps = $this->buildProps($props);

        return 'let vc = '.$source.'.findVc('.$source.'); vc.loadComponent(\'' . $component->getInstanceId() . '\', \'' . $component->getId() . '\', \'' . ($hasParent === true ? $component->getParentId() : "") . '\', \'' . $action . '\',  \'Async Component\', ' . $entity . ', ' . $entityList . ', ' . $strProps . ', true, ' . ($hydrate === true ? "true" : "false") . ', ' . $callback . ');';
    }

    protected function renderCustomPropsJavascriptObject() : string
    {
        $arProps = [];

        if ($this->customProps !== null)
        {
            foreach($this->customProps as $currPropName => $currPropValue)
            {
                $arProps[] = $currPropName . ": this." . $currPropName;
            }
        }

        if ($this->props !== null && is_array($this->props))
        {
            foreach($this->props as $currProp)
            {
                /** @var $currProp VueProps */
                if (is_a($currProp, VueProps::class))
                {
                    $arProps[] = $currProp->getName() . ": this." . $currProp->getName();
                }
            }
        }

        return "{". implode(",", $arProps) . "}";
    }

    public function addHelpers(VueComponent $component)
    {
        $this->helpers[$component->getInstanceId()] = $component;
    }

    public function loadProp(VueProps $prop)
    {
        $this->props[] = $prop;
    }

    public function addProp(VueProps $prop) : self
    {
        $this->props[] = $prop;
        return $this;
    }

    protected function buildProps($props = null): ?string
    {
        $columns = "{";

        if ($props === null || count($props) === 0)
        {
            return $columns . "}";
        }

        foreach ($props as $currPropLabel => $currProp)
        {
            $columns .= "{$currPropLabel}: {$currProp},";
        }

        return substr($columns, 0, -1) . "}";
    }

    public function loadProps(array $props) : self
    {
        foreach($props as $currProp)
        {
            $this->addProp($currProp);
        }

        return $this;
    }

    public function getProps() : array
    {
        return $this->props;
    }

    public function addComponent(VueComponent $component, $render = true)
    {
        $component->setRendered($render);
        $this->components[$component->getInstanceId()] = $component;

        return $this;
    }

    public function addComponentsList(ExcellCollection $component, $render = null)
    {
        $component->Foreach(function($currComponent) use ($render)
        {
            if ($render === null)
            {
                $this->addComponent($currComponent["component"], $currComponent["render"]);
            }
            else
            {
                $this->addComponent($currComponent["component"], $render);
            }
        });

        return $this;
    }

    public function getComponentByUniqueId(string $componentUniqueId) : ?VueComponent
    {
        if (empty($this->components[$componentUniqueId]))
        {
            return null;
        }

        return $this->components[$componentUniqueId];
    }

    public function getComponentById(string $componentId) : ?VueComponent
    {
        /** @var VueComponent $currComponent */
        foreach ($this->components as $currComponent)
        {
            if ($currComponent->getId() === $componentId)
            {
                return $currComponent;
            }
        }

        return null;
    }

    protected function renderTemplate() : string
    {
        return "";
    }

    protected function renderTemplateFromCache() : string
    {
        if ($this->template !== "") { return $this->template; }

        $this->template = $this->renderTemplate();

        return $this->template;
    }

    protected function renderDynamicComponentHydrationScript() : string
    {
        $dynamicComponentList = [];

        foreach($this->dynamicComponents as $currInstanceId => $currComponent)
        {
            $component = $currComponent["component"];
            /** @var VueComponent $component */
            $dynamicComponentList[] = '                    if (this.dyn' . str_replace("-", "", $component->getInstanceId()) . ' !== null) {
                this.dyn' . str_replace("-", "", $component->getInstanceId()).'Component = this.dyn'.str_replace("-", "", $component->getInstanceId()) . ';
                }';
        }

        return implode(PHP_EOL, $dynamicComponentList);
    }

    public function addDynamicComponent(VueComponent &$component, $alertParent = false, $render = false, $props = []) : self
    {
        if ($component->getDynamicComponentsForParent()->Count() > 0)
        {
            foreach($component->getDynamicComponentsForParent() as $currInstanceId => $currChildDynamicComponent)
            {
                if ($currChildDynamicComponent["alertParent"] === true)
                {
                    $this->dynamicComponents[$currInstanceId] = $currChildDynamicComponent;
                }
            }
        }

        $this->dynamicComponents[$component->getInstanceId()]["id"] = $component->getId();
        $this->dynamicComponents[$component->getInstanceId()]["component"] = $component;
        $this->dynamicComponents[$component->getInstanceId()]["alertParent"] = $alertParent;
        $this->dynamicComponents[$component->getInstanceId()]["render"] = $render;
        $this->dynamicComponents[$component->getInstanceId()]["props"] = $props;
        $this->addComponent($component, $render);
        return $this;
    }

    public function registerDefaultComponentViaRegisteredHub(VueHub &$vueHub, VueComponent $defaultComponent, $view, $props = []) : VueHub
    {
        $vueHub->loadProps($props);

        if ($defaultComponent !== null)
        {
            $vueHub->addDefaultComponent($defaultComponent);
            $this->addComponent($vueHub);
        }

        return $vueHub;
    }

    public function registerDefaultComponentViaHub(VueComponent $defaultComponent, $view, $props = []) : VueHub
    {
        $appDynamicComponent = new VueHub();
        $appDynamicComponent->loadProps($props);

        if ($defaultComponent !== null)
        {
            $appDynamicComponent->addDefaultComponent($defaultComponent);
            $this->addComponent($appDynamicComponent);
        }

        return $appDynamicComponent;
    }

    public function registerDynamicComponentViaHub($staticId, $view, $props = []) : VueHub
    {
        $appDynamicComponent = new VueHub();
        $appDynamicComponent->loadProps($props);

        if ($staticId !== null)
        {
            $appDynamicComponent->setDefaultComponentId($staticId)->setDefaultComponentAction($view)->setDefaultComponentProps($props);
        }

        $appDynamicComponent->setRendered(false);
        $this->addDynamicComponent($appDynamicComponent, true, false);

        return $appDynamicComponent;
    }

    public function registerDynamicComponentViaRegisteredHub(VueHub &$vueHub, VueComponent $component, $view, $props = []) : VueHub
    {
        $vueHub->loadProps($props);
        $vueHub->addDefaultComponent($component)->setDefaultComponentAction($view)->setDefaultComponentProps($props);
        $vueHub->setRendered(false);
        $this->addDynamicComponent($vueHub, true, false);

        return $vueHub;
    }

    public function registerDynamicComponent(VueComponent $component, $view, $props = []) : VueComponent
    {
        $component->loadProps($props);
        $component->setDefaultAction($view);
        $component->setRendered(false);
        $component->setParentId($this->getInstanceId());
        $this->addDynamicComponent($component, true, false);
        return $component;
    }

    public function getDynamicComponentsForParent() : ExcellCollection
    {
        $this->renderTemplateFromCache();

        $collection = new ExcellCollection();

        foreach($this->dynamicComponents as $currInstanceId => $currComponent)
        {
           if ($currComponent["alertParent"] === true)
           {
               $collection->Add($currInstanceId, $currComponent);
           }
        }

        return $collection;
    }

    protected function getDynamicComponentByInstanceId(string $componentInstanceId) : ?VueComponent
    {
        if (empty($this->dynamicComponents[$componentInstanceId])) { return null; }

        return $this->dynamicComponents[$componentInstanceId]["component"];
    }

    protected function renderDynamicComponentAssignments() : string
    {
        $dynamicComponentList = [];

        foreach($this->dynamicComponents as $currInstanceId => $currComponent)
        {
            $component = $currComponent["component"];
            /** @var VueComponent $component */
            $dynamicComponentList[] = 'dyn'.str_replace("-", "", $component->getInstanceId()).': {id:"' . $component->getId() . '", instanceId: "' . $component->getInstanceId() . '", title: "' . $component->getTitle() . '"}';
        }

        return implode(",", $dynamicComponentList);
    }

    protected function renderDynamicComponentDataAssignmentDataField() : string
    {
        $dynamicComponentList = [];

        foreach($this->dynamicComponents as $currInstanceId => $currComponent)
        {
            $component = $currComponent["component"];
            /** @var VueComponent $component */
            $dynamicComponentList[] = '                    dyn'.str_replace("-", "", $component->getInstanceId()).': null,';
            $dynamicComponentList[] = '                    dyn'.str_replace("-", "", $component->getInstanceId()).'Component: null,';
        }

        return implode(PHP_EOL, $dynamicComponentList);
    }

    protected function renderDynamicPropsFromAjaxRequest() : string
    {
        $arProps = [];

        if ($this->props !== null && is_array($this->props))
        {
            foreach($this->props as $currProp)
            {
                /** @var $currProp VueProps */
                if (is_a($currProp, VueProps::class))
                {
                    $arProps[] = "                    ". $currProp->getName() . ": null,";
                }
            }
        }

        if ($this->customProps !== null)
        {
            foreach($this->customProps as $currPropName => $currPropValue)
            {
                if (strpos($currPropName, "authSession") !== false) { continue; }

                $arProps[] = "                    ". $currPropName . ": null,";
            }
        }

        return implode(PHP_EOL, $arProps);
    }

    protected function registerAndRenderDynamicComponent(VueComponent $component, $view, $props = [], $options = null) : string
    {
        return $this->renderRegisteredDynamicComponent(
            $this->registerDynamicComponent(
                $component,
                $view,
                $props
            ),
            $options
        );
    }

    protected function renderRegisteredDynamicComponent(VueComponent $vueComponent, $vueOptions = null) : string
    {
        return ($this->getDynamicComponentByInstanceId($vueComponent->getInstanceId()) !== null) ? ('
            <div id="' . $vueComponent->getInstanceId() . '" class="vue-app-body-component" data-static-id="' . $vueComponent->getId() . '">
            <component :is="dyn' . str_replace("-", "", $vueComponent->getInstanceId()) . 'Component" ' . $this->renderProps($vueComponent->getProps()) . ' ' . $this->renderVueOptions($vueOptions) . '></component></div>
            ') : ("Component: " . $vueComponent->getTitle() . " could not be loaded.");
    }

    public function loadRegisteredComponents($baseBinding = "") : string
    {
        $data = "const rootVc = typeof this.findRootVc === 'function' ? this.findRootVc(this) : null;";
        foreach ($this->components as $currInstanceId => $currComponent)
        {
            /** @var VueComponent $currComponent */
            $data .= "
                if (rootVc === null || rootVc.getComponentById('{$currComponent->getId()}') === null)
                {
                    this.vc.addComponent(" . $currComponent->getComponentObject() . ", '{$baseBinding}');
                }
                else
                {
                    this.vc.addExistingComponent(rootVc.getComponentById('{$currComponent->getId()}'), '{$baseBinding}');
                }
            
            " . PHP_EOL;
        }

        foreach ($this->helpers as $currInstanceId => $currComponent)
        {
            /** @var VueComponent $currComponent */
            $data .= "this.vc.addHelper(" . $currComponent->getComponentObject() . ", '{$baseBinding}');";
        }

        return $data;
    }

    public function buildComponentList ()
    {
        $data = "";

        if (empty($this->components) || !is_array($this->components) ||  count($this->components) === 0)
        {
            return $data;
        }

        foreach ($this->components as $currInstanceId => $currComponent)
        {
            /** @var VueComponent $currComponent */
            if (!$currComponent->isRendered()) { continue; }
            $data .= '<div id="' . $currInstanceId . '" class="vue-app-body-component" style="display:none;">
                            ' . $currComponent->renderHtmlTagInstance() . '                        </div>';
        }

        return $data;
    }

    public function addEntity(AppModel $model) : self
    {
        $this->entity = $model;
        return $this;
    }

    public function getEntity() : AppModel
    {
        return $this->entity;
    }

    public function renderHtmlTagInstance($innerHtml = "")
    {
        if ($this->getTemplateSource() !== "inline")
        {
            return;
        }

        $strInstance = '<' . $this->getInstanceName();

        $strInstance .= $this->renderProps($this->props);

        $strInstance .= ' id="sub_'.$this->getInstanceId().'" ref="'.$this->getRef().'">'.$innerHtml.'</'.$this->getInstanceName().'>' . PHP_EOL;

        return $strInstance;
    }

    protected function renderVueOptions($options) : string
    {
        $optionAttributeString = "";

        if (empty($options) || !is_array($options) || count($options) === 0) { return $optionAttributeString; }

        foreach($options as $currOptionAttribute => $currOptionValue)
        {
            $optionAttributeString .= ' ' . $currOptionAttribute . '="'. $currOptionValue .'"';
        }

        return $optionAttributeString;
    }

    protected function renderProps($props) : string
    {
        $propAttributeString = "";

        if (empty($props) || !is_array($props) || count($props) === 0) { return $propAttributeString; }

        foreach($props as $currProp)
        {
            /** @var $currProp VueProps */
            if (is_a($currProp, VueProps::class))
            {
                $strType = $currProp->getType();
                switch(strtolower($strType))
                {
                    case "number":
                    case "string":
                        $propAttributeString .= ' ' . buildHyphenLowercaseFromPascalCase(ucwords($currProp->getName())) . '="'. $currProp->getValue() .'"';
                        break;
                    case "boolean":
                        $propAttributeString .= ' ' . buildHyphenLowercaseFromPascalCase(ucwords($currProp->getName()));
                        break;
                    case "object":
                    case "array":
                    case "json":
                        $propAttributeString .= ' :' . buildHyphenLowercaseFromPascalCase(ucwords($currProp->getName())) . '="'. $currProp->getValue() .'"';
                        break;
                    case "function":
                        $propAttributeString .= ' @' . buildHyphenLowercaseFromPascalCase(ucwords($currProp->getName())) . '="'. $currProp->getValue() .'"';
                        break;
                }
                continue;
            }
        }

        return $propAttributeString;
    }

    protected function renderDynamicProps($props) : string
    {
        $propAttributeString = "";

        if (empty($props) || !is_array($props) || count($props) === 0) { return $propAttributeString; }

        foreach($props as $currProp)
        {
            /** @var $currProp VueProps */
            if (is_a($currProp, VueProps::class))
            {
                $strType = $currProp->getType();
                switch(strtolower($strType))
                {
                    case "number":
                    case "string":
                        $propAttributeString .= ' ' . buildHyphenLowercaseFromPascalCase(ucwords($currProp->getName())) . '="'. $currProp->getValue() .'"';
                        break;
                    case "boolean":
                        $propAttributeString .= ' ' . buildHyphenLowercaseFromPascalCase(ucwords($currProp->getName()));
                        break;
                    case "object":
                    case "array":
                    case "json":
                        $propAttributeString .= ' v-bind="'. $currProp->getValue() .'"';
                        break;
                    case "function":
                        $propAttributeString .= ' @' . buildHyphenLowercaseFromPascalCase(ucwords($currProp->getName())) . '="'. $currProp->getValue() .'"';
                        break;
                }
                continue;
            }
        }

        return $propAttributeString;
    }

    public function renderDynamicComponentTag() : string
    {
        if ($this->getTemplateSource() !== "inline")
        {
            return "";
        }

        $strInstance = '<component :is="' . $this->getInstanceName() . '"';

        if ($this->props !== null && is_array($this->props) && count($this->props) > 0)
        {
            foreach($this->props as $currProp)
            {
                /** @var $currProp VueProps */
                if (is_a($currProp, VueProps::class))
                {
                    $strType = $currProp->getType();
                    switch(strtolower($strType))
                    {
                        case "number":
                        case "string":
                        case "boolean":
                            $strInstance .= ' ' . buildHyphenLowercaseFromPascalCase(ucwords($currProp->getName())) . '="'. $currProp->getValue() .'"';
                            break;
                        case "object":
                        case "array":
                        case "json":
                            $strInstance .= ' :' . buildHyphenLowercaseFromPascalCase(ucwords($currProp->getName())) . '="'. $currProp->getValue() .'"';
                            break;
                        case "function":
                            $strInstance .= ' @' . buildHyphenLowercaseFromPascalCase(ucwords($currProp->getName())) . '="'. $currProp->getValue() .'"';
                            break;
                    }
                    continue;
                }
            }
        }

        $strInstance .= ' ref="'.$this->getRef().'"></component>' . PHP_EOL;

        return $strInstance;
    }

    public function installJavascriptComponent() : string
    {
        $strComponent = '
            Vue.component(\'' . $this->getInstanceName() . '\', {
                render: h => h(' . $this->getInstanceName() . '),
                ';
        if ($this->props !== null && is_array($this->props))
        {
            $strComponent .= "props: ['";

            $arProps = [];

            foreach($this->props as $currProp)
            {
                /** @var $currProp VueProps */
                if (is_a($currProp, VueProps::class))
                {
                    $arProps[] = $currProp->getName();
                }
            }

            $strComponent .= implode("','", $arProps) . "']";
        }

        $strComponent .= '});';

        return $this->buildHelpers() . PHP_EOL . $this->buildTemplate() . PHP_EOL . $strComponent . PHP_EOL;
    }

    protected function buildPropsJavaScriptObject($props = null) : array
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

        if ($this->customProps !== null)
        {
            foreach($this->customProps as $currPropName => $currPropValue)
            {
                if (strpos($currPropName, "authSession") !== false) { continue; }

                if (isBoolean($currPropValue) || $currPropValue === "true" || $currPropValue === "false")
                {
                    $arProps[] = $currPropName . ": { type: Boolean }";
                }
                elseif (isInteger($currPropValue))
                {
                    $arProps[] = $currPropName . ": { type: Number }";
                }
                else
                {
                    $arProps[] = $currPropName . ": { type: Object }";
                }
            }
        }

        return $arProps;
    }

    protected function buildTemplate() : string
    {
        return "";
    }

    protected function buildHelpers() : string
    {
        return "";
    }
}