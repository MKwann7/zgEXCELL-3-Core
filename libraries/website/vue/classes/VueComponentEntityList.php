<?php

namespace App\Website\Vue\Classes;

use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\Base\VueCustomMethods;

class VueComponentEntityList extends VueComponent
{
    protected $title = "My Component Entity List";
    protected $batchLoadEndpoint;
    protected $batchCount = 500;
    protected $entityPageDisplayCount = 15;
    protected $noEntitiesWarning = "There are no entities in this module.";

    protected function renderComponentDataAssignments() : string
    {
        return '
                    batchOffset: 0,
                    singleEntity: false,
                    userId: 0,
                    batchStart: false,
                    batchEnd: false,
                    orderKey: "' . $this->getEntity()->getDefaultSortColumn() . '",
                    sortByType: ' . $this->getEntitySortOrder() . ',
                    mainEntityColumns: ' . $this->getEntityDisplayColumns() .',
                    searchMainQuery: "",
                    mainEntityPageDisplayCount: ' . $this->entityPageDisplayCount . ',
                    mainEntityPageTotal: 1,
                    mainEntityPageIndex: 1,
                    mainEntityList: [],
                    batchLoadingUri: "' . $this->batchLoadEndpoint . '",
                    listLayoutType: "' . $this->listLayoutType() . '",
        ';
    }

    protected function listLayoutType() : string
    {
        return "list";
    }

    public function getEntitySortOrder(): string
    {
        return strtolower($this->getEntity()->getDefaultSortOrder()) === "asc" ? "true" : "false";
    }

    public function getEntityDisplayColumns(): string
    {
        $columns = "[";

        foreach ($this->getEntity()->getDisplayColumns() as $currColumnName)
        {
            $columns .= "'{$currColumnName}',";
        }

        return substr($columns, 0, -1) . "]";
    }

    protected function renderComponentMountedScript() : string
    {
        return '
            if (this.filterByEntityValue === true)
            {
               this.batchLoadMainEntitiesAwait();
            }
            else
            {
                this.batchLoadMainEntities();
            }
            ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentHydrationScript() . '
        ';
    }

    protected function renderReloadComponentMethod() : string
    {
        return '
            reloadComponent: function() {
                this.mainEntityList = [];
                this.batchOffset = 0;
                this.batchStart = true;
                this.batchEnd = false;
                this.batchLoadMainEntities();
            },
        ';
    }

    protected function renderPaginationMethods() : string
    {
        return '
            orderByColumn: function(column)
            {
                this.sortByType = ( this.orderKey == column ) ? ! this.sortByType : this.sortByType;
                this.orderKey = column;
            },
            prevMainEntityPage: function()
            {
                this.mainEntityPageIndex--;
                this.mainEntityList = this.mainEntityList;
            },
            nextMainEntityPage: function()
            {
                this.mainEntityPageIndex++;
                this.mainEntityList = this.mainEntityList;
            },
        ';
    }

    protected function renderBatchMethods() : string
    {
        return '
            processBatchLoop: function()
            {
            },
            processBatchCompletion: function()
            {
            },
        ';
    }

    public function setEntityPageDisplayCount(int $count) : self
    {
        $this->entityPageDisplayCount = $count;
        return $this;
    }

    protected function renderComponentMethods() : string
    {
        return VueCustomMethods::renderSortMethods() . '
            ' . $this->renderPaginationMethods() . '
            ' . $this->renderBatchMethods() . '
            addAjaxClass: function(className, retry)
            {
                this.getClassElementInComponent(className, function(containerElement) {                    
                    containerElement.classList.add("ajax-loading-anim");
                });
            },
            getClassElementInComponent(className, callback, retry)
            {
                const vc = this.findVc(this);
                
                if (vc === null)
                {
                        
                }
                
                const currentComponent = vc.getComponentByInstanceId("'.$this->getInstanceId().'");
                
                if ( currentComponent === null && (typeof retry === "undefined" || retry <= 3))
                {
                    const self = this;
                    setTimeout(function() { self.getClassElementInComponent(className, callback, parseFloat(retry) + 1); }, 100);
                    return;
                }
                
                let containerElement = currentComponent.instance.$el;
                
                if (typeof containerElement === "undefined")
                {
                    containerElement = document.getElementById("'.$this->getInstanceId().'");
                }
                
                if (typeof className !== "undefined")
                {
                    containerElement = containerElement.getElementsByClassName(className);
                    
                    if (typeof containerElement[containerElement.length - 1] === "undefined") 
                    {
                        return;
                    }
                    
                    containerElement = containerElement[containerElement.length - 1];
                }
                
                if (containerElement !== null)
                {
                    callback(containerElement);
                }
            },
            removeAjaxClass: function(className)
            {
                this.getClassElementInComponent(className, function(containerElement) {
                    containerElement.classList.remove("ajax-loading-anim");
                });
            },
            deleteMainEntity: function(card)
            {
                let self = this;
                modal.EngageFloatShield();
                let data = {title: "Delete Card?", html: "Are you sure you want to proceed?<br>Please confirm."};
    
                modal.EngagePopUpConfirmation(data, function() {
//                    modal.EngageFloatShield();
//                    const deletionUrl = "/module-widget/ezcard/member-directory/v1/delete-directory-record?member=" + member.member_directory_record_id;
//                    ajax.PostExternal(deletionUrl, "", true, function(result)
//                    {
//                        if (result.success !== true)
//                        {
//                            modal.CloseFloatShield(function() {
//                                modal.EngageFloatShield();
//                                let alertData = {title: "Drat. Something Went Wrong!", html: "We\'ve recorded it and our developers will look into it soon.<hr/><i>Please contact customer service to see if we can resolve your deletion request on our end.</i>"};
//                                modal.EngagePopUpAlert(alertData, function() {
//                                    modal.CloseFloatShield(function() { modal.CloseFloatShield(); });
//                                }, 500, 115, true);
//                            },500);
//                            return;
//                        }
//    
//                        self.directoryMembers = self.directoryMembers.filter(function (currEntity) {
//                            return member.member_directory_record_id != currEntity.member_directory_record_id;
//                        });
//    
//                        self.$forceUpdate();
//                        modal.CloseFloatShield(function() {
//                            modal.CloseFloatShield();
//                        },500);
//                    });
                }, 400, 115);
            },
            addEntityToList: function(entity) 
            {
                for (let currEntity of this.mainEntityList)
                {
                    if ( currEntity.sys_row_id == entity.sys_row_id)
                    {
                        return;
                    }
                }
                
                this.mainEntityList.push(entity);
            },
            ' . $this->renderReloadComponentMethod() . '
            toggleLayoutGrid: function() {
                this.listLayoutType = "grid";
            },
            toggleLayoutList: function() {
                this.listLayoutType = "list";
            },
            updatePage: function() {
                // update....
            },
            batchLoadMainEntitiesAwait: function()
            {
                if (typeof this.filterEntityId === "undefined")
                {
                    const self = this;
                    setTimeout(function() {
                        self.batchLoadMainEntitiesAwait();
                    },100);
                    
                    return;
                }
                
                this.batchLoadMainEntities();
            },
            batchLoadMainEntities: function()
            {
                const vc = this.findVc(this);
                
                console.log(vc);
                
                if ( this.batchLoadingUri === "") { this.removeAjaxClass(); return; }
                
                this.batchOffset++;
                
                if (this.batchStart === false) { this.addAjaxClass(); }

                let self = this;
                
                setTimeout(function()
                {
                    let strBatchUrl = self.batchLoadingUri + "?batch=' . $this->batchCount . '&offset=" + self.batchOffset + "&fields=' . implode(",", $this->getEntity()->getRenderColumns()) . '";
                        
                    if (typeof self.filterEntityId !== "undefined")
                    {
                        strBatchUrl += "&filterEntity=" + self.filterEntityId;
                    }
                    
                    ajax.Get(strBatchUrl, null, function(result)
                    {
                        if (result.success == false) { return; }
                        objCardResult = result.response;
                   
                        for(let currEntityIndex in objCardResult.data.list)
                        {
                            self.mainEntityList.push(objCardResult.data.list[currEntityIndex]);
                        }
                        
                        setTimeout(function() { self.batchStart = true; self.removeAjaxClass(); } , 250);
                        self.mainEntityPageTotal = self.mainEntityList / self.mainEntityPageDisplayCount;
                        
                        if (objCardResult.end == "false")
                        {
                            self.processBatchLoop();
                            self.batchLoadMainEntities();
                            return;
                        }
                        
                        self.processBatchCompletion();
                        
                        self.batchEnd = true;
                    });
                },50);
            },';
    }

    protected function renderComponentComputedValues() : string
    {
        return 'totalMainEntityPages: function()
                {
                    return this.mainEntityPageTotal;
                },
                ' . $this->renderOrderedMainEntityListComputedMethod();
    }

    protected function renderOrderedMainEntityListComputedMethod() : string
    {
        return '
                orderedMainEntityList: function()
                {
                    var self = this;

                    let objSortedPeople = this.sortedEntity(this.searchMainQuery, this.mainEntityList, this.orderKey, this.sortByType, this.mainEntityPageIndex,  this.mainEntityPageDisplayCount, this.mainEntityPageTotal, function(data) {
                        self.mainEntityPageTotal = data.pageTotal;
                        self.mainEntityPageIndex = data.pageIndex;
                    }'.$this->renderEntityFitlers().');
                    
                    '.$this->additionalFilterQueries().'

                    return objSortedPeople;
                },
        ';
    }

    protected function additionalFilterQueries() : string
    {
        return "";
    }

    protected function renderTemplate() : string
    {
        return '<div class="formwrapper-control">
            [LIST PARENT CLASS]
        </div>';
    }

    protected function renderEntityFitlers() : string
    {
        $filterColumns = $this->entity->getFilterColumns();

        if (empty($filterColumns) || count($filterColumns) === 0) { return  ""; }

        $filterColumnforJavaScript = [];

        foreach($filterColumns as $currFilterColumn)
        {
            $filterColumnforJavaScript[] = '"$currFilterColumn"';
        }

        return ',[' . implode(",", $filterColumnforJavaScript) . '], "entity-search-filter"';
    }

    protected function buildMainEntityDisplayFieldsForTable() : string
    {
        $columnList = "";
        foreach( $this->entity->getDisplayColumns() as $currColumn)
        {
            $columnList .= "<td>{{ mainEntity.{$currColumn} }}</td>";
        }
        return $columnList;
    }
}