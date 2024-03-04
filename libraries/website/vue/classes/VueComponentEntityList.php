<?php

namespace App\Website\Vue\Classes;

use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\Base\VueCustomMethods;

class VueComponentEntityList extends VueComponent
{
    protected string $title = "My Component Entity List";
    protected string $batchLoadEndpoint;
    protected int $batchCount = 500;
    protected int $entityPageDisplayCount = 15;
    protected string $noEntitiesWarning = "There are no entities in this module.";

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
            batchLoadingUri: "' . ($this->batchLoadEndpoint ?? "") . '",
            listLayoutType: "' . $this->listLayoutType() . '",
        ';
    }

    protected function listLayoutType() : string
    {
        return "list";
    }

    protected function getEntityManager() : ?VueComponent
    {
        return null;
    }

    protected function getManageEntityStaticId() : string
    {
        return "";
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
                
                if ( this.batchLoadingUri === "") { this.removeAjaxClass(); return; }
                
                this.batchOffset++;
                
                if (this.batchStart === false) { this.addAjaxClass(); }

                let self = this;
                
                setTimeout(function()
                {
                    let strBatchUrl = self.batchLoadingUri + "?batch=' . $this->batchCount . '&offset=" + self.batchOffset + "&fields=' . implode(",", $this->getEntity()->getRenderColumns()) . '";
                        
                    if (typeof self.filterEntityId !== "undefined") {
                        strBatchUrl += "&filterEntity=" + self.filterEntityId;
                    }
   
                    ajax.Get(strBatchUrl, null, function(result) {
                        if (result.success == false) { return; }
                        objCardResult = result.response;
                   
                        for(let currEntityIndex in objCardResult.data.list) {
                            let data = objCardResult.data.list[currEntityIndex];
                            let key1 = Object.keys(data)[0];
                            //self.mainEntityList[Number(data[key1])] = objCardResult.data.list[currEntityIndex];
                            self.mainEntityList.push(objCardResult.data.list[currEntityIndex]);
                            //self.mainEntityList.splice(Number(data[key1]), 0, objCardResult.data.list[currEntityIndex]);
                        }
                        
                        setTimeout(function() { self.batchStart = true; self.removeAjaxClass(); } , 250);
                        self.mainEntityPageTotal = self.mainEntityList / self.mainEntityPageDisplayCount;
                        
                        if (objCardResult.end == "false") {
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