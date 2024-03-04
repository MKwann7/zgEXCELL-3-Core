<?php

namespace App\Website\Vue\Classes\Base;

use App\Core\App;
use App\Core\AppModel;
use App\Utilities\Excell\ExcellCollection;
use App\Website\Constructs\Breadcrumb;
use App\Website\Constructs\SubPageLinks;
use App\Website\Vue\Classes\VueModal;

abstract class VueComponent extends VueBase
{
    protected string $name;
    protected string $id;
    protected string $title = "My Component";
    protected ?AppModel $entity;
    protected ?VueModal $modal      = null;
    protected string $vueType     = "comp";
    protected ?ExcellCollection $breadcrumbs = null;
    protected ?ExcellCollection $subpagelinks = null;
    protected string $modalTitleForAddEntity = "";
    protected string $modalTitleForEditEntity = "";
    protected string $modalTitleForDeleteEntity = "";
    protected string $modalTitleForRowEntity = "";
    protected array $mixIns = [];
    protected array $directives = [];
    protected bool $rendered = false;
    protected string $mountType = "default";
    protected bool $noHydrate = false;
    protected string $modalWidth = "auto";
    protected array $parentData = [];
    protected string $applicationType;

    public bool $isNotDynamic = false;

    public function __construct(?AppModel $entity = null)
    {
        $this->entity = $entity;
        $this->instanceId = getGuid();
        $this->instanceName = preg_replace("/[^A-Za-z0-9]/", '', $this->instanceId);

        if (!isset($entity)) { return; }

        $this->modalTitleForAddEntity = "Add " . $entity->getModelName();
        $this->modalTitleForEditEntity = "Edit " . $entity->getModelName();
        $this->modalTitleForDeleteEntity = "Delete " . $entity->getModelName();
        $this->modalTitleForRowEntity = "View " . $entity->getModelName();

        $this->loadBreadCrumbs();

        /** @var App $app */
        global $app;
        $this->applicationType = $app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label", "application_type")->value ?? "default";
    }

    protected function renderComponentHydrationScript() : string
    {
        return "if (typeof this.disableModalLoadingSpinner === 'function') { this.disableModalLoadingSpinner(); }";
    }

    protected function setMixin(string $mixin) : self
    {
        $this->mixIns[] = $mixin;
        return $this;
    }

    protected function setDirective(array $directive) : self
    {
        foreach($directive as $currDirectiveLabel => $currDirectiveFlag)
        {
            $this->directives[$currDirectiveLabel] = $currDirectiveFlag;
        }
        return $this;
    }

    protected function renderMixins() : string
    {
        if (count($this->mixIns) === 0) { return ""; }

        return "mixins: [".implode(",", $this->mixIns)."]," . PHP_EOL;
    }

    protected function renderDirectives() : string
    {
        if (count($this->directives) === 0) { return ""; }

        $directiveObjectArray = [];

        foreach($this->directives as $currDirectiveLabel => $currDirectiveFlag)
        {
            $directiveObjectArray[] = "$currDirectiveLabel: $currDirectiveFlag,";
        }

        return "directives: {".implode(",", $directiveObjectArray)."}," . PHP_EOL;
    }

    protected function loadTemplateFromAjaxRequest() : string
    {
        return "";
    }

    protected function renderComponentDataAssignments() : string
    {
        return "";
    }

    protected function renderComponentMethods() : string
    {
        return "";
    }

    protected function renderComponentComputedValues() : string
    {
        return "";
    }

    protected function renderComponentCreatedScript() : string
    {
        return "";
    }

    protected function renderComponentMountedScript() : string
    {
        return "";
    }

    protected function renderComponentDismissalScript() : string
    {
        return "";
    }

    protected function renderDispatchEvents() : string
    {
        return '
            dispatch.register("user_auth", this, "setUserAuth")
        ';
    }

    protected function renderReloadComponentMethod() : string
    {
        return "";
    }

    protected function renderParentData() : void
    {
        global $app;
        $this->parentData["loggedInUser"] = "{user_id: '".$app->getActiveLoggedInUser()?->user_id."', first_name: '".$app->getActiveLoggedInUser()?->first_name."', last_name: '".$app->getActiveLoggedInUser()?->last_name."'}";
    }

    private function renderParentDataString(): string
    {
        $this->renderParentData();

        $componentParentData = "{";
        foreach ($this->parentData as $currKey => $currDataString)
        {
            $componentParentData .= $currKey . ":" . $currDataString .",";
        }

        return substr($componentParentData,0,-1) . "}";
    }

    protected function loadBreadCrumbs() : self
    {
        return $this;
    }

    public function setRendered($logical) : void
    {
        $this->rendered = $logical;
    }

    public function isRendered() : bool
    {
        return $this->rendered;
    }

    public function setMountType($type) : self
    {
        $this->mountType = $type;
        return $this;
    }

    public function getMountType() : string
    {
        return $this->mountType;
    }

    public function setComponentsMountType($type) : void
    {
        if (!empty($this->components)) {
            foreach ($this->components as $currInstanceId => $currComponent)
            {
                $this->components[$currInstanceId]->mountType = $type;
            }
        }
    }

    public function setNoHydrate($logical) : void
    {
        $this->noHydrate = $logical;
    }

    public function setComponentsToNoHydrate($logical) : void
    {
        if (!empty($this->components)) {
            foreach ($this->components as $currInstanceId => $currComponent)
            {
                $this->components[$currInstanceId]->noHydrate = $logical;
            }
        }
    }

    public function setModal($modal) : self
    {
        $this->modal = $modal;
        return $this;
    }

    public function getModal() : VueModal
    {
        return $this->modal;
    }

    public function getComponentEntityModel()
    {
        return $this->entity;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function getDefaultEntityRenderColumns() : array
    {
        return $this->entity->getRenderColumns();
    }

    public function getDefaultEntitySortOrder() : string
    {
        return $this->entity->getDefaultSortOrder();
    }

    public static function getStaticUriAbstract() : string
    {
        return (new static)->endpointUriAbstract;
    }

    public function addParentId($parentId) : self
    {
        $this->parentId = $parentId;
        return $this;
    }

    public function renderComponentsDeclarationList() : string
    {
        $componentList = "";

        /** @var VueComponent $curComponent */
        foreach ($this->components as $curComponent)
        {
            $componentList .= $curComponent->getInstanceName() . ",";
        }

        return substr($componentList, 0, -1);
    }

    public function getComponentObject()
    {
        $instanceTag = "";

        if ($this->mountType === "no_mount")
        {
            $instanceTag = ', instance: this.$refs.'.$this->getRef();
        }

        return '{instanceId: "' . $this->getInstanceId() . '", id: "' . $this->getId() . '", parentInstanceId: "' . $this->getParentId() . '", uriAbstract: "' . ($this->endpointUriAbstract ?? "null") . '",  modalWidth: "' . $this->modalWidth . '", ref: "' . $this->getRef() . '", render: "' . $this->isRendered() . '"'.$instanceTag.'}';
    }

    protected function renderParentLinkActions() : ?string
    {
        $linkActions = [];

        foreach ($this->parentLinkActions as $currLinkAction)
        {
            $linkActions[] = "'{$currLinkAction}'";
        }

        return implode(",", $linkActions);
    }

    public function addBreadcrumb(Breadcrumb $breadcrumb) : self
    {
        if ($this->breadcrumbs === null) { $this->breadcrumbs = new ExcellCollection(); }
        $this->breadcrumbs->Add($breadcrumb);
        return $this;
    }

    public function addSubPageLink(SubPageLinks $subpagelink) : self
    {
        if ($this->subpagelinks === null) { $this->subpagelinks = new ExcellCollection(); }
        $this->subpagelinks->Add($subpagelink);
        return $this;
    }

    protected function renderBreadcrumbMethod() : string
    {
        if ($this->breadcrumbs === null) { return ""; }

        $breadcrumb = "";

        foreach ($this->breadcrumbs as $currBreadCrumb)
        {
            $breadcrumb .= '{';
            $breadcrumb .= 'linkLabel: "' . $currBreadCrumb->getLabel() . '", linkHref: "' . $currBreadCrumb->getLink() . '", linkType: "' . $currBreadCrumb->getType() . '"';
            $breadcrumb .= '},';
        }

        return $breadcrumb;
    }

    protected function renderSubPageLinksMethod() : string
    {
        if ($this->subpagelinks === null) { return ""; }

        $subPageLinks = "";

        foreach ($this->subpagelinks as $currSubPageLinks)
        {
            $subPageLinks .= '{';
            $subPageLinks .= 'linkLabel: "' . $currSubPageLinks->getLabel() . '", linkHref: "' . $currSubPageLinks->getLink() . '", active: "' . $currSubPageLinks->getActive() . '"';
            $subPageLinks .= '},';
        }

        return $subPageLinks;
    }

    protected function buildHelpers() : string
    {
        $strHelperList = "";

        /** @var VueComponent $currComponent */
        if (!empty($this->components)) {
            foreach($this->components as $currComponent)
            {
                $strHelperList .= $currComponent->buildTemplate() . PHP_EOL;
            }
        }

        return $strHelperList;
    }

    protected function buildHelperObjects()
    {
        $strHelperList = "";

        /** @var VueComponent $currComponent */
        if (!empty($this->components )) {
            foreach($this->components as $currComponent) {
                $strHelperList .= $currComponent->buildComponentObject($currComponent->getInstanceName(), $currComponent->getInstanceId(), $currComponent->getId(), $currComponent->getParentId()) . ",";
            }
        }

        return $strHelperList;
    }

    public function buildTemplate() : string
    {
        return 'const ' . $this->getInstanceName() . ' = ' . $this->buildComponentObject($this->getInstanceName(), $this->getInstanceId(), $this->getId());
    }

    public function renderComponentForAjaxDelivery($props = null) : string
    {
        $this->customProps = $props;
        return 'return {
            main: ' . $this->buildComponentObject($this->getInstanceName(), $this->getInstanceId(), $this->getId(), "") . ',
            helpers: [
                ' . $this->buildHelperObjects() . '
            ]
        }';
    }

    public function buildComponentObject($name, $instanceId, $id, $parent = "") : string
    {
        return '{
            name: \'' . $name . '\',
            id: \'' . $id . '\',
            instanceId: \'' . $instanceId . '\',
            parent: \'' . $parent . '\',
            uriPath: \'' . ($this->uriPath ?? "") . '\',
            uriAbstract: "' . ($this->endpointUriAbstract ?? "null") . '",
            modalWidth: "' . $this->modalWidth . '",
            mountType: "' . $this->mountType . '",
            noHydrate: "' . ($this->noHydrate === true ? "true" : "false"). '",
            template: `
                ' . $this->renderTemplateFromCache() . '
            `,
            mounted() {
                this.setDispatchEvents();
                this.hydrateAuth(this.$parent);
                if (typeof this.isDynamicComponent === "function" && this.isDynamicComponent() === true) {
                    this.hydrateComponent(this.$parent._props, true);
                }
                
                ' . $this->renderComponentMountedScript() . '       
            },
            props: { ' .
                (implode(", ", $this->buildPropsJavaScriptObject()))
        . ' },
            dynamicComponents() {
                return {
                    ' . $this->renderDynamicComponentAssignments() . '
                }
            },
            data: function() {
                return {
                    instanceId: \'\',
                    parentId: \'\',
                    parentData: ' . $this->renderParentDataString() . ',
                    hasParent: false,
                    isLoggedIn: "inactive",
                    authUserId: null,
                    user: {},        
                    userId: null,        
                    userAdminRole: false,
                    userSuperAdminRole: false,
                    userGodMode: false,
                    entity: null,
                    entities: [],
                    action: "view",
                    dynamicComponentForReHydration: {},
                    uriPath: \'\',
                    component_class: "'. static::class .'",
                    component_title: "' . $this->title . '",
                    component_title_original: "'.$this->title.'",
' . $this->renderDynamicPropsFromAjaxRequest() . '
' . $this->renderDynamicComponentDataAssignmentDataField() . '
                    ' . $this->renderComponentDataAssignments() . '
                };
            },
            computed: {
                ' . $this->renderComponentComputedValues() . '
            },
            ' . $this->renderMixins() . '
            ' . $this->renderDirectives() . '
            filters: {
                ucWords: function(str)
                {
                    return str.replace(/_/g," ").replace(/\w\S*/g, function (txt) {
                        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                    });
                },
            },
            created() {
                ' . $this->renderComponentCreatedScript() . '
            },
            methods: {
                hydrateComponent: function(props, show, callback)
                {
                    this.loadProps(props);
                    this.instantiateDynamicComponents();
                    if (this.authenticateUser()) {
                        this.checkAuthRoles()   
                    }
                    '. $this->renderComponentHydrationScript() . '
                },
                loadProps: function(props)
                {
                    for(let currPropLabel in props)
                    {
                        this[currPropLabel] = props[currPropLabel];
                    }
                },
                getClass: function() {
                    return this.component_class;
                },
                instantiateDynamicComponents: function(increment)
                {
                    const self = this
                    let retry = false
                    
                    '. $this->renderDynamicComponentHydrationScript() . '
                    
                    if (retry === true && (typeof increment === "undefined" || increment < 50)) {
                        if (typeof increment === "undefined") {
                            increment = 1
                        } else {
                            increment = increment + 1
                        }
                        
                        setTimeout(function() {
                            self.instantiateDynamicComponents(increment)
                        }, 20)
                    }
                },
                getModalTitle: function(action)
                {
                    switch(action) {
                        case "add": return \'' . ($this->modalTitleForAddEntity ?? get_class($this)) . '\';
                        case "edit": return \'' . ($this->modalTitleForEditEntity ?? get_class($this)) . '\';
                        case "delete": return \'' . ($this->modalTitleForDeleteEntity ?? get_class($this)) . '\';
                        case "read": return \'' . ($this->modalTitleForRowEntity ?? get_class($this)) . '\';
                    }
                },
                buildBreadCrumb: function()
                {
                    return ['.$this->renderBreadcrumbMethod().'{linkLabel: this.component_title_original, linkHref: "", linkType: "title"}];
                },
                buildSubPageLinks: function()
                {
                    return ['.$this->renderSubPageLinksMethod().'];
                },
                getParentLinkActions: function() 
                {
                    return ['.$this->renderParentLinkActions().']
                },
                dismissComponent: function()
                { 
                    ' . $this->renderComponentDismissalScript() . '
                },
                setDispatchEvents: function()
                { 
                    ' . $this->renderDispatchEvents() . '
                },
                uuidv4: function () {
                    return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
                        (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
                    );
                },
                formatDateForDisplay: function(dateString, nullDate) {
                    if (!nullDate) nullDate = "Bad Date";
                    if (typeof dateString === "undefined" || !dateString) return nullDate;
                    return new Date(dateString).format("ddd, MMM d yyyy");
                },
                updateAllChildrenAuth: function(isLoggedIn, authUserId) {
                    this.updateVcComponentsAuth(this.findVc(this), isLoggedIn, authUserId)
                    this.updateVcComponentsAuth(this.findChildVc(this), isLoggedIn, authUserId)
                },
                updateVcComponentsAuth: function(vc, isLoggedIn, authUserId)
                {
                    if (typeof vc === "undefined" || vc === null) return;
                    vc.updateComponentAuths(isLoggedIn, authUserId)
                },
                authenticateUser: function()
                {
                    const event = dispatch.get("user_auth")
                    return this.setUserAuth(event)
                },
                setUserAuth: function(data)
                {
                    if (data === null || typeof data.isLoggedIn === "undefined" || typeof data.user === "undefined" || data.isLoggedIn === "inactive" || data.user === "visitor") { 
                        this.isLoggedIn = "inactive";
                        this.authUserId = null;
                        this.userId = null;
                        this.userId = null;
                        this.userNum = null;
                        this.user = null;
                        return false; 
                    }
                    
                    this.isLoggedIn = data.isLoggedIn
                    this.authUserId = data.authUserId
                    this.userId = data.userId
                    this.userNum = data.userNum
                    
                    try {
                        const userData = JSON.parse(data.user)
                        this.user = {}
                        this.user.data = userData
                        this.user.id = data.userNum
                        this.user.uuid = data.userId
                        this.user.login = this.isLoggedIn
                        return true
                    } catch(e) {
                        console.log(data);
                        console.log("something went wrong");
                        return false
                    }
                    this.hydrateAuth(this.$parent);
                },
                hydrateAuth: function(parent)
                {
                    if (typeof parent.authentication === "undefined" || parent.authentication === null) {
                        if (typeof parent.$parent === "undefined") {
                            return;
                        }
                        return this.hydrateAuth(parent.$parent);
                    }
                    
                    parent.authentication.authenticate();
                },
                checkAuthRoles: function()
                {
                    if (typeof this.user === "string") this.user = JSON.parse(this.user); 
                    if (this.user !== null && this.user.data.Roles.length >= 1)
                    {
                        let self = this;
                        
                        this.user.data.Roles.forEach(el => {
                            switch(parseInt(el.user_class_type_id__value)) {
                                case 0: self.userSuperAdminRole = true; self.userAdminRole = true; self.userGodMode = true; self.readOnly = false; return;
                                case 1: 
                                case 2: self.userSuperAdminRole = true; self.userAdminRole = true; self.readOnly = false; return;
                                case 3: self.userSuperAdminRole = true; self.userAdminRole = true; self.readOnly = true; return;
                                case 4: 
                                case 5: self.userAdminRole = true; self.readOnly = false; return;
                                case 6: self.userAdminRole = true; self.readOnly = true; return;
                            }
                        });                  
                    } else {
                        this.userSuperAdminRole = false;
                        this.userAdminRole = false;
                        this.readOnly = false;
                    }
                },
                hydrateDynamicComponents: function(entity, type, increment) {
                    const self = this
                    let retry = false
                    if (!increment) { 
                        increment = 1
                    } else {
                        increment++
                    }
                    for (let dynCompRef in this.dynamicComponentForReHydration) {
                        if (typeof this.$refs[dynCompRef] !== "undefined") {
                            this.$refs[dynCompRef][type] = entity
                        } else {
                            retry = true
                        }
                    }
                    if (retry === true && increment < 20) {
                        setTimeout(function() {
                            ezLog(increment)
                            self.hydrateDynamicComponents(entity,type, increment)
                        }, 50)
                    }
                },
                ' . $this->renderComponentMethods() . '
            }
        }';
    }
}