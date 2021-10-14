<?php

namespace App\Website\Vue\Classes\Base;

use App\Core\AppModel;
use App\Utilities\Excell\ExcellCollection;
use App\Website\Constructs\Breadcrumb;
use App\Website\Vue\Classes\VueModal;
use App\Website\Vue\Classes\VueProps;

abstract class VueComponent extends VueBase
{
    protected $name;
    protected $id;
    protected $title = "My Component";
    protected $entity;
    protected $modal      = null;
    protected $vueType     = "comp";
    protected $breadcrumbs = null;
    protected $modalTitleForAddEntity;
    protected $modalTitleForEditEntity;
    protected $modalTitleForDeleteEntity;
    protected $modalTitleForRowEntity;
    protected $mixIns = [];
    protected $directives = [];
    protected $rendered = false;
    protected $noMount = false;
    protected $noHydrate = false;
    protected $modalWidth = "auto";
    protected $userAdminRole = false;
    protected $userEzDigtalRole = false;
    protected $userGodMode = false;
    protected $parentData = [];

    public $isNotDynamic = false;

    public function __construct(?AppModel $entity = null)
    {
        $this->entity = $entity;
        $this->instanceId = getGuid();
        $this->instanceName = preg_replace("/[^A-Za-z0-9]/", '', $this->instanceId);

        if (userCan("manage-system"))
        {
            $this->userAdminRole = true;
        }

        if (userCan("manage-platform"))
        {
            $this->userEzDigtalRole = true;
        }

        if (userCan("god-mode"))
        {
            $this->userGodMode = true;
        }

        if(!isset($entity)) { return; }

        $this->modalTitleForAddEntity = "Add " . $entity->getModelName();
        $this->modalTitleForEditEntity = "Edit " . $entity->getModelName();
        $this->modalTitleForDeleteEntity = "Delete " . $entity->getModelName();
        $this->modalTitleForRowEntity = "View " . $entity->getModelName();

        $this->loadBreadCrumbs();
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

    protected function renderReloadComponentMethod() : string
    {
        return "";
    }

    protected function renderParentData() : void
    {
        global $app;
        $this->parentData["loggedInUser"] = "{user_id: '".$app->getActiveLoggedInUser()->user_id."', first_name: '".$app->getActiveLoggedInUser()->first_name."', last_name: '".$app->getActiveLoggedInUser()->last_name."'}";
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

    public function setNoMount($logical) : void
    {
        $this->noMount = $logical;
    }

    public function setComponentsToNoMount($logical) : void
    {
        foreach ($this->components as $currInstanceId => $currComponent)
        {
            $this->components[$currInstanceId]->noMount = $logical;
        }
    }

    public function setNoHydrate($logical) : void
    {
        $this->noHydrate = $logical;
    }

    public function setComponentsToNoHydrate($logical) : void
    {
        foreach ($this->components as $currInstanceId => $currComponent)
        {
            $this->components[$currInstanceId]->noHydrate = $logical;
        }
    }

    public function isNotMounted() : bool
    {
        return $this->noMount;
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

        if ($this->noMount === false)
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

    protected function buildHelpers() : string
    {
        $strHelperList = "";

        /** @var VueComponent $currComponent */
        foreach($this->components as $currComponent)
        {
            $strHelperList .= $currComponent->buildTemplate() . PHP_EOL;
        }

        return $strHelperList;
    }

    protected function buildHelperObjects()
    {
        $strHelperList = "";

        /** @var VueComponent $currComponent */
        foreach($this->components as $currComponent)
        {
            $strHelperList .= $currComponent->buildComponentObject($currComponent->getInstanceName(), $currComponent->getInstanceId(), $currComponent->getId(), $currComponent->getParentId()) . ",";
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
        $result = 'return {
            main: ' . $this->buildComponentObject($this->getInstanceName(), $this->getInstanceId(), $this->getId(), "") . ',
            helpers: [
                ' . $this->buildHelperObjects() . '
            ]
        }';

        return $result;
    }

    public function buildComponentObject($name, $instanceId, $id, $parent = "") : string
    {
        return '{
            name: \'' . $name . '\',
            id: \'' . $id . '\',
            instanceId: \'' . $instanceId . '\',
            parent: \'' . $parent . '\',
            uriPath: \'' . $this->uriPath . '\',
            uriAbstract: "' . ($this->endpointUriAbstract ?? "null") . '",
            modalWidth: "' . $this->modalWidth . '",
            noMount: "' . ($this->noMount === true ? "true" : "false"). '",
            noHydrate: "' . ($this->noHydrate === true ? "true" : "false"). '",
            template: `
                ' . $this->renderTemplateFromCache() . '
            `,
            mounted() {
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
                    userId: null,        
                    userAdminRole: '. ($this->userAdminRole === true ? "true" : "false").',
                    userEzDigitalRole: '. ($this->userEzDigtalRole === true ? "true" : "false").',
                    userGodMode: '. ($this->userGodMode === true ? "true" : "false").',
                    entity: null,
                    entities: [],
                    action: "view",
                    dynamicComponentsHydrated: false,
                    uriPath: \'\',
                    component_title: "'.$this->title.'",
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
                    '. $this->renderComponentHydrationScript() . '
                },
                loadProps: function(props)
                {
                    for(let currPropLabel in props)
                    {
                        this[currPropLabel] = props[currPropLabel];
                    }
                },
                instantiateDynamicComponents: function()
                {
                    if (this.dynamicComponentsHydrated === true) { return; }
'. $this->renderDynamicComponentHydrationScript() . '
                    this.dynamicComponentsHydrated = true;
                },
                getModalTitle: function(action)
                {
                    switch(action) {
                        case "add": return \'' . $this->modalTitleForAddEntity . '\';
                        case "edit": return \'' . $this->modalTitleForEditEntity . '\';
                        case "delete": return \'' . $this->modalTitleForDeleteEntity . '\';
                        case "read": return \'' . $this->modalTitleForRowEntity . '\';
                    }
                },
                buildBreadCrumb: function()
                {
                    return ['.$this->renderBreadcrumbMethod().'{linkLabel: this.component_title_original, linkHref: "", linkType: "title"}];
                },
                getParentLinkActions: function() 
                {
                    return ['.$this->renderParentLinkActions().']
                },
                dismissComponent: function()
                { 
                    ' . $this->renderComponentDismissalScript() . '
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
                inheritAuth: function()
                {
                    if (this.$parent.isLoggedIn === "active")
                    {
                        this.isLoggedIn = this.$parent.isLoggedIn;
                        this.authUserId = this.$parent.authUserId;
  
                        
                        if (typeof this.vc !== "undefined") 
                        {
                            this.vc.updateComponentAuths(this.isLoggedIn, this.authUserId);
                        }
                    }
                },
                ' . $this->renderComponentMethods() . '
            }
        }';
    }
}