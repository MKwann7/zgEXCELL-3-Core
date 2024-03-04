<?php

namespace Entities\Cards\Components\Vue\HelperWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use App\website\vue\classes\VueComponentListTable;
use App\website\vue\classes\VueComponentSortableList;
use Entities\Cards\Models\CardModel;

class DomainManagementWidget extends VueComponent
{
    protected string $id = "cc607221-3d23-43d3-98bb-8a56c0a3a944";
    protected string $modalWidth = "550";
    protected string $mountType = "no_mount";
    protected string $title = "Member Login";

    public function __construct ($props = [], ?VueComponentListTable $listTable = null, ?VueComponentSortableList $sortableList = null)
    {
        parent::__construct((new CardModel()));

        $this->modalTitleForAddEntity = $this->title;
        $this->modalTitleForEditEntity = $this->modalTitleForAddEntity;
        $this->modalTitleForDeleteEntity = $this->modalTitleForAddEntity;
        $this->modalTitleForRowEntity = $this->modalTitleForAddEntity;
    }

    protected function renderComponentDataAssignments() : string
    {
        return '
            dynamicOwnerSearch: false,
            customerList: [],
            ownerSearch: "",
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '
        ';
    }

    protected function renderComponentMethods() : string
    {
        return '
            checkForDuplicateDomainName: function(entity)
            {
                const el = document.getElementById("domain_1603190947");
                
                if (!entity.card_domain) {    
                    el.classList.add("pass-validation")
                    el.classList.remove("error-validation")
                    return
                }
                
                this.entity.card_domain = this.entity.card_domain.replace("https", "")
                this.entity.card_domain = this.entity.card_domain.replace("http", "")
                this.entity.card_domain = this.entity.card_domain.replace("://", "")
                if (this.entity.card_domain.slice(-1) === "/") {
                    this.entity.card_domain = this.entity.card_domain.substring(0, this.entity.card_domain.length - 1)
                }
                
                const url = "/api/v1/cards/check-domain?card_domain=" + entity.card_domain + "&card_id=" + entity.card_id;

                ajax.Get(url, null, function(result) {
                    if (result.match === true) {
                        el.classList.remove("pass-validation")
                        el.classList.add("error-validation")
                        return;
                    }
                    
                    el.classList.add("pass-validation")
                    el.classList.remove("error-validation")
                });
            },
            checkForDuplicateVanityUrl: function(entity)
            {
                const el = document.getElementById("vanity_1603190947")
                
                if (entity.card_vanity_url === "") 
                {    
                    el.classList.add("pass-validation")
                    el.classList.remove("error-validation")
                    return
                }
                
                const url = "/api/v1/cards/check-vanity-url?vanity_url=" + entity.card_vanity_url + "&card_id=" + entity.card_id

                ajax.Get(url, null, function(result) 
                {
                    if (result.match === true) 
                    {
                        el.classList.remove("pass-validation")
                        el.classList.add("error-validation")
                        return
                    }
                    
                    el.classList.add("pass-validation")
                    el.classList.remove("error-validation")
                });
            },
            checkForDuplicateKeyword: function(entity)
            {
                const el = document.getElementById("keyword_1603190947")
                
                if (entity.card_keyword === "") 
                {    
                    el.classList.add("pass-validation")
                    el.classList.remove("error-validation")
                    return
                }
                
                const url = "api/v1/cards/check-keyword?keyword=" + entity.card_keyword + "&card_id=" + entity.card_id;
                
                ajax.Get(url, null, function(result) 
                {
                    if (result.match === true) 
                    {
                        el.classList.remove("pass-validation")
                        el.classList.add("error-validation")
                        return;
                    }
                    
                    el.classList.add("pass-validation")
                    el.classList.remove("error-validation")
                });
            },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentHydrationScript() . '
            this.entity = {}
        ';
    }

    protected function renderTemplate() : string
    {
        return '
            <div class="domainManagementWidget">
                <v-style type="text/css">
                </v-style>
                <table class="table no-top-border" >
                    <tbody>
                        <tr>
                            <td style="width:125px;vertical-align: middle;">Domain Name</td>
                            <td><input v-on:blur="checkForDuplicateDomainName(entity)" v-model="entity.card_domain" id="domain_1603190947" class="form-control pass-validation" type="text" placeholder="Enter Domain Name..."></td>
                        </tr>
                        <tr v-if="entity.card_domain !== \'\'">
                            <td style="width:125px;vertical-align: middle;">SSL Certificate?</td>
                            <td><label for="useSslForDomain">
                                <input v-model="entity.domain_ssl" id="useSslForDomain" type="radio" value="y"> Yes </label>&nbsp;&nbsp;
                                <label for="noSslForDomain">
                                <input v-model="entity.domain_ssl" id="noSslForDomain" type="radio" value="n"> No</label>
                            </td>
                        </tr>
                        <tr v-if="entity.card_domain !== \'\' && entity.domain_ssl === \'y\'">
                            <td style="width:125px;vertical-align: middle;">SSL Cert<br>(not bundle)</td>
                            <td>
                                <textarea v-model="entity.domain_ssl_cert" type="text" class="form-control">
                                </textarea>
                            </td>
                        </tr>
                        <tr v-if="entity.card_domain !== \'\' && entity.domain_ssl === \'y\'">
                            <td style="width:125px;vertical-align: middle;">SSL Private Key</td>
                            <td>
                                <textarea v-model="entity.domain_ssl_key" type="text" class="form-control">
                                </textarea>
                            </td>
                        </tr>
                        <tr v-if="userAdminRole && entity.template_id != 6">
                            <td style="width:125px;vertical-align: middle;">Vanity URL</td>
                            <td><input v-on:blur="checkForDuplicateVanityUrl(entity)" v-model="entity.card_vanity_url" id="vanity_1603190947" class="form-control pass-validation" type="text" placeholder="Enter Vanity URL..."></td>
                        </tr>
                        <tr v-if="userAdminRole && entity.card_type_id == 1">
                            <td style="width:125px;vertical-align: middle;">Keyword</td>
                            <td><input v-on:blur="checkForDuplicateKeyword(entity)" v-model="entity.card_keyword" id="keyword_1603190947" class="form-control pass-validation" type="text" placeholder="Enter Keyword..."></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        ';
    }
}