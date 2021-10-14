<?php

namespace App\Website\Vue\Classes;

use App\Core\AppModel;
use App\Utilities\Caret\ExcellCarets;
use App\Website\Vue\Classes\Base\VueBase;
use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\Breadcrumbs\VueBreadcrumbsVer1;
use Entities\Cart\Components\Vue\CartWidget\CartWidget;

abstract class VueApp extends VueBase
{
    /** @var VueModal $modal */
    protected $modal;
    /** @var AppModel $entity */
    protected $entity;
    protected $domId;
    protected $selfAsApp = false;
    protected $domHtmlTag;
    protected $breadcrumb;
    protected $baseBinding = "";
    protected $vueSlickSort = [];
    protected $endpointUriBase = "";
    protected $componentAbstracts = [];
    protected $vueType       = "app";
    protected $appNamePlural = "Entities";
    protected $appNameSingular = "Entity";

    public function __construct($domId, VueModal &$modal = null)
    {
        $this->domId = preg_replace("/[^A-Za-z0-9]/", '', $domId);
        $this->domHtmlTag = preg_replace("/[^A-Za-z0-9]/", '', $domId);
        $this->instanceId = getGuid();
        $this->instanceName = preg_replace("/[^A-Za-z0-9]/", '', $this->instanceId);

        $this->breadcrumb = new VueBreadcrumbsVer1();
        $this->addComponent($this->breadcrumb, false);

        if ($modal !== null)
        {
            $this->addModal($modal);
        }

        global $app;
        $this->baseBinding = $app->strActivePortalBindin;
    }

    protected function renderDeclarations() : ?string
    {
        return $this->renderSlickSortDeclarations();
    }

    protected function renderSlickSortDeclarations() : ?string
    {
        if (count($this->vueSlickSort) === 0)
        {
            return '';
        }

        return "const { " . implode(", ", $this->vueSlickSort) . " } = window.VueSlicksort;" . PHP_EOL;
    }

    public function enableSlickSortContainerMixin() : self
    {
        $this->vueSlickSort[] = "ContainerMixin";
        return $this;
    }

    public function enableSlickSortElementMixin() : self
    {
        $this->vueSlickSort[] = "ElementMixin";
        return $this;
    }

    public function enableSlickSortHandleDirective() : self
    {
        $this->vueSlickSort[] = "HandleDirective";
        return $this;
    }

    public function enableSlickSortList() : self
    {
        $this->vueSlickSort[] = "SlickList";
        return $this;
    }

    public function enableSlickSortItem() : self
    {
        $this->vueSlickSort[] = "SlickItem";
        return $this;
    }

    public function getAppTitle() : string
    {
        if (empty($this->appTitle))
        {
            return $this->appNamePlural;
        }

        return $this->appTitle;
    }

    public function getAppNameSingular() : string
    {
        return $this->appNameSingular;
    }

    public function getAppNamePlural() : string
    {
        return $this->appNamePlural;
    }

    public function getAppId() : string
    {
        return $this->domId;
    }

    public function addModal(VueModal $modal) : self
    {
        $this->modal = $modal;
        return $this;
    }

    public function getModal() : VueModal
    {
        return $this->modal;
    }

    public function getBreadcrumb() : VueBreadcrumbs
    {
        return $this->breadcrumb;
    }

    public function setUriBase($uriBase) : self
    {
        $this->endpointUriBase = $uriBase;
        return $this;
    }

    public function getUriBase() : string
    {
        return $this->endpointUriBase;
    }

    public function registerComponentAbstracts(array $abstracts) : self
    {
        foreach ($abstracts as $currComponentId => $currUriAbstract)
        {
            $this->componentAbstracts[$currComponentId] = $currUriAbstract;
        }

        return $this;
    }

    public function renderVueComponentRefLoad(VueComponent $component, $action, $title, $entity = "null", $entities): string
    {
        return 'this.vc.loadComponent(\'' . $component->getInstanceId() . '\',  \'' . $component->getId() . '\', null, \'' . $title . '\',  ' . (!empty($entity) ? $entity : "null") . ', ' . $entities . ', null, true, true);' . PHP_EOL;
    }

    public function renderComponentRefLoad(VueComponent $component, $action, $title, $entity = "null", $entities): string
    {
        return 'this.$refs.' . $this->getRef() . '.$children[0].loadModal(\'' . $action . '\', this, \'' . $component->getInstanceId() . '\',  \'' . $component->getId() . '\', null, \'' . $title . '\',  ' . (!empty($entity) ? $entity : "null") . ', ' . $entities . ', null, true, true);' . PHP_EOL;
    }

    protected function addSelfAsComponent() : void
    {
        $this->selfAsApp = true;
    }

    public function activateRegisteredComponentById(string $componentId, string $action, $hasParent = true, $entity = "entity", $entityList = "entities", ?array $props = null, $source = "this",  $callback = "false", $hydrate = "true"): string
    {
        $component = $this->getComponentById($componentId);

        if ($component === null)
        {
            return '// NO COMPONENT: ' . $componentId;
        }

        $strProps = $this->buildProps($props);

        return 'let vc = this.findVc(this); vc.loadComponent(\'' . $component->getInstanceId() . '\', \'' . $component->getId() . '\', \'' . ($hasParent === true ? ($component->getParentId() ?? $this->getInstanceId()) : "") . '\', \'' . $action . '\',  \'Async Component\', ' . $entity . ', ' . $entityList . ', ' . $strProps . ', true, ' . ($hydrate === true ? "true" : "false") . ');';
    }

    public function renderAppHtml() : string
    {
        return '
        <div class="formwrapper-outer">
            <section id="vue-app-body-' . $this->getInstanceName() . '" class="vue-app-body formwrapper-control">
                <div class="vue-modal-wrapper formwrapper-control">
                    ' . $this->buildComponentList() . '
                </div>
            </section>
        </div>';
    }

    public function renderAppMounted (): string
    {
        return '            
        ';
    }

    public function renderAppComputedValues (): string
    {
        return '';
    }

    public function renderAppMethods (): string
    {
        return '';
    }

    public function renderAppData (): string
    {
        return '';
    }

    public function renderAppFilters (): string
    {
        return '';
    }

    public function renderVueComponentsList (): string
    {
        return '';
    }

    public function renderAppJavascript() : string
    {
        return (new ExcellCarets())->processInternalCarets($this->renderDeclarations() .
            $this->modal->installJavascriptComponent() .
            $this->buildHelpers() .
            $this->buildMainAppJavascript());
    }

    protected function buildMainAppJavaScript() : string
    {
        global $app;
        return 'const vueApplication = new Vue({

        el: \'#' . $this->getAppId() . '\',

        components: [' . $this->renderVueComponentsList() .'],

        data() {
            return {
                userId: null,
                userNum: null,
                user: {},
                mainEntityList: [],
                authentication: null,
                modal: null,
                uriBase: \'' . $this->getUriBase() . '\',
                binding: \'' . $this->baseBinding . '\',
                activeComponentId: \'' . $this->getDefaultComponentInstanceId() . '\',
                abstractsMap: ' . $this->renderComponentsAbstractsToIdMap() . ',
                ' . $this->renderAppData() . '
            }
        },
        computed:
        {
            ' . $this->renderAppComputedValues() . '
        },

        filters: {
            ' . $this->renderAppFilters() . '
        },

        methods: {
            connectToWebSocket: function()
            {
                let self = this;
                
                try 
                {
                    self.socket = new WebSocket("wss://ws.ezdigital.com/cs?auth=" + Cookie.get("me"));
    
                    self.socket.onopen = function(e) {
                        self.socket.send("My name is John")
                    };
                    
                    self.socket.onmessage = function(event) {
                        console.log(`[message] Data received from server: ${event.data}`)
                    };
                    
                    self.socket.onclose = function(event) {
                        if (event.wasClean) {
                            console.log(`[close] Connection closed cleanly, code=${event.code} reason=${event.reason}`)
                        } else {
                            console.log("[close] Connection died")
                        }
                    };
                    
                    self.socket.onerror = function(error) {
                        //console.log(`[error] ${error.message}`)
                    };
                }
                catch(err)
                {
                    ezLog(err,"Socket Error")
                }
            },
            sendSocketMessage: function(text)
            {
                this.socket.send(text)
            },
            backToComponent: function(methodCall)
            {
                if (this.vc === null) { return; }
                this.vc.backToComponent(methodCall);
            },
            componentHasParent: function()
            {
                if (this.vc === null) { return false; }
                return this.vc.componentHasParent(activeComponentId);
            },
            renderBreadCrumb: function(componentInstance)
            {
                if (typeof componentInstance.buildBreadCrumb !== "function") { return; }                
                if (typeof this.$refs.'.$this->breadcrumb->getRef().' === "undefined") { console.log("Error: Breadcrumb Component has failed to load."); return; }        
                if (typeof this.$refs.'.$this->breadcrumb->getRef().'.updateBreadCrumb === "undefined") { console.log("Error: Breadcrumb Component is missing updateBreadCrumb method."); return; }                
                this.$refs.'.$this->breadcrumb->getRef().'.updateBreadCrumb(componentInstance.buildBreadCrumb());
            },
            addAjaxClass: function(className)
            {
                let bodyDialogBox = document.getElementsByClassName(className);
                
                if (typeof bodyDialogBox[bodyDialogBox.length - 1] !== "undefined") 
                {
                    bodyDialogBox[bodyDialogBox.length - 1].classList.add("ajax-loading-anim");
                }
            },
            removeAjaxClass: function(className)
            {
                let bodyDialogBox = document.getElementsByClassName(className);
                
                if (typeof bodyDialogBox[bodyDialogBox.length - 1] !== "undefined") 
                {
                    bodyDialogBox[bodyDialogBox.length - 1].classList.remove("ajax-loading-anim");
                }
            },
            uuidv4: function () 
            {
                return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
                    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
                );
            },
            findComponentByUriAbstract: function (uriAbstract) 
            {
                for (let currAbstractComponentId in this.abstractsMap)
                {
                    if (this.uriAbstractMapSuccess(this.abstractsMap[currAbstractComponentId].abstract, uriAbstract))
                    {
                        return this.abstractsMap[currAbstractComponentId].id;
                    }
                }

                return null;
            },
            uriAbstractMapSuccess: function(map, abstract)
            {
                const mapMatch = map.match(/\//g);
                const abstractMatch = abstract.match(/\//g);
                if (abstractMatch === null || mapMatch.length > abstractMatch.length)
                {
                    return false;
                }
                
                for (let currMapIndex in mapMatch)
                {
                    if (mapMatch[currMapIndex].substring(0,1) === "{" && mapMatch[currMapIndex].slice(-1) === "}")
                    {
                        continue;
                    }
                    
                    if ( mapMatch[currMapIndex] === abstractMatch[currMapIndex])
                    {
                        continue;
                    }
                    
                    return false;
                }
                
                return true;
                
            },
            loadPageByUri: function(uriPath) 
            {
                console.log("vueApplication: " + uriPath);
            },
            findModal: function(self) 
            {
                return this.modal;
            },
            renderFullName: function(user)
            {
                let fullName = [];
                
                if (typeof user.name_prefx !== "undefined") { fullName.push(user.name_prefx); }
                if (typeof user.first_name !== "undefined") { fullName.push(user.first_name); }
                if (typeof user.middle_name !== "undefined") { fullName.push(user.middle_name); }
                if (typeof user.last_name !== "undefined") { fullName.push(user.last_name); }
                if (typeof user.name_sufx !== "undefined") { fullName.push(user.name_sufx); }
                
                return fullName.join(" ");
            },
            accessProfile: function()
            {
                app.enableAvatarMenu();
            },
            openMobileMenu: function()
            {
                app.Logout()
            },
            openCart: function()
            {   
                if (appCart === null || typeof appCart.openCart === "undefined") { console.log("no cart assigned"); return; }
                appCart.openCart({className: false});
            },
            openNotifications: function()
            {   
                app.enableNotifications();
            },
            openSearch: function()
            {   
                app.enableSearch();
            },
            hydrateCart: function()
            {
                const self = this;
                
                if (typeof AppCart === "undefined")
                {
                    setTimeout(function() 
                    {
                        self.hydrateCart();
                    }, 100);
                    
                    return;
                }
                
                '. $this->activateDynamicComponentByIdInModal(CartWidget::getStaticId(),"", "add", "{}", "this.mainEntityList", null, "self", false, "function(cartVueWidget) {
                    appCart = new AppCart(cartVueWidget.instance);
                }").'
            },
            loadComponent: function(action, instanceId, id, parentInstanceId, title, entity, entities, props, show, hydrate)
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
            show: function(title)
            {
                if (title) { this.modal_title = title; }                  
                this.showModal = true;
            },
            close: function($event)
            {
                this.vc.runComponentDismissalScript();
                this.showModal = false;
            },
            backToComponent: function(methodCall)
            {
                this.vc.backToComponent(methodCall);
            },
            componentHasParent: function()
            {
                if (this.vc === null) { return false; }
                return this.vc.componentHasParent();
            },
            addAjaxClass: function()
            {
                let bodyDialogBox = document.getElementsByClassName("vue-app-body");
                bodyDialogBox[bodyDialogBox.length - 1].classList.add("ajax-loading-anim");
                this.$forceUpdate();
            },
            removeAjaxClass: function()
            {
                let bodyDialogBox = document.getElementsByClassName("vue-app-body");
                bodyDialogBox[bodyDialogBox.length - 1].classList.remove("ajax-loading-anim");
                this.$forceUpdate();
            },
            authenticate: function()
            {
                this.authentication = new ExcellAuthentication(this);
                this.authentication.validate();
            },
            logout: function()
            {
                this.authentication.clearAuth();
            },
            ' . $this->renderAppMethods() . '
        },
        mounted() 
        {
            const vueAppBody = document.getElementById("vue-app-body-' . $this->getInstanceName() . '")
            
            if (typeof this.$refs.'.$this->getModal()->getRef().'.$children !== "undefined")
            {
                this.modal = this.$refs.'.$this->getModal()->getRef().'.$children[0]
            }
            
            this.authenticate()
            this.hydrateCart()
            this.connectToWebSocket()
                    
            if (typeof(vueAppBody) != \'undefined\' && vueAppBody != null) 
            { 
                this.vc = new vueComponents(this, document.getElementById("vue-app-body-' . $this->getInstanceName() . '"), "app", this.uriBase)
            ' . $this->loadRegisteredComponents($this->baseBinding) . '
                this.vc.hideComponents()
                this.vc.setInitialComponentLoad()
                ' . $this->loadDefaultComponentScript() . '
                this.vc.registerModalByRef(this.$refs.'. $this->getModal()->getRef().')
            }
            
            ' . $this->renderAppMounted() . '
        }
    });';
    }

    protected function renderComponentsAbstractsToIdMap() : string
    {
        if (count($this->componentAbstracts) === 0) { return "{}"; }

        $strAbstractMap = "{";

        foreach ($this->componentAbstracts as $currComponentId => $currAbstractUri)
        {
            $strAbstractMap .= "comp_".str_replace("-","",$currComponentId) . ": { abstract: '" . $currAbstractUri . "', id: '".$currComponentId."'}," ;
        }

        return substr($strAbstractMap, 0, -1) . "}";
    }

    protected function loadDefaultComponentScript() : string
    {
        if ($this->getDefaultComponent() === null)
        {
            return '
                const currentHistory = appHistory.getCurrentHistory();
                 
                if ( \'/' . $this->getUriBase() . '\' !== currentHistory.path)
                {
                    let currentAbstract = currentHistory.path.replace(\'/' . $this->getUriBase() . '/\',\'\');
                    
                    if (currentAbstract.includes("?"))
                    {
                        currentAbstract = currentAbstract.split("?")[0];
                    }
                    
                    const componentId = this.findComponentByUriAbstract(currentAbstract);
                    
                    if (componentId !== null)
                    {
                        const self = this;
                        
                        self.vc.loadComponentByStaticId( "' . $this->getDefaultComponentId() .'", "", "' . $this->getDefaultComponentAction() .'", this.entity, this.mainEntityList, ' . $this->renderCustomPropsJavascriptObject() . ', false, true, function(mainComponent) {
                            self.vc.registerParentComponent(mainComponent).loadComponentByStaticId(componentId, mainComponent.instanceId, "default", self.entity, self.mainEntityList, ' . $this->renderCustomPropsJavascriptObject() . ', true, true, function(component) 
                            {
                                if (typeof component.instance.loadFromUriAbstract !== "function") 
                                {
                                    ezLog("The loadFromUriAbstract method is not installed on the hydrating component.", "Missing Method");
                                    return;
                                }
                                
                                const idFromUriAbstract = self.vc.getIdFromUriAbstract(component.uriAbstract, currentAbstract);
                                component.instance.loadFromUriAbstract(idFromUriAbstract);
                            }); 
                        });
                       
                        return;
                    }
                }                
                
                this.vc.loadComponentByStaticId( "' . $this->getDefaultComponentId() .'", "", "' . $this->getDefaultComponentAction() .'", this.entity, this.mainEntityList, ' . $this->renderCustomPropsJavascriptObject() . ',  true, true);           
            ';

        }

        return 'this.vc.loadComponent("'.$this->getDefaultComponent()->getInstanceId().'", "'.$this->getDefaultComponent()->getId().'", "", "'.$this->getDefaultComponent()->getDefaultAction().'", "", this.entity, this.mainEntityList, ' . $this->renderCustomPropsJavascriptObject() . ', true, true);';
    }

    protected function buildHelpers () : string
    {
        $helper = '';

        foreach ($this->components as $currComponent)
        {
            $helper .= $currComponent->installJavascriptComponent();
        }

        return $helper;
    }
}