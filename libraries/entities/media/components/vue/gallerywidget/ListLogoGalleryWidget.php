<?php

namespace Entities\Media\Components\Vue\GalleryWidget;

use App\Core\App;
use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\VueComponentEntityList;
use App\Website\Vue\Classes\VueProps;
use Entities\Companies\Classes\Companies;
use Entities\Media\Models\ImageModel;

class ListLogoGalleryWidget extends VueComponentEntityList
{
    protected string $id = "544cbd0e-47e6-4c12-aadc-f34f2ec39343";
    protected string $modalWidth = "850";
    protected string $title = "Logo List";
    protected string $batchLoadEndpoint = "api/v1/media/get-logo-batches";
    protected string $noEntitiesWarning = "There are no logos to display.";

    public function __construct($defaultEntity = null, array $components = [])
    {
        if ($defaultEntity === null) {
            $defaultEntity = (new ImageModel())
                ->setDefaultSortColumn("image_id", "DESC")
                ->setDisplayColumns(["thumb","width","height"])
                ->setFilterColumns(["image_id","width","height"])
                ->setRenderColumns(["image_id", "title", "width", "height", "image_class","thumb", "url","created_on", "last_updated", "sys_row_id",]);
        }

        parent::__construct($defaultEntity, $components);

        $filterEntity = new VueProps("filterEntityId", "object", "filterEntityId");
        $filterByEntityValue = new VueProps("filterByEntityValue", "boolean", "filterByEntityValue");
        $filterByEntityRefresh = new VueProps("filterByEntityRefresh", "boolean", true);

        $this->addProp($filterEntity);
        $this->addProp($filterByEntityValue);
        $this->addProp($filterByEntityRefresh);

        $editorComponent = $this->getEntityManager();
        $editorComponent->addParentId($this->getInstanceId(), ["edit"]);

        $this->addComponentsList($editorComponent->getDynamicComponentsForParent());
        $this->addComponent($editorComponent);

        $this->modalTitleForAddEntity = "View Logos";
        $this->modalTitleForEditEntity = "View Logos";
        $this->modalTitleForDeleteEntity = "View Logos";
        $this->modalTitleForRowEntity = "View Logos";
        $this->setDefaultAction("view");
    }

    protected function getEntityManager() : ?VueComponent
    {
        return new ManageImageWidget();
    }

    protected function getManageEntityStaticId() : string
    {
        return ManageImageWidget::getStaticId();
    }

    protected function renderParentData(): void
    {
        parent::renderParentData();
        $this->parentData["singleEntity"] = "false";
    }

    protected function renderComponentMethods() : string
    {
        return parent::renderComponentMethods() . '
            goToImageDashboard: function(entity)
            {
                const self = this
                if (typeof this.filterEntity === "undefined")
                {
                    modal.EngageFloatShield();
                    const myVc = self.findVc(self)
                    const listLogoComponent = myVc.getComponentByInstanceId(self.instanceId)
                    myVc.setNewParentIdForComponentById("'.$this->getManageEntityStaticId().'", listLogoComponent.id)
                    '. $this->activateRegisteredComponentById($this->getManageEntityStaticId(), "edit", true, "entity", "this.mainEntityList", ["singleEntity" => "this.singleEntity", "siteEntity" => "this.entity", "label" => "this.label", "manageType" => "'logo'"], "this", "function(component) { 
                        ezLog(self,'listLogoInstance')
                        ezLog(component,'component1')
                        modal.CloseFloatShield();
                    }").'
                }
                else
                {
                    '.$this->activateRegisteredComponentByIdInModal($this->getManageEntityStaticId(), "edit", true, "entity", "this.mainEntityList", ["singleEntity" => "this.singleEntity", "filterEntityId" => "this.filterEntityId", "siteEntity" => "this.entity", "label" => "this.label", "manageType" => "'logo'"], "this", "function(component) {
                        ezLog(component,'component2')
                    }").'
                }    
            },
            openUploadDialog: function() {            
                document.getElementById(this.uploadNewImageId).click()
            },
            updateImageEntityList: function(data) {            
                this.updateMainEntityList(data);
            },
            updateMainEntityList: function (data) {
                let assignedCard = false;
                
                for (let currEntityIndex in Array.from(this.mainEntityList)) {
                    if (this.mainEntityList[currEntityIndex].card_id === data.card.card_id) {
                        assignedCard = true
                        this.mainEntityList[currEntityIndex] = null;
                        this.mainEntityList[currEntityIndex] = data.card;
                        break;
                    }
                }
                
                if (!assignedCard) {
                    this.entities.push(data.card);
                }
            },
            imageError: function (entity) {
                entity.banner = "/_ez/images/no-image.jpg";
            },
            submitNewImageUpload: function(event) {
                const self = this;
                const data = new FormData()
                const files = event.target.files
                const type = this.getCardType(this.entity)
                const url = "api/v1/media/batch-image-upload?uuid=" + this.userId + "&entity_name=" + type + "&entity_id=" + this.entity.card_id + "&user_id=" + this.entity.card_owner_id + "&class=logos";
                
                for(const fileIndex in files) {
                    const fileName = "file_" + fileIndex;
                    data.append(fileName, files[fileIndex])
                    data.append("files", fileName);
                }
                
                ajax.File(url, data, function(result) {
                    self.reloadImageList()
                })
            },
            getCardType: function(entity) {
                if (this.entity.card_type_label.toLowerCase() === "default") {
                    return "card"
                }
                return this.entity.card_type_label.toLowerCase()
            },
            reloadImageList: function() {
                this.reloadComponent()
            },
        ';
    }

    protected function listLayoutType() : string
    {
        return "grid";
    }

    protected function renderComponentDataAssignments() : string
    {
        return parent::renderComponentDataAssignments(). '
                uniqueId: 0,
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentHydrationScript() . '
            this.uniqueId = Math.floor(Math.random() * 1000);
            console.log(props)
        ';
    }

    protected function customCss(): string
    {
        return '';
    }

    protected function renderComponentComputedValues(): string
    {
        return parent::renderComponentComputedValues(). '
            uploadNewImageForm: function() {
                return "bulk_new_image_form_" + this.uniqueId
            },
            uploadNewImageId: function() {
                return "bulk_new_image_upload_" + this.uniqueId
            },
        ';
    }

    protected function renderTemplate() : string
    {
        /** @var App $app */
        global $app;
        return '<div class="formwrapper-control list-images-main-wrapper">
                    <v-style type="text/css">'.
            $this->customCss()
            .'
                        .BodyContentBox .list-images-main-wrapper .form-search-box {
                            top: 0px;
                        }
                        .BodyContentBox .list-images-main-wrapper .form-control {
                            position: relative;
                            top: -1px;
                            font-size: 13px;
                            padding: .100rem .75rem .150rem;
                            width: 140px;
                            line-height: 1.1;
                            height: calc(1.55rem + 2px);
                        }
                        .BodyContentBox .list-images-main-wrapper #entity-search-input {
                            margin-left: 5px;
                            position: relative;
                            top: -1px;
                        }
                        .list-images-main-wrapper .upload-new-logo {
                            position: absolute;
                            top: -55px;
                            left: 182px;
                        }
                        .vue-app-body-component .formwrapper-outer .formwrapper-control .vue-modal-wrapper .fformwrapper-header {
                            top:-9px;
                            position:relative;
                        }
                        .vue-app-body-component .formwrapper-outer .formwrapper-control .vue-modal-wrapper .account-page-title {
                            font-size: 1.5rem;
                            font-weight: 500;
                            top:-5px;
                        }
                        .vue-app-body-component .formwrapper-outer .formwrapper-control .vue-modal-wrapper .account-page-title .componentIconCards {
                            margin-right:5px;
                        }
                        .vue-app-body-component .formwrapper-outer .formwrapper-control .vue-modal-wrapper .account-page-title .componentIconCards:before {
                            content: "\\\f2c2";
                        }
                        .tableGridLayout .card-list-outer tbody tr td:nth-child(5) {
                            order:-1;
                            font-family: \'Montserrat\', sans-serif;
                            font-size:1.3vw;
                            justify-content: center;
                            padding-bottom: 0;
                        }
                        .tableGridLayout .card-list-outer tbody tr td:nth-child(5): a {
                            text-decoration:none;
                        }
                        .tableGridLayout .card-list-outer tbody tr td:nth-child(4) {
                            font-family: \'Montserrat\', sans-serif;
                            font-size:0.9vw;
                        }
                        .tableGridLayout .card-list-outer tbody tr td:nth-child(3),
                        .tableGridLayout .card-list-outer tbody tr td:nth-child(2),
                        .tableGridLayout .card-list-outer tbody tr td:nth-child(7),
                        .tableGridLayout .card-list-outer tbody tr td:nth-child(8),
                        .tableGridLayout .card-list-outer tbody tr td:nth-child(9),
                        .tableGridLayout .card-list-outer tbody tr td:nth-child(10),
                        .tableGridLayout .card-list-outer tbody tr td:nth-child(6) {
                            display:none;
                        }
                        .formwrapper-control .entityListOuter:not(.tableGridLayout) td:nth-child(0) {
                            width:125px;
                        }
                        .formwrapper-control .entityListOuter:not(.tableGridLayout) td:nth-child(1),
                        .formwrapper-control .entityListOuter:not(.tableGridLayout) td:nth-child(2) {
                            width:100px;
                        }
                        @media (max-width:750px) {
                            .vue-app-body-component .vue-app-body-component .formwrapper-outer .formwrapper-control .vue-modal-wrapper .fformwrapper-header {
                                top:0;
                                position:relative;
                            }
                        }
                    </v-style>'.'
                    <button class="btn btn-primary upload-new-logo" v-on:click="openUploadDialog()">Upload New Logo</button>
                    <input v-bind:id="uploadNewImageId" multiple="true" type="file" v-on:change="submitNewImageUpload" style="display:none;">
                    <div class="fformwrapper-header">
                        <table class="entity-list-header-wrapper table header-table" style="margin-bottom:0px;">
                            <tbody>
                            <tr>
                                <td>
                                    <h3 class="account-page-title" style="display:none;">
                                        <span class="componentIcon" v-bind:class="\'componentIcon\' + component_title.replace(\' \', \'\')"></span>
                                        {{ component_title }} 
                                    </h3>
                                    <div class="form-search-box" v-cloak>
                                        <table>
                                            <tr>
                                                <td>
                                                    <select id="entity-search-filter" class="form-control" @change="updatePage()">
                                                        <option value="card_num">Image Name</option>
                                                        <option value="card_owner_name">Image Url</option>
                                                        <option value="last_updated">Last Updated</option>
                                                        <option value="created_on">Created On</option>
                                                        <option value="everything" selected>Everything</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input id="entity-search-input" v-model="searchMainQuery" class="form-control" type="text" placeholder="Search..."/>
                                                </td>
                                                <td>
                                                    ' . ( $app->objCustomPlatform->getApplicationType() === Companies::APP_TYPE_DEFAULT ? '<button class="btn btn-sm btn-primary" v-on:click="openCartPackageSelection()" style="margin-left: 5px;margin-top: -4px;">Purchase New Card</button>' : '') . '
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                                <td class="text-right page-count-display" style="vertical-align: middle;">
                                ' . ( $app->objCustomPlatform->getApplicationType() === Companies::APP_TYPE_DEFAULT ? '
                                    <span class="page-count-display-data">
                                        Current: <span>{{ mainEntityPageIndex }}</span>
                                        Pages: <span>{{ totalMainEntityPages }}</span>
                                    </span>
                                    <button v-on:click="prevMainEntityPage()" class="btn prev-btn" :disabled="mainEntityPageIndex == 1">Prev</button>
                                    <button v-on:click="nextMainEntityPage()" class="btn" :disabled="mainEntityPageIndex == totalMainEntityPages">Next</button>
                                    <span>
                                        <span v-bind:class="{active: listLayoutType === \'grid\'}" v-on:click="toggleLayoutGrid" class="fas fa-th pointer"></span>
                                        <span v-bind:class="{active: listLayoutType === \'list\'}" v-on:click="toggleLayoutList" class="fas fa-list pointer"></span>
                                    </span>
                                    ' : '
                                    <button v-on:click="prevMainEntityPage()" class="btn prev-btn" :disabled="mainEntityPageIndex == 1">Prev</button>
                                    <span class="page-count-display-data">
                                        <span>{{ mainEntityPageIndex }}</span> / <span>{{ totalMainEntityPages }}</span>
                                    </span>
                                    <button v-on:click="nextMainEntityPage()" class="btn" :disabled="mainEntityPageIndex == totalMainEntityPages">Next</button>
                                    <span>
                                        <span v-bind:class="{active: listLayoutType === \'grid\'}" v-on:click="toggleLayoutGrid" class="fas fa-th pointer"></span>
                                        <span v-bind:class="{active: listLayoutType === \'list\'}" v-on:click="toggleLayoutList" class="fas fa-list pointer"></span>
                                    </span>
                                    ') . '
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="entityListOuter" v-bind:class="{tableGridLayout: listLayoutType === \'grid\'}">
                        <table class="card-list-outer table table-striped entityList">
                            <thead>
                            <th v-for="mainEntityColumn in mainEntityColumns">
                                <a v-on:click="orderByColumn(mainEntityColumn)" v-bind:class="{ active : orderKey == mainEntityColumn, sortasc : sortByType == true, sortdesc : sortByType == false }">
                                    {{ mainEntityColumn | ucWords }}
                                </a>
                            </th>
                            <th class="text-right">
                                Actions
                            </th>
                            </thead>
                            <tbody v-if="orderedMainEntityList.length > 0">
                            <tr v-for="mainEntity in orderedMainEntityList" v-on:dblclick="goToImageDashboard(mainEntity)" v-bind:class="{demoCard: (mainEntity.product_id === 1100) }">
                                '.$this->buildMainEntityDisplayFieldsForTable().'
                                <td class="text-right">
                                    <span v-on:click="goToCardDashboard(mainEntity)" class="pointer editEntityButton"></span>
                                    <span v-on:click="deleteMainEntity(mainEntity)" class="pointer deleteEntityButton"></span>
                                </td>
                            </tr>
                            </tbody>
                            <tbody v-if="orderedMainEntityList.length == 0 && batchEnd == true">
                                <tr><td colspan="100"><span><span class="fas fa-exclamation-triangle"></span> '.$this->noEntitiesWarning.'</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>';
    }

    protected function buildMainEntityDisplayFieldsForTable() : string
    {
        global $app;
        $columnList = "";

        foreach( $this->entity->getDisplayColumns() as $currColumn)
        {
            switch($currColumn)
            {
                case "thumb":
                    $columnList .= '<td><a target="_blank" v-on:click="goToImageDashboard(mainEntity)" v-bind:hrefX="imageServerUrl() + mainEntity.url"><span v-bind:style="{background: \'url(\' + imageServerUrl() + mainEntity.' . $currColumn . ' + \') no-repeat center center / cover, url(/_ez/images/no-image.jpg) no-repeat center center / cover\'}" width="75" height="75" class="main-list-image entity-banner"></span></a></td>';
                    break;
                case "status":
                    $columnList .= '<td class="statusColumn"><span v-bind:class="statusClass(mainEntity.' . $currColumn . ')">{{ mainEntity.' . $currColumn . ' }}</span></td>';
                    break;
                case "created_on":
                case "last_updated":
                    $columnList .= '<td>{{ formatDateForDisplay(mainEntity.' . $currColumn . ') }}</td>';
                    break;
                default:
                    $columnList .= "<td>{{ mainEntity.{$currColumn} }}</td>";
                    break;
            }
        }

        return $columnList;
    }
}