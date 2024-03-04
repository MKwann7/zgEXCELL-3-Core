<?php
/**
 * Created by PhpStorm.
 * User: Micah.Zak
 * Date: 10/11/2018
 * Time: 9:43 AM
 */

use App\Utilities\Database;

$this->CurrentPage->BodyId            = "view-my-cards-page";
$this->CurrentPage->BodyClasses       = ["admin-page", "view-my-cards-page", "two-columns", "left-side-column"];
$this->CurrentPage->Meta->Title       = "List My Cards | " . $this->app->objCustomPlatform->getPortalDomainName();
$this->CurrentPage->Meta->Description = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Meta->Keywords    = "";
$this->CurrentPage->SnipIt->Title     = "List My Cards";
$this->CurrentPage->SnipIt->Excerpt   = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Columns           = 0;

$this->LoadVenderForPageScripts($this->CurrentPage->BodyId, "froala");
$this->LoadVenderForPageScripts($this->CurrentPage->BodyId, "slim");
$this->LoadVenderForPageScripts($this->CurrentPage->BodyId, ["jquery"=>"color-picker/v1.0"]);
$this->LoadVenderForPageScripts($this->CurrentPage->BodyId, ["jquery"=>"input-picker/v1.0"]);
$this->LoadVendorForPageStyles($this->CurrentPage->BodyId, "froala");
$this->LoadVendorForPageStyles($this->CurrentPage->BodyId, "slim");
$this->LoadVendorForPageStyles($this->CurrentPage->BodyId, ["jquery"=>"color-picker/v1.0"]);
$this->LoadVendorForPageStyles($this->CurrentPage->BodyId, ["jquery"=>"input-picker/v1.0"]);

?>
<div class="breadCrumbs">
    <div class="breadCrumbsInner<?php if ( $strApproach === "view") { echo " edit-entity"; } ?>">
        <input id="entity-page-entrance" data-source="<?php if ( $strApproach === "view") { echo 'edit-entity'; } else { echo 'list-entities'; } ?>" type="hidden"/>
        <a href="/account" class="breadCrumbHomeImageLink">
            <img src="/media/images/home-icon-01_white.png" class="breadCrumbHomeImage" width="15" height="15" />
        </a> &#187;
        <a href="/account" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Home</span>
        </a> &#187;
        <span id="view-list">
            <span class="breadCrumbPage">List My Cards</span>
        </span>
        <span id="editing-entity">
            <a id="backToViewEntityList" href="/account/cards" class="breadCrumbHomeImageLink">
                <span class="breadCrumbPage">List My Cards</span>
            </a> &#187;
            <span class="breadCrumbPage">My Card Dashboard</span>
        </span>
    </div>
</div>
<?php $this->RenderPortalComponent("content-left-menu"); ?>
<div class="BodyContentBox BodyContentBoxOld">
    <style type="text/css">
        .BodyContentBox .entityList.table-striped td:nth-child(3),
        .BodyContentBox .entityList.table-striped td:nth-child(5) {
            width:8%;
        }

        .BodyContentBox .entityList.table-striped td {
            width:10%;
        }
        .BodyContentBox .entityList.table-striped td:first-child {
            width:2%;
        }
        .BodyContentBox .entityList.table-striped td:nth-child(2) {
            width:7%;
        }
        .BodyContentBox .entityList.table-striped td:nth-child(6) {
            width:5%;
        }
        .card-main-color-block {
            width:80px;height:160px;cursor:pointer;
        }
        .BodyContentBox .card-users_role {
            width:10%;
        }
        .BodyContentBox .card-users_first_name {
            width:12%;
        }
        .BodyContentBox .card-users_last_name {
            width:12%;
        }
        .entityDetailsInner .custom-card-handle {
            width: 3em;
            height: 1.6em;
            top: 50%;
            margin-top: -.8em;
            text-align: center;
            line-height: 1.6em;
            margin-left: -20px;
        }
        .style-button {
            padding: .15rem .75rem !important;
            margin-top: 5px;
        }

        .cards_role > div {
            transform: rotate(-90deg);
            padding: 3px 10px;
            margin: 0px -10px 0px -10px;
            border-radius: 4px;
        }

        .cards_role .cardowner {
            background: green;
        }

        .cards_role .cardowner::before {
            content: "Owner";
            color:#fff;
        }

        .cards_role .cardadmin {
            background: blue;
        }

        .cards_role .cardadmin::before {
            content: "Admin";
            color:#fff;
        }

        .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23fff' d='M6.564.75l-3.59 3.612-1.538-1.55L0 4.26 2.974 7.25 8 2.193z'/%3e%3c/svg%3e");
        }

        .custom-checkbox .custom-control-label::after {
            position: absolute;
            top: 0.25rem;
            left: -1.5rem;
            display: block;
            width: 1rem;
            height: 1rem;
            content: "";
            background: no-repeat 50% / 50% 50%;
        }

        .custom-checkbox .custom-control-input:indeterminate ~ .custom-control-label::before {
            border-color: #007bff;
            background-color: #007bff;
        }

        .custom-checkbox .custom-control-label::before {
            border-radius: 0.25rem;
        }

        .custom-control-label::before, .custom-file-label, .custom-select {
            transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .custom-control-label::before {
            position: absolute;
            top: 0.25rem;
            left: -1.5rem;
            display: block;
            width: 1rem;
            height: 1rem;
            pointer-events: none;
            content: "";
            background-color: #fff;
            border: #adb5bd solid 1px;
        }

        .contact-multiple-selection {
            position: relative;
            left: -5px;
            top: 2px;
            z-index: 4;
            cursor:pointer;
        }

        .dropdown-menu a {
            cursor:pointer;
        }

        @media (max-width:750px) {
            .card-main-color-block {
                width:75%;height:80px;cursor:pointer;
                margin-left:auto;
                margin-right:auto;
            }
            .main-list-image {
                width:35px;
                height:35px;
            }
            .cards_card_name,
            .cards_sponsor_id,
            .cards_product_id,
            .cards_last_updated {
                display:none;
            }
            .cards_banner {
                width:40px;
            }
            .table th {
                font-size:12px;
            }
            .entityDetails h4,
            .entityDetails h4 span {
                font-size: 1.2rem;
            }
            .entityDetails h4 .desktop-25px {
                width:20px !important;
            }
            .entityDetails h4 .desktop-30px {
                width:25px !important;
            }
            .editEntityButton::before {
                width: 15px;
                height: 15px;
                top: 6px;
            }
        }

        @media (max-width:620px) {
            .entityDetailsInner.cardProfile,
            .entityDetailsInner.cardStyles {
                overflow-x: visible;
                overflow-y: visible;
            }
        }

        @media (max-width:525px) {
            .entityDetailsInner .card-main-color-block,
            .entityDetailsInner .mobile-to-75 {
                width:100% !important;
                margin-left: auto;
                margin-right: auto;
            }
        }

        [v-cloak] { display: none; }
    </style>
    <div id="app" class="formwrapper">
        <div class="formwrapper-outer<?php if ( $strApproach === "view") { echo " edit-entity"; } ?>">
            <div class="formwrapper-control" v-cloak>
                <div class="fformwrapper-header">
                    <table class="table header-table" style="margin-bottom:0px;">
                        <tbody>
                        <tr>
                            <td>
                                <h3 class="account-page-title">My Cards  <span class="pointer addNewEntityButton entityButtonFixInTitle"  v-on:click="addCard()" ></h3>
                                <div class="form-search-box" v-cloak>
                                    <input v-model="searchCardQuery" class="form-control" type="text" placeholder="Search for..."/>
                                </div>
                            </td>
                            <td class="text-right page-count-display" style="vertical-align: middle;">
                                <span class="page-count-display-data">
                                    Current: <span>{{ pageIndex }}</span>
                                    Pages: <span>{{ totalPages }}</span>
                                </span>
                                <button v-on:click="prevPage()" class="btn prev-btn" :disabled="pageIndex == 1">Prev</button>
                                <button v-on:click="nextPage()" class="btn" :disabled="pageIndex == totalPages">Next</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="entityListOuter">
                    <table class="table table-striped entityList">
                        <thead>
                        <th v-for="cardColumn in cardColumns" :class="generateListItemClass('cards', cardColumn)">
                            <a v-on:click="orderByCard(cardColumn)" v-bind:class="{ active : orderKeyCard == cardColumn, sortasc : sortByTypeCard == true, sortdesc : sortByTypeCard == false }">
                                {{ cardColumn | ucWords }}
                            </a>
                        </th>
                        <th class="text-right">
                            Actions
                        </th>
                        </thead>
                        <tbody>
                        <tr v-for="card in orderedCards" v-on:dblclick="gotoCard(card)">
                            <td class="cards_role"><div v-bind:class="{ cardowner : card.user_rel_type_id == 1, cardadmin : card.user_rel_type_id == 2 }"></div></td>
                            <td class="cards_banner"><img class="main-list-image" v-bind:src="card.main_thumb" width="75" height="75" /></td>
                            <td class="cards_card_name">{{ card.card_name }}</td>
                            <td class="cards_card_num"><a v-bind:href="'/' + card.card_num" target="_blank">{{ card.card_num }}</a></td>
                            <td class="cards_card_vanity_url"><a v-bind:href="(card.card_vanity_url) ? '/' + card.card_vanity_url : ''" target="_blank">{{ card.card_vanity_url }}</a></td>
                            <td class="cards_product_id">{{ card.product_id }}</td>
                            <td class="cards_sponsor_id">{{ card.sponsor_id }}</td>
                            <td class="cards_contacts">{{ card.card_contacts }}</td>
                            <td class="cards_status">{{ card.status }}</td>
                            <td class="cards_last_updated">{{ card.last_updated }}</td>
                            <td class="text-right">
                                <span v-on:click="gotoCard(card)" class="pointer editEntityButton"></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="formwrapper-manage-entity">
                <div class="entityDashboard">
                    <table class="table header-table" style="margin-bottom:0px;">
                        <tbody>
                        <tr>
                            <td class="mobile-to-table">
                                <h3 class="account-page-title"><a id="back-to-entity-list" class="back-to-entity-list pointer"></a> Card Dashboard</h3>
                                <div class="form-search-box" v-cloak>
                                    <input v-model="searchCardQuery" class="form-control" type="text" placeholder="Search for..."/>
                                </div>
                            </td>
                            <td class="mobile-to-table text-right page-count-display dashboard-tab-display" style="vertical-align: middle;">
                                <div data-block="profile" class="dashboard-tab fas fa-user-circle" v-bind:class="{active: sessionStorage.getItem('dashboard-tab') == 'profile'}"><span>Profile</span></div>
                                <div data-block="tabs" class="dashboard-tab fas fa-list-ol" v-bind:class="{active: sessionStorage.getItem('dashboard-tab') == 'tabs'}"><span>Pages</span></div>
                                <div data-block="contacts" class="dashboard-tab fas fa-id-card" v-bind:class="{active: sessionStorage.getItem('dashboard-tab') == 'contacts'}"><span>Contacts</span></div>
                                <div data-block="users" class="dashboard-tab fas fa-users" v-bind:class="{active: sessionStorage.getItem('dashboard-tab') == 'users'}"><span>Users</span></div>
                                <div style="opacity:.4;" data-block="groups" class="dashboard-tab fas fa-layer-group1 fa-lock" v-bind:class="{active: sessionStorage.getItem('dashboard-tab') == 'groups'}"><span>Groups</span></div>
                                <div style="opacity:.4;" data-block="billing" class="dashboard-tab fas fa-credit-card1 fa-lock" v-bind:class="{active: sessionStorage.getItem('dashboard-tab') == 'billing'}"><span>Billing</span></div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <input id="dashboard-entity-id" type="hidden" value="<?php echo !empty($objCard->card_id) ? $objCard->card_id : ""; ?>" />
                    <input id="entity-owner-id" type="hidden" value="<?php echo !empty($objCard->owner_id) ? $objCard->owner_id : ""; ?>" />
                    <div class="entityTab" data-tab="profile">
                        <div class="width100 entityDetails">
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-user-circle fas-large desktop-25px"></span>
                                        <span class="fas-large">Profile</span>
                                        <span v-on:click="editCardProfile()" class="pointer editEntityButton entityButtonFixInTitle"></span></h4>
                                    <div class="entityDetailsInner cardProfile">
                                        <table>
                                            <tbody>
                                            <tr>
                                                <td>Card Name: </td>
                                                <td><strong id="entityCardName"><?php echo !empty($objCard->card_name) ? $objCard->card_name : ""; ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td>Card Number: </td>
                                                <td><strong id="entityCardNum"><a href="<?php echo getFullUrl(); ?>/<?php echo !empty($objCard->card_num) ? $objCard->card_num : ""; ?>" target="_blank"><?php echo !empty($objCard->card_num) ? $objCard->card_num : ""; ?></a></strong></td>
                                            </tr>
                                            <tr class="highlighed-field btn-primary pointer" v-on:click="goToCard()">
                                                <td>Vanity URL: </td>
                                                <?php if (!empty($objCard->card_vanity_url)) { ?>
                                                    <input id="card_vanity_url_value" type="hidden" value="<?php echo !empty($objCard->card_vanity_url) ? $objCard->card_vanity_url : ""; ?>" />
                                                    <td><strong id="entityVanityUrl"><?php echo getFullPublicUrl(); ?>/<?php echo !empty($objCard->card_vanity_url) ? $objCard->card_vanity_url : ""; ?></strong></td>
                                                <?php } else { ?>
                                                    <input id="card_vanity_url_value" type="hidden" value="??" />
                                                    <td><strong id="entityVanityUrl"><?php echo getFullPublicUrl(); ?>/<?php echo !empty($objCard->card_num) ? $objCard->card_num : ""; ?></strong></td>
                                                <?php } ?>
                                            </tr>
                                            <tr>
                                                <td>Card Theme: </td>
                                                <td><strong id="entityCardType"><?php echo !empty($objCard->template_id) ? $objCard->template_id : ""; ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td>Card Package: </td>
                                                <td><strong id="entityPackage"><?php echo !empty($objCard->product_id) ? $objCard->product_id : ""; ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td>Status: </td>
                                                <td><strong id="entityStatus"><?php echo !empty($objCard->status) ? $objCard->status : ""; ?></strong></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-images fas-large desktop-30px"></span>
                                        <span class="fas-large">Main Images</span>
                                        <span v-on:click="editMainImage()" class="pointer editEntityButton entityButtonFixInTitle"></span>
                                    </h4>
                                    <div class="entityDetailsInner" style="margin-top:5px;">
                                        <div class="divTable widthAuto mobile-to-100">
                                            <div class="divRow">
                                                <div class="divCell mobile-to-table mobile-text-center">
                                                    <strong class="mobile-center mobile-to-75">Card Banner</strong><br>
                                                    <img v-on:click="editMainImage()" class="pointer mobile-to-75 mobile-to-block mobile-vertical-margins-15 mobile-to-heightAuto mobile-center" id="entityMainImage" width="160" height="160" data-src="<?php echo $objCard->card_thumb ?? ""; ?>" src="<?php echo $objCard->card_thumb ?? ""; ?>" />
                                                </div>
                                                <div class="divCell mobile-hide" style="width:15px;"></div>
                                                <div class="divCell mobile-to-table mobile-text-center">
                                                    <strong class="mobile-center mobile-to-75">Card Favicon</strong><br>
                                                    <img v-on:click="editFaviconImage()" class="pointer" id="entityFavicon" width="64" height="64" data-src="<?php echo $objCard->favicon_image ?? ""; ?>" src="<?php echo $objCard->favicon_image ?? ""; ?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="width100 entityDetails">
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-share-alt fas-large desktop-25px"></span>
                                        <span class="fas-large">Share Buttons</span>
                                        <span class="pointer addNewEntityButton entityButtonFixInTitle" v-on:click="addConnection()" ></span>
                                    </h4>
                                    <render-portal-card-connections :card-connections="cardConnections"></render-portal-card-connections>
                                </div>
                            </div>

                            <div class="width50">
                                <div class="card-tile-50" style="min-height: 269px;">
                                    <h4>
                                        <span class="fas fa-cogs fas-large desktop-35px"></span>
                                        <span class="fas-large">Settings</span>
                                    </h4>
                                    <div class="entityDetailsInner cardStyles" style="margin-top:5px;">
                                        <div class="divTable">
                                            <div class="divRow">
                                                <div class="divCell desktop-90px mobile-to-table mobile-text-center">
                                                    <strong class="mobile-center mobile-to-75">Main Color</strong><br>
                                                    <div v-on:click="editCardMainColor()" class="card-main-color-block mobile-margin-top-15" style="background:#<?php echo $objCard->card_data->style->card->color->main ?? "ff0000"; ?>;"></div>
                                                </div>
                                                <div class="divCell mobile-hide" style="width:15px;"></div>
                                                <div class="divCell mobile-to-table mobile-text-center mobile-padding-bottom-15">
                                                    <div class="mobile-margin-top-15">
                                                        <strong class="mobile-center mobile-to-75">Card Width</strong><br>
                                                        <div class="mobile-center mobile-to-95 margin-top-15">
                                                            <div id="widthSlider">
                                                                <div id="custom-width-handle" class="ui-slider-handle custom-card-handle"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="margin-top-15">
                                                        <strong class="mobile-center mobile-to-75 margin-top-15">Page Height</strong><br>
                                                        <div class="mobile-center mobile-to-95 margin-top-15">
                                                            <div id="heightSlider">
                                                                <div id="custom-height-handle" class="ui-slider-handle custom-card-handle"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="margin-top-15">
                                                        <strong class="mobile-center mobile-to-75 margin-top-15">Custom Styles</strong><br>
                                                        <div class="mobile-center mobile-to-100">
                                                            <button v-on:click="editCardStyleCode()" type="button" class="btn btn-primary style-button mobile-to-100">Manage Style Code</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <script>
                                                    $( function() {
                                                        let handleWidth = $( "#custom-width-handle" );
                                                        let handleHeight = $( "#custom-height-handle" );

                                                        $( "#widthSlider" ).slider({
                                                            min: 300,
                                                            max: 500,
                                                            create: function() {
                                                                $(this).slider("value","<?php echo (($objCard->card_data->style->card->width ?? "") != "NaN") ? ($objCard->card_data->style->card->width ?? "") : "400"; ?>");
                                                                handleWidth.text( $( this ).slider( "value" ) );
                                                            },
                                                            slide: function( event, ui ) {
                                                                handleWidth.text( ui.value );
                                                            },
                                                            change: function( event, ui ) {
                                                                if(customerApp)
                                                                {
                                                                    customerApp.UpdateCardData("style.card.width", $( this ).slider( "value" ));
                                                                }
                                                            }
                                                        });

                                                        $( "#heightSlider" ).slider({
                                                            min: 40,
                                                            max: 70,
                                                            create: function() {
                                                                $(this).slider("value","<?php echo (($objCard->card_data->style->tab->height ?? "") != "NaN") ? ($objCard->card_data->style->tab->height ?? "") : "55"; ?>");
                                                                handleHeight.text( $( this ).slider( "value" ) );
                                                            },
                                                            slide: function( event, ui ) {
                                                                handleHeight.text( ui.value );
                                                            },
                                                            change: function( event, ui ) {
                                                                customerApp.UpdateCardData("style.tab.height", $( this ).slider( "value" ));
                                                                // console.log($( this ).slider( "value" ));
                                                            }
                                                        });
                                                    } );
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>

                    <div class="entityTab" data-tab="tabs">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                <h4 class="account-page-subtitle">
                                    <span class="fas fa-list-ol fas-large desktop-30px"></span>
                                    <span class="fas-large">Card Pages</span>
                                    <span class="pointer addNewEntityButton entityButtonFixInTitle"  v-on:click="addCardPage()" ></span></h4>
                                <div class="form-search-box" v-cloak>
                                    <input v-model="searchCardPageQuery" class="form-control" type="text" placeholder="Search for..."/>
                                </div>
                                <render-portal-card-tabs :card-tabs="cardTabs"></render-portal-card-tabs>

                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>

                    <div class="entityTab" data-tab="users">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                <h4 class="account-page-subtitle">
                                    <span class="fas fa-users fas-large desktop-30px"></span>
                                    <span class="fas-large">Card Users</span>
                                    <span style="display:none" class="pointer addNewEntityButton entityButtonFixInTitle" v-on:click="addCardUser()"></span>
                                </h4>
                                <div class="form-search-box" v-cloak>
                                    <input v-model="searchCardUserQuery" class="form-control" type="text" placeholder="Search for..."/>
                                </div>
                                <div class="entityDetailsInner">
                                    <table class="table table-striped" style="margin-top:10px;" v-cloak>
                                        <thead>
                                        <th v-for="cardUserColumn in cardUserColumns" :class="generateListItemClass('card-users', cardUserColumn)">
                                            <a v-on:click="orderByCardUser(cardUserColumn)" v-bind:class="{ active : orderKeyCardUser == cardUserColumn, sortasc : sortByTypeCardUser == true, sortdesc : sortByTypeCardUser == false }">
                                                {{ cardUserColumn | ucWords }}
                                            </a>
                                        </th>
                                        <th class="text-right">
                                            Actions
                                        </th>
                                        </thead>
                                        <tbody>
                                        <tr v-for="user in cardUsers" v-on:dblclick="viewUser(user)" class="pointer">
                                            <td><strong class="card-user-role">{{ user.role }}</strong></td>
                                            <td>{{ user.first_name }}</td>
                                            <td>{{ user.last_name }}</td>
                                            <td>{{ user.username }}</td>
                                            <td class="text-right">
                                                <span v-on:click="editCardUser(user)" class="pointer editEntityButton"></span>
                                                <span v-on:click="removeCardUser(user)" class="pointer deleteEntityButton"></span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="width100 entityDetails" style="display:none;">
                            <div class="card-tile-100">
                                <h4 class="account-page-subtitle">Card Members <span class="pointer addNewEntityButton entityButtonFixInTitle"  v-on:click="addToCardAffiliate()" ></span></h4>
                                <div class="form-search-box" v-cloak>
                                    <input v-model="searchCardAffiliateQuery" class="form-control" type="text" placeholder="Search for..."/>
                                </div>
                                <table class="table table-striped" style="margin-top:10px;" v-cloak>
                                    <thead>
                                    <th v-for="cardAffiliateColumn in cardAffiliateColumns" :class="generateListItemClass('affiliates', cardAffiliateColumn)">
                                        <a v-on:click="orderByCardAffiliate(cardAffiliateColumn)" v-bind:class="{ active : orderKeyCardAffiliate == cardAffiliateColumn, sortasc : sortByTypeCardAffiliate == true, sortdesc : sortByTypeCardAffiliate == false }">
                                            {{ cardAffiliateColumn | ucWords }}
                                        </a>
                                    </th>
                                    <th class="text-right">
                                        Actions
                                    </th>
                                    </thead>
                                    <tbody>
                                    <tr v-for="cardAffiliate in orderedCardAffiliates" v-on:dblclick="gotoCardAffiliate(cardAffiliate)">
                                        <td>{{ cardAffiliate.epp_level}}</td>
                                        <td>{{ cardAffiliate.user_id}}</td>
                                        <td>{{ cardAffiliate.first_name }}</td>
                                        <td>{{ cardAffiliate.last_name }}</td>
                                        <td>{{ cardAffiliate.affiliate_type }}</td>
                                        <td>{{ cardAffiliate.status }}</td>
                                        <td>{{ cardAffiliate.epp_value }}</td>
                                        <td class="text-right">
                                            <span v-on:click="editCardAffiliateAssignment(cardAffiliate)" class="pointer editEntityButton"></span>
                                            <span v-on:click="deleteCardAffiliateAssignment(cardAffiliate)" class="pointer deleteEntityButton"></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>

                    <div class="entityTab" data-tab="contacts">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                <h4 class="account-page-subtitle">
                                    <span class="fas fa-id-card fas-large desktop-30px"></span>
                                    <span class="fas-large">Card Contacts</span>
                                </h4>
                                <div class="form-search-box" v-cloak>
                                    <input v-model="searchCardContactQuery" class="form-control" type="text" placeholder="Search for..."/>
                                </div>
                                <div class="page-right-hand-tools page-count-display-data">
                                    <div class="btn-group" role="group" aria-label="Button group with nested dropdown" style="margin-right:10px;">
                                        <button v-on:click="MessageContactsModal()" class="btn btn-primary btn-sm">Message All Contacts</button>
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1">
                                                <a class="dropdown-item" v-on:click="MessageSelectedContactsModal()"><i class="fas fa-envelope" style="margin-right: 5px;"></i>Message Selected</a>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        $(function()
                                        {
                                            $(document).on("click", '.dropdown-toggle', function()
                                            {
                                                if ($("div[aria-labelledby='btnGroupDrop1']").length > 0)
                                                {
                                                    $("div[aria-labelledby='btnGroupDrop1']").toggleClass("show");
                                                }
                                            });
                                        });
                                    </script>

                                    <span>Current: <span>{{ contactPageIndex }}</span></span>
                                    <span>Pages: <span>{{ totalContactPages }}</span></span>&nbsp;&nbsp;
                                    <span>Count: <span>{{ cardContacts.length }}</span></span>&nbsp;&nbsp;
                                    <button v-on:click="prevContactPage()" class="btn prev-btn" :disabled="contactPageIndex == 1">Prev</button>
                                    <button v-on:click="nextContactPage()" class="btn" :disabled="contactPageIndex == totalContactPages">Next</button>
                                </div>
                                <div class="entityDetailsInner">
                                    <table class="table table-striped" style="margin-top:10px;" v-cloak>
                                        <thead>
                                        <th v-for="cardContactColumn in cardContactColumns" :class="generateListItemClass('card-contacts', cardContactColumn)">
                                            <a v-on:click="orderByCardContact(cardContactColumn)" v-bind:class="{ active : orderKeyCardContact == cardContactColumn, sortasc : sortByTypeCardContact == true, sortdesc : sortByTypeCardContact == false }">
                                                {{ cardContactColumn | ucWords }}
                                            </a>
                                        </th>
                                        <th class="text-right">
                                            Actions
                                        </th>
                                        </thead>
                                        <tbody>
                                        <tr v-for="contact in orderedCardContacts" class="pointer">
                                            <td class="contacts_rel_id">{{ contact.contact_id }}</td>
                                            <td class="contacts_first_name">{{ contact.first_name }}</td>
                                            <td class="contacts_last_name">{{ contact.last_name }}</td>
                                            <td class="contacts_phone">{{ formatAsPhoneIfApplicable(contact.phone_number) }}</td>
                                            <td class="contacts_email">{{ contact.email }}</td>
                                            <td class="contacts_created_on">{{ contact.created_on }}</td>
                                            <td class="text-right">
                                                <span>
                                                    <div class="custom-control custom-checkbox" style="display:inline;">
                                                        <input v-model="contact.selected" type="checkbox" class="custom-control-input contact-multiple-selection">
                                                        <label class="custom-control-label" for="defaultChecked">&nbsp;</label>
                                                    </div>
                                                </span>
                                                <span v-on:click="messageContactModal(contact)" style="margin-right: 5px;" class="pointer mailEntityButton fas fa-envelope"></span>
                                                <span v-on:click="editContact(contact)" class="pointer editEntityButton"></span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>

                    <div class="entityTab" data-tab="groups">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                <h4 class="account-page-subtitle">
                                    <span class="fas fa-layer-group1 fa-lock fas-large desktop-25px"></span>
                                    <span class="fas-large">Card Groups</span>
                                    <span class="pointer addNewEntityButton entityButtonFixInTitle" v-on:click="addToCardGroup()" ></span>
                                </h4>
                                <table class="table table-striped" style="margin-top:10px;" v-cloak>
                                    <thead>
                                    <th v-for="cardGroupColumn in cardGroupColumns" :class="generateListItemClass('card-group', cardGroupColumn)">
                                        <a v-on:click="orderByCardPage(cardTabColumn)" v-bind:class="{ active : orderKeyCardGroup == cardGroupColumn, sortasc : sortByTypeCardGroup == true, sortdesc : sortByTypeCardGroup == false }">
                                            {{ cardGroupColumn | ucWords }}
                                        </a>
                                    </th>
                                    <th class="text-right">
                                        Actions
                                    </th>
                                    </thead>
                                    <tbody>
                                    <tr v-for="cardGroup in orderedCardGroups" v-on:dblclick="gotoCardGroup(cardGroup)">
                                        <td>{{ cardGroup.title}}</td>
                                        <td>{{ cardGroup.card_tab_type_id }}</td>
                                        <td>{{ cardGroup.visibility }}</td>
                                        <td>{{ cardGroup.permanent }}</td>
                                        <td>{{ cardGroup.created_on }}</td>
                                        <td>{{ cardGroup.last_updated }}</td>
                                        <td class="text-right">
                                            <span v-on:click="editCardGroupAssignment(cardGroup)" class="pointer editEntityButton"></span>
                                            <span v-on:click="deleteCardGroupAssignment(cardGroup)" class="pointer deleteEntityButton"></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>

                    <div class="entityTab" data-tab="billing">
                        <div class="width100 entityDetails">
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-credit-card1 fa-lock fas-large desktop-30px"></span>
                                        <span class="fas-large">Payment Account</span>
                                         <span v-on:click="editCardPaymentAccount()" class="pointer editEntityButton entityButtonFixInTitle"></span></h4>
                                    <div class="entityDetailsInner">
                                    </div>
                                </div>
                            </div>
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4 class="account-page-subtitle">History</h4>
                                    <table class="table table-striped" style="margin-top:10px;" v-cloak>
                                        <thead>
                                        <th v-for="transColumn in transColumns" :class="generateListItemClass('transaction', transColumn)">
                                            <a v-on:click="orderByTrans(transColumn)" v-bind:class="{ active : orderKeyTrans == transColumn, sortasc : sortByTypeTrans == true, sortdesc : sortByTypeTrans == false }">
                                                {{ transColumn | ucWords }}
                                            </a>
                                        </th>
                                        <th class="text-right">
                                            Actions
                                        </th>
                                        </thead>
                                        <tbody>
                                        <tr v-for="trans in orderedTrans" v-on:dblclick="viewTrans(trans)">
                                            <td>{{ trans.transaction_id }}</td>
                                            <td>{{ trans.created_on }}</td>
                                            <td>{{ trans.card_type_id }}</td>
                                            <td>{{ trans.card_name }}</td>
                                            <td>{{ trans.card_num }}</td>
                                            <td class="text-right">
                                                <span v-on:click="viewTrans(trans)" class="pointer viewEntityButton"></span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">

    const { ContainerMixin, ElementMixin, HandleDirective } = window.VueSlicksort;

    const standardSortableList = {
        mixins: [ContainerMixin],
        template: `
    <table class="table table-striped no-top-border table-shadow" v-cloak>
		<tbody>
              <slot />
        </tbody>
	</table>
        `
    };

    const connectionSortableItem = {
        mixins: [ElementMixin],
        directives: { handle: HandleDirective },
        props: ['connection'],
        template: `
            <tr class="pointer sortable-item" v-on:dblclick="editConnectionRel(connection)">
                <td><span v-handle class="handle"></span></td>
                <td class="mobile-hide">{{ connection.display_order }}</td>
                <td>{{ connection.connection_type_id }}</td>
                <td><strong class="entityEmailName">{{ trunc(connection.connection_value, 35, true) }}</strong></td>
                <td class="text-right">
                    <span v-if="connection.connection_type_id != 'blank'" v-on:click="editConnection(connection)" class="pointer editEntityButton"></span>
                    <span v-on:click="editConnectionRel(connection)" class="pointer swapEntityButton fas fa-retweet"></span>
                    <span v-if="connection.connection_type_id != 'blank'" v-on:click="removeConnection(connection)" class="pointer deleteEntityButton" style="margin-left:6px;"></span>
                </td>
            </tr>
        `,
        methods: {
            editConnectionRel: function(connection)
            {
                modal.EngageFloatShield();
                let data = {};
                data.title = "Change Card Connection";
                modal.EngagePopUpDialog(data, 750, 115, true);
                let intCardId = $('#dashboard-entity-id').val();
                let strViewRequestParameter = "view=editConnectionRel&id=" + intCardId + "&connection_id=" + connection.connection_id + "&connection_rel_id=" + connection.connection_rel_id + "&display_order=" + connection.display_order;

                modal.AssignViewToPopup("/cards/card-data/get-card-dashboard-views", strViewRequestParameter, function()
                {
                    if($(".select-action-for-card-connection").val() == "--Select Action--")
                    {
                        $(".select-action-for-card-connection").addClass("error-outline");
                        $("#editConnectionRelForm").prepend('<div class="alert alert-danger" role="alert">You must select an action.</div>');

                        return false;
                    }
                    modal.EngageFloatShield();
                },
                function (objResult)
                {
                    connection.connection_rel_id = objResult.connection.connection_rel_id;
                    connection.connection_id = objResult.connection.connection_id;
                    connection.connection_type_id = objResult.connection.connection_type_id;
                    connection.connection_value = objResult.connection.connection_value;
                    connection.action = objResult.connection.action;
                    connection.is_primary = objResult.connection.is_primary;
                    connection.status = objResult.connection.status;

                    modal.CloseFloatShield(function() {
                        modal.CloseFloatShield();
                    },500);
                });
            },

            editConnection: function(connection)
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Customer Connection";
                data.html = "Please note, changing this connection here will modify it on your account and on all other cards it is assigned to.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var strViewRequestParameter = "view=editConnection&connection_id=" + connection.connection_id;

                modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {
                        connection.connection_type_id = objResult.connection.connection_type_id;
                        connection.connection_value = objResult.connection.connection_value;

                        modal.CloseFloatShield(function() {
                            modal.CloseFloatShield();
                        },500);
                    },
                    function (objValidate)
                    {
                        if ($("#addConnectionForm #connection_type_id").val() == "")
                        {
                            $("#addConnectionForm #connection_type_id").addClass("error-validation").blur(function() {
                                $(this).removeClass("error-validation");
                            });

                            return false;
                        }

                        if ($("#addConnectionForm #connection_value").val() == "")
                        {
                            $("#addConnectionForm #connection_value").addClass("error-validation").blur(function() {
                                $(this).removeClass("error-validation");
                            });

                            return false;
                        }

                        return true;
                    });
            },

            removeConnection: function(connection)
            {
                modal.EngageFloatShield();
                let data = {};
                data.title = "Remove Card Connection?";
                data.html = "Are you sure you want to proceed?<br>Please confirm.";
                modal.EngagePopUpConfirmation(data, function() {
                    let intConnectionRelId = connection.connection_rel_id;
                    let intConnectionId = connection.connection_id;
                    let intCardId = $('#dashboard-entity-id').val();
                    ajax.Post("cards/card-data/update-card-data?type=remove-connection&id=" + intCardId + "&connection_id=" + intConnectionId + "&connection_rel_id=" + intConnectionRelId);
                    connection.connection_rel_id = null;
                    connection.connection_id = null;
                    connection.connection_type_id = "blank";
                    connection.connection_value = null;
                    connection.action = null;
                    connection.is_primary = null;
                    connection.status = null;
                    modal.CloseFloatShield(function() {
                        modal.CloseFloatShield();
                    });
                }, 400, 115);
            },
        }
    };

    const tabSortableItem = {
        mixins: [ElementMixin],
        directives: { handle: HandleDirective },
        props: ['tab'],
        template: `
            <tr class="pointer sortable-item" v-on:dblclick="editCardPage(tab)" v-bind:class="{'sortable-item-permanent':(tab.permanent == 1), 'sortable-item-library':(tab.library_tab == 1), 'sortable-item-clone':(tab.card_tab_rel_type == 'mirror')}">
                <td class="desktop-35px"><span v-handle class="handle"></span></td>
                <td class="desktop-35px mobile-hide"><span class="tab-type-icon"></span></td>
                <td class="desktop-35px mobile-hide">{{ tab.rel_sort_order}}</td>
                <td>{{ tab.title}}</td>
                <td class="mobile-hide">{{ tab.card_tab_type_id }}</td>
                <td class="mobile-hide">{{ tab.created_on }}</td>
                <td class="mobile-hide">{{ tab.last_updated }}</td>
                <td class="text-right">
                    <label class="switch-small">
                        <input name="visibility" type="checkbox" v-model="tab.rel_visibility" v-bind:true-value="true" v-bind:false-value="false" v-on:click="updateTabRelVisibility(tab)" >
                        <span class="slider round"></span>
                    </label>
                    <span v-on:click="editCardPage(tab)" class="pointer editEntityButton"></span>
                    <span v-on:click="deleteCardPage(tab)" v-if="(tab.permanent == 0 && tab.library_tab != 1)" class="pointer deleteEntityButton"></span>
                    <span v-on:click="deleteCardPageRel(tab)" v-if="(tab.permanent == 0 && tab.library_tab == 1)" class="pointer deleteEntityButton"></span>
                    <span v-if="(tab.permanent == 1)" class="pointer deleteEntityButton" style="opacity:.3"></span>
                </td>
            </tr>
        `,
        methods: {
            editCardPage: function(cardTab)
            {
                modal.EngageFloatShield();
                let data = {};
                data.title = "Edit Card Page";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 1000, 115, true);
                let intCardId = $('#dashboard-entity-id').val();
                let strViewRequestParameter = "view=editCardPage&id=" + intCardId + "&card_tab_id=" + cardTab.card_tab_id + "&card_tab_rel_id=" + cardTab.card_tab_rel_id + "&display_order=" + cardTab.display_order;

                modal.AssignViewToPopup("/cards/card-data/get-card-dashboard-views", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {
                        console.log(objResult);
                        cardTab.card_tab_id = objResult.tab.card_tab_id;
                        cardTab.card_id = objResult.tab.card_id;
                        cardTab.title = objResult.tab.title;
                        cardTab.content = objResult.tab.content;
                        cardTab.card_tab_type_id = objResult.tab.card_tab_type_id;
                        cardTab.rel_sort_order = objResult.tab.rel_sort_order;
                        cardTab.rel_visibility = objResult.tab.rel_visibility;

                        modal.CloseFloatShield(function() {
                            modal.CloseFloatShield();
                        },500);
                    },
                    null,
                    null,
                    function(strFormId)
                    {
                        console.log("form-load: " + strFormId);
                        if (typeof engageSelectLibraryTab === "function" )
                        {
                            engageSelectLibraryTab();
                        }
                    }
                );
            },
            deleteCardPage: function(cardTab)
            {
                if(cardTab.permanent == 1 || cardTab.card_tab_rel_type == "mirror") { return; }
                modal.EngageFloatShield();
                let data = {};
                data.title = "Remove Card Tab?";
                data.html = "Are you sure you want to proceed?<br>Please confirm.";
                modal.EngagePopUpConfirmation(data, function() {
                    let intTabnRelId = cardTab.card_tab_rel_id;
                    let intTabId = cardTab.card_tab_id;
                    let intCardId = $('#dashboard-entity-id').val();
                    ajax.Post("cards/card-data/update-card-data?type=remove-tab&id=" + intCardId + "&card_tab_id=" + intTabId + "&card_tab_rel_id=" + intTabnRelId);

                    customerApp.cardTabs = customerApp.cardTabs.filter(function (currTab) {
                        return cardTab.card_tab_rel_id != currTab.card_tab_rel_id;
                    });

                    modal.CloseFloatShield(function() {
                        modal.CloseFloatShield();
                    });
                }, 400, 115);
            },

            deleteCardPageRel: function(cardTab)
            {
                if(cardTab.permanent == 1 || cardTab.card_tab_rel_type == "mirror") { return; }
                modal.EngageFloatShield();
                let data = {};
                data.title = "Disconnect This Card Tab?";
                data.html = "Are you sure you want to proceed?<br>Please confirm.";
                modal.EngagePopUpConfirmation(data, function() {
                    let intTabnRelId = cardTab.card_tab_rel_id;
                    let intTabId = cardTab.card_tab_id;
                    let intCardId = $('#dashboard-entity-id').val();
                    ajax.Post1("cards/card-data/update-card-data?type=remove-tab-rel&id=" + intCardId + "&card_tab_id=" + intTabId + "&card_tab_rel_id=" + intTabnRelId);

                    customerApp.cardTabs = customerApp.cardTabs.filter(function (currTab) {
                        return cardTab.card_tab_rel_id != currTab.card_tab_rel_id;
                    });

                    modal.CloseFloatShield(function() {
                        modal.CloseFloatShield();
                    });
                }, 400, 115);
            },

            updateTabRelVisibility: function(tab)
            {
                window.setTimeout(function () {
                    let intEntityId = $('#dashboard-entity-id').val();
                    let blnVisibility = tab.rel_visibility;
                    console.log(tab);
                    ajax.Post("/cards/card-data/update-card-data?type=update-tab-rel-visibility&id=" + intEntityId + "&card_tab_id=" + tab.card_tab_id + "&card_tab_rel_id=" + tab.card_tab_rel_id + "&rel_visibility=" + blnVisibility, null, function (objResult) {
                        console.log(objResult);
                    });
                },500);
            },
        }
    };

    const connectionsSortableList = {
        name: 'Connection Sortable List',
        template: `
<div class="entityDetailsInner">
    <standardSortableList lockAxis="y" v-model="cardConnect" :useDragHandle="true" @input="onSortEnd($event)">
        <connectionSortableItem v-for="(connection, index) in cardConnect" :index="index" :key="index" :connection="connection"/>
    </standardSortableList>
</div>  `,
        components: {
            connectionSortableItem,
            standardSortableList
        },
        methods: {
            onSortEnd: function($event)
            {
                let reOrderedConnections = [];
                for(i = 0; i < $event.length; i++)
                {
                    reOrderedConnections[i] = {};
                    reOrderedConnections[i].connection_rel_id = $event[i].connection_rel_id;
                    reOrderedConnections[i].rel_sort_order = (i + 1 );
                    $event[i].rel_sort_order = (i + 1 );
                }

                let objConnectionsUpdate = {connections: btoa(JSON.stringify(reOrderedConnections))};
                this.$parent.cardConnections = $event;
                let intCardId = $('#dashboard-entity-id').val();
                ajax.Post("cards/card-data/update-card-data?type=reorder-connection&id=" + intCardId, objConnectionsUpdate);
            }
        },
        computed: {
            cardConnect: function(){
                return this.$parent.cardConnections;
            }
        }
    };

    Vue.component('render-portal-card-connections', {
        render: h => h(connectionsSortableList),
        props: ['cardConnections']
    });

    const tabsSortableList = {
        name: 'Tab Sortable List',
        template: `
<div class="entityDetailsInner">
    <standardSortableList lockAxis="y" v-model="cardTab" :useDragHandle="true" @input="onSortEnd($event)">
        <tabSortableItem v-for="(tab, index) in cardTab" :index="index" :key="index" :tab="tab"/>
    </standardSortableList>
</div>  `,
        components: {
            tabSortableItem,
            standardSortableList
        },
        methods: {
            onSortEnd: function($event)
            {
                let reOrderedTabs = [];
                for(i = 0; i < $event.length; i++)
                {
                    reOrderedTabs[i] = {};
                    reOrderedTabs[i].card_tab_rel_id = $event[i].card_tab_rel_id;
                    reOrderedTabs[i].rel_sort_order = (i + 1 );
                    $event[i].rel_sort_order = (i + 1 );
                }

                let objTabUpdate = {tabs: btoa(JSON.stringify(reOrderedTabs))};
                this.$parent.cardTabs = $event;
                let intCardId = $('#dashboard-entity-id').val();
                ajax.Post("cards/card-data/update-card-data?type=reorder-tabs&id=" + intCardId, objTabUpdate, function(data) {
                    console.log(data);
                    if (data.success == false)
                    {
                        alert("We apologize for the inconvenience, but there was an error updating your tabs. We've recorded this error and will provide a resolution right away.")
                    }
                });
            }
        },
        computed: {
            cardTab: function(){
                return this.$parent.cardTabs;
            }
        }
    };

    Vue.component('render-portal-card-tabs', {
        render: h => h(tabsSortableList),
        props: ['cardTabs']
    });

    let customerApp = new Vue({

        el: '#app',

        computed:
            {
                totalPages: function()
                {
                    return this.pageTotal;
                },

                totalCards: function()
                {
                    return this.cardTotal;
                },

                totalCardPages: function()
                {
                    return this.cardTabTotal;
                },

                totalCardGroups: function()
                {
                    return this.cardGroupTotal;
                },

                totalContactPages: function()
                {
                    return this.cardContactTotal;
                },

                totalCardAffiliates: function()
                {
                    return this.cardAffiliateTotal;
                },

                orderedCards: function()
                {
                    var self = this;

                    let objSortedCards = this.sortedEntity(this.searchCardQuery, this.cards, this.orderKeyCard, this.sortByTypeCard, this.pageIndex,  this.pageDisplay, this.pageTotal, function(data) {
                        self.pageTotal = data.pageTotal;
                        self.pageIndex = data.pageIndex;
                    });

                    return objSortedCards;
                },

                orderedCardPages: function()
                {
                    var self = this;

                    let objSortedCardPages = this.sortedEntity(this.searchCardPageQuery, this.cardTabs, this.orderKeyCardPage, this.sortByTypeCardPage, this.cardTabIndex,  this.cardTabDisplay, this.cardTabTotal, function(data) {
                        self.cardTabTotal = data.pageTotal;
                        self.cardTabIndex = data.pageIndex;
                    });

                    return objSortedCardPages;
                },

                orderedCardContacts: function()
                {
                    var self = this;

                    let objSortedCardPages = this.sortedEntity(this.searchCardContactQuery, this.cardContacts, this.orderKeyCardContact, this.sortByTypeCardContact, this.contactPageIndex,  this.cardContactDisplay, this.cardContactTotal, function(data) {
                        self.cardContactTotal = data.pageTotal;
                        self.contactPageIndex = data.pageIndex;
                    });

                    return objSortedCardPages;
                },

                orderedCardGroups: function()
                {
                    var self = this;

                    let objSortedCardPages = this.sortedEntity(this.searchCardGroupQuery, this.cardGroups, this.orderKeyCardPage, this.sortByTypeCardGroup, this.cardGroupIndex,  this.cardGroupDisplay, this.cardGroupTotal, function(data) {
                        self.cardGroupTotal = data.pageTotal;
                        self.cardGroupIndex = data.pageIndex;
                    });

                    return objSortedCardPages;
                },

                orderedCardAffiliates: function()
                {
                    var self = this;

                    let objSortedCardAffiliates = this.sortedEntity(this.searchCardAffiliateQuery, this.cardAffiliates, this.orderKeyCardAffiliate, this.sortByTypeCardAffiliate, this.cardAffiliateIndex,  this.cardAffiliateDisplay, this.cardAffiliateTotal, function(data) {
                        self.cardAffiliateTotal = data.pageTotal;
                        self.cardAffiliateIndex = data.pageIndex;
                    });

                    return objSortedCardAffiliates;
                },

                orderedTrans: function()
                {
                    var self = this;

                    let objSortedTrans = this.sortedEntity(this.searchTransQuery, this.transactions, this.orderKeyTrans, this.sortByTypeTrans, this.transIndex,  this.transDisplay, this.transTotal, function(data) {
                        self.transTotal = data.pageTotal;
                        self.transIndex = data.pageIndex;
                    });

                    return objSortedTrans;
                }
            },

        filters: {
            ucWords: function(str)
            {
                return str.replace(/_/g," ").replace(/\w\S*/g, function (txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            },

            orderBy: function(type)
            {

            }
        },

        methods: {

            generateListItemClass: function(label, columnItem)
            {
                return label + "_" + columnItem;
            },

            orderByColumn: function(column)
            {
                this.sortByType = ( this.orderKey == column ) ? ! this.sortByType : this.sortByType;

                this.orderKey = column;
            },

            orderByCard: function(column)
            {
                this.sortByTypeCard = ( this.orderKeyCard == column ) ? ! this.sortByTypeCard : this.sortByTypeCard;

                this.orderKeyCard = column;
            },

            orderByCardPage: function(column)
            {
                this.sortByTypeCardPage = ( this.orderKeyCardPage == column ) ? ! this.sortByTypeCardPage : this.sortByTypeCardPage;

                console.log(column);

                this.orderKeyCardPage = column;
            },

            orderByCardGroup: function(column)
            {
                this.sortByTypeCardGroup = ( this.orderKeyCardGroup == column ) ? ! this.sortByTypeCardGroup : this.sortByTypeCardGroup;

                this.orderKeyCardGroup = column;
            },

            orderByCardContact: function(column)
            {
                this.sortByTypeCardContact = ( this.orderKeyCardContact == column ) ? ! this.sortByTypeCardContact : this.sortByTypeCardContact;

                this.orderKeyCardContact = column;
            },

            orderByCardUser: function(column)
            {

                this.sortByTypeCardUser = ( this.orderKeyCardUser == column ) ? ! this.sortByTypeCardUser : this.sortByTypeCardUser;

                this.orderKeyCardUser = column;
            },

            orderByCardAffiliate: function(column)
            {

                this.sortByTypeCardAffiliate = ( this.orderKeyCardAffiliate == column ) ? ! this.sortByTypeCardAffiliate : this.sortByTypeCardAffiliate;

                this.orderKeyCardAffiliate = column;
            },

            orderByTrans: function(column)
            {

                this.sortByTypeTrans = ( this.orderKeyTrans == column ) ? ! this.sortByTypeTrans : this.sortByTypeTrans;

                this.orderKeyTrans = column;
            },

            sortedEntity: function (searchQuery, entity, orderkey, sortByType, pageIndex, pageDisplay, pageTotal, callback)
            {
                var returnData = {};

                returnData.pageIndex = pageIndex;

                let objOrderedEntity = _.orderBy(entity, orderkey, sortByType ? 'asc' : 'desc');

                let intStartIndex = ((returnData.pageIndex-1) * pageDisplay);
                let intIndexOffset = entity.length - intStartIndex;
                let intEndIndex = intStartIndex + (( pageDisplay <= intIndexOffset ) ? pageDisplay : intIndexOffset);

                if (!searchQuery) {
                    var intTotalPages = 1;

                    if (pageDisplay < objOrderedEntity.length) {
                        intTotalPages = objOrderedEntity.length / pageDisplay;
                    }

                    returnData.pageTotal = Math.ceil(intTotalPages);

                    if ( typeof callback === "function") {
                        callback(returnData);
                    }

                    return objOrderedEntity.slice(intStartIndex, intEndIndex);
                }

                let objFilteredEntity = objOrderedEntity.filter(function (currEntity)
                {
                    let searchRegex = new RegExp(searchQuery, 'i');
                    let intFoundMatch = false;
                    let arFoundMatch = false;
                    let arEntityKeys = Object.keys(currEntity);

                    for (let entityField in currEntity)
                    {
                        if ( typeof filterFields !== "undefined")
                        {
                            if(arFoundMatch[entityField] === true)
                            {
                                continue;
                            }

                            for (let indexFilters in filterFields)
                            {
                                if (arEntityKeys[entityField] == filterFields[indexFilters] )
                                {
                                    arFoundMatch[entityField] = true;
                                    continue;
                                }
                            }
                        }

                        if (searchRegex.test(currEntity[entityField])) {
                            //console.log(searchQuery);
                            intFoundMatch = true;
                        }
                    }

                    if (intFoundMatch == true) {

                        if ( typeof callback === "function") {
                            callback(returnData);
                        }

                        return currEntity;
                    }
                });

                let intOrderedIndexOffset = objOrderedEntity.length - intStartIndex;
                let intOrderedEndIndex = intStartIndex + (( pageDisplay <= intOrderedIndexOffset ) ? pageDisplay : intOrderedIndexOffset);

                if (objFilteredEntity.length < intStartIndex) {

                    intStartIndex = Math.floor(objFilteredEntity.length / pageDisplay) * pageDisplay;
                    returnData.pageIndex =  Math.ceil(objFilteredEntity.length / pageDisplay);
                    intOrderedIndexOffset = objFilteredEntity.length - intStartIndex;
                    intOrderedEndIndex = intStartIndex + intOrderedIndexOffset;
                }

                var intTotalFilteredPages = 1;

                if (pageDisplay < objFilteredEntity.length) {
                    intTotalFilteredPages = objFilteredEntity.length / pageDisplay;
                }

                returnData.pageTotal = Math.ceil(intTotalFilteredPages);

                if ( typeof callback === "function") {
                    callback(returnData);
                }

                return objFilteredEntity.slice(intStartIndex, intOrderedEndIndex);
            },

            gotoCard: function(card)
            {
                var stateObj = { foo: "bar" };

                modal.EngageFloatShield();

                $('#dashboard-entity-id').val(card.card_id);
                $('#entity-owner-id').val(card.owner_id);
                $('#entityCardName').html(card.card_name);
                $('#entityCardNum a').attr("href","/" + card.card_num).text(card.card_num);
                if (card.card_vanity_url)
                {
                    $('#entityVanityUrl').html("<?php echo getFullPublicUrl(); ?>/" + card.card_vanity_url);
                }
                else
                {
                    $('#entityVanityUrl').html("<?php echo getFullPublicUrl(); ?>/" + card.card_num);
                }
                $('#entityCardType').html(card.template_id);
                $('#entityPackage').html(card.product_id);
                $('#entityStatus').html(card.status);
                //
                $(".formwrapper-outer").addClass("edit-entity");
                $(".breadCrumbsInner").addClass("edit-entity");

                var entityIdParameter = "card_id=" + card.card_id;

                this.cardTabs = [];
                this.cardUsers = [];
                this.cardGroups = [];
                this.cardContacts = [];
                this.cardAffiliates = [];
                this.cardConnections = [];

                ajax.Post("cards/card-data/get-card-dashboard-info", entityIdParameter, function(objCardResult)
                {
                    if (objCardResult.success == false)
                    {
                        console.log(objCardResult.message);
                        var data = {};
                        data.title = "Card View Error...";
                        data.html = objCardResult.message;
                        modal.AddFloatDialogMessage(data);
                        return false;
                    }

                    setTimeout(function(){
                        $(".formwrapper-outer.edit-entity .formwrapper-control").fadeTo(100,0,function() {
                            $(this).hide();
                        });
                    },10);

                    let cardWidth = 400;
                    if (objCardResult.data.card.card_width != "NaN")
                    {
                        let cardWidth = objCardResult.data.card.card_width;
                    }

                    let cardTabHeight = 55;
                    if (objCardResult.data.card.card_tab_height != "NaN")
                    {
                        let cardTabHeight = objCardResult.data.card.card_tab_height;
                    }

                    $('#entityMainImage').attr("data-src", objCardResult.data.card.card_thumb);
                    $('#entityMainImage').attr("src", objCardResult.data.card.card_thumb);
                    $('#entityFavicon').attr("data-src", objCardResult.data.card.favicon_image);
                    $('#entityFavicon').attr("src", objCardResult.data.card.favicon_image);
                    $('#widthSlider').slider("value", cardWidth);
                    $('#custom-width-handle').html(cardWidth);
                    $('#heightSlider').slider("value", cardTabHeight);
                    $('#custom-height-handle').html(cardTabHeight);
                    $('.card-main-color-block').css("backgroundColor", "#" + objCardResult.data.card.card_primary_color);

                    let lstSortedCardPages = sortObject(objCardResult.data.cardTabs,"rel_sort_order", true, false);
                    for(var intCardPageIndex in lstSortedCardPages)
                    {
                        console.log(lstSortedCardPages[intCardPageIndex]);
                        customerApp.cardTabs.push(lstSortedCardPages[intCardPageIndex]);
                    }

                    for(var intConnectionIndex in objCardResult.data.cardConnections)
                    {
                        customerApp.cardConnections.push(objCardResult.data.cardConnections[intConnectionIndex]);
                    }

                    for(var intUserIndex in objCardResult.data.cardUsers)
                    {
                        customerApp.cardUsers.push(objCardResult.data.cardUsers[intUserIndex]);
                    }

                    for(var intContactIndex in objCardResult.data.cardContacts)
                    {
                        customerApp.cardContacts.push(objCardResult.data.cardContacts[intContactIndex]);
                    }

                    customerApp.loadColorPicker();

                    // success
                    history.pushState(stateObj, "View Card", "/account/cards/view-card?id=" + card.card_num);

                    dash.loadDashboardTabs();

                    modal.CloseFloatShield();

                });
            },

            deleteColumn: function(card)
            {
                this.people = this.people.filter(function (curCard) {
                    return card.card_id != curCard.user_id;
                });
            },

            prevPage: function()
            {
                this.pageIndex--;

                this.cards = this.cards;
            },

            nextPage: function()
            {
                this.pageIndex++;

                this.cards = this.cards;
            },

            prevContactPage: function()
            {
                this.contactPageIndex--;
                this.updatePage();
            },

            nextContactPage: function()
            {
                this.contactPageIndex++;
                this.updatePage();
            },

            goToCard: function()
            {

                let strCardUrlValue = $("#card_vanity_url_value").val();

                if (strCardUrlValue == "")
                {
                    modal.EngageFloatShield();
                    var data = {};
                    data.title = "Request Custom Vanity URL";
                    //data.html = "We are logging you in.<br>Please wait a moment.";
                    modal.EngagePopUpDialog(data, 550, 115, true);

                    var intEntityId = $('#dashboard-entity-id').val();
                    var intOwnerId = $('#entity-owner-id').val();
                    var strViewRequestParameter = "view=requestVanityUrl&card_id=" + intEntityId + "&user_id=" + intOwnerId;

                    modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {
                        //customerApp.connections.push(objResult.connection);

                        modal.CloseFloatShield(function() {
                            modal.CloseFloatShield();
                        },500);
                    });
                }
                else
                {

                }

                let strCardUrl = $("#entityVanityUrl").html();

                window.open(
                    strCardUrl,
                    '_blank'
                );
            },

            addCard: function()
            {
                window.location = "/account/cards/get-new-card";
            },

            addConnection: function()
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Add A Profile Connection";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var intEntityId = $('#entity-owner-id').val();
                var strViewRequestParameter = "view=addConnection&user_id=" + intEntityId;

                console.log(strViewRequestParameter);

                modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter, function()
                {
                    modal.EngageFloatShield();
                },
                function (objResult)
                {
                    modal.CloseFloatShield(function() {
                        modal.CloseFloatShield();
                    },500);
                },
                function (objValidate)
                {
                    if ($("#addConnectionForm #connection_type_id").val() == "")
                    {
                        $("#addConnectionForm #connection_type_id").addClass("error-validation").blur(function() {
                            $(this).removeClass("error-validation");
                        });

                        return false;
                    }

                    if ($("#addConnectionForm #connection_value").val() == "")
                    {
                        $("#addConnectionForm #connection_value").addClass("error-validation").blur(function() {
                            $(this).removeClass("error-validation");
                        });

                        return false;
                    }

                    return true;
                });
            },

            addCardUser: function()
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Add Card User";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var strViewRequestParameter = "view=addCardUser";

                modal.AssignViewToPopup("/cards/card-data/get-card-dashboard-views", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {
                        modal.CloseFloatShield(function() {
                            modal.CloseFloatShield();
                        },500);
                    },
                    function (objValidate)
                    {
                        if ($("#addConnectionForm #connection_type_id").val() == "")
                        {
                            $("#addConnectionForm #connection_type_id").addClass("error-validation").blur(function() {
                                $(this).removeClass("error-validation");
                            });

                            return false;
                        }

                        if ($("#addConnectionForm #connection_value").val() == "")
                        {
                            $("#addConnectionForm #connection_value").addClass("error-validation").blur(function() {
                                $(this).removeClass("error-validation");
                            });

                            return false;
                        }

                        return true;
                    });
            },

            editCardUser: function(user)
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Card User";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var strViewRequestParameter = "view=editCardUser&user_id=" + user.user_id + "&card_id=" + $('#dashboard-entity-id').val();

                console.log(strViewRequestParameter);

                modal.AssignViewToPopup("/cards/card-data/get-card-dashboard-views", strViewRequestParameter);
            },

            editMainImage: function(card)
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Card Banner";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 500, 115, true);

                var strViewRequestParameter = "view=editMainImage&card_id=" + $('#dashboard-entity-id').val();

                console.log(strViewRequestParameter);

                modal.AssignViewToPopup("/cards/card-data/get-card-dashboard-views", strViewRequestParameter);
            },

            editFaviconImage: function(card)
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Favicon Image";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 250, 250, true);

                var strViewRequestParameter = "view=editFaviconImage&card_id=" + $('#dashboard-entity-id').val();

                console.log(strViewRequestParameter);

                modal.AssignViewToPopup("/cards/card-data/get-card-dashboard-views", strViewRequestParameter);
            },

            editCardMainColor: function(card)
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Card Main Color";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 520, 115, true);

                var strViewRequestParameter = "view=editCardMainColor&card_id=" + $('#dashboard-entity-id').val();

                modal.AssignViewToPopup("/cards/card-data/get-card-dashboard-views", strViewRequestParameter);
            },

            editCardStyleCode: function(card)
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Card Style Code";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 650, 115, true);

                var strViewRequestParameter = "view=editCardStyleCode&card_id=" + $('#dashboard-entity-id').val();

                console.log(strViewRequestParameter);

                modal.AssignViewToPopup("/cards/card-data/get-card-dashboard-views", strViewRequestParameter);
            },

            addCardPage: function()
            {
                modal.EngageFloatShield();
                let data = {};
                data.title = "Add Card Page";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 1000, 115, true);
                let intCardId = $('#dashboard-entity-id').val();
                let intOwnerCardId = $('#entity-owner-id').val();
                let strViewRequestParameter = "view=addCardPage&id=" + intCardId + "&owner_id=" + intOwnerCardId;
                let strContentPrefix = '<div class="tabSelectionOuter divTable"><div class="tabSelectionRow divRow"><div class="divCell tabSelectionLabel tabSelectionHtmlTab" style="width:calc(100% - 8px);" onclick="customerApp.LoadTabHtmlSelection();"><h2>Create a New HTML Page</h2><div class="tabSelectionActionButton"><i class="fas fa-file-code"></i></div></div><div class="divCell tabSelectionLabel tabSelectionLibraryTabs" style="width:calc(100% - 6px);" onclick="customerApp.LoadTabLibrarySelection();"><h2>Select A Library Widget</h2><div class="tabSelectionActionButton"><i class="fas fa-book"></i></div></div><div class="divCell tabSelectionLabel tabSelectionSpecialTabs" style="width:calc(100% - 6px);" onclick="customerApp.LoadTabSpecialtySelection();"><h2>Select a Specialty Widget</h2><div class="tabSelectionActionButton"><i class="fas fa-clone"></i></div></div></div></div>';

                modal.AssignViewToPopup("/cards/card-data/get-card-dashboard-views", strViewRequestParameter, function()
                {
                    modal.EngageFloatShield();
                },
                function (objResult)
                {
                    customerApp.cardTabs.push(objResult.tab);

                    modal.CloseFloatShield(function() {
                        modal.CloseFloatShield();
                    },500);
                },
                function (objValidate)
                {
                    let strModalType = $("#card_tab_management_type").val();

                    switch(strModalType)
                    {
                        case "addCardPage":
                        case "editCardPage":
                            if ($("#addCardPageForm #tab_title").val() == "")
                            {
                                $("#addCardPageForm #tab_title").addClass("error-validation").blur(function() {
                                    $(this).removeClass("error-validation");
                                });

                                return false;
                            }
                            break;
                        case "addLibraryTab":
                            if ($("#addCardPageLibraryTabForm #tab_title").val() == "")
                            {
                                $("#addCardPageLibraryTabForm #tab_title").addClass("error-validation").blur(function() {
                                    $(this).removeClass("error-validation");
                                });

                                return false;
                            }
                            break;

                    }

                    return true;
                },
                strContentPrefix);
            },

            addToCardGroup: function()
            {
                modal.EngageFloatShield();
                let data = {};
                data.title = "Add To Card Group";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);
                let intCardId = $('#dashboard-entity-id').val();
                let strViewRequestParameter = "view=addToCardGroup&id=" + intCardId;

                modal.AssignViewToPopup("/cards/card-data/get-card-dashboard-views", strViewRequestParameter, function()
                {
                    modal.EngageFloatShield();
                },
                function (objResult)
                {
                    customerApp.cardTabs.push(objResult.tab);

                    modal.CloseFloatShield(function() {
                        modal.CloseFloatShield();
                    },500);
                });
            },

            editCardProfile: function()
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Card Profile";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var intEntityId = $('#dashboard-entity-id').val();
                var strViewRequestParameter = "view=editCardProfile&card_id=" + intEntityId;

                modal.AssignViewToPopup("/cards/card-data/get-card-dashboard-views", strViewRequestParameter, function()
                {
                    modal.EngageFloatShield();
                },
                function (objResult)
                {
                    $('#entityCardName').html(objResult.card.card_name);

                    modal.CloseFloatShield(function() {
                        modal.CloseFloatShield();
                    },500);
                },
                function (objValidate)
                {
                    let intErrors = 0;

                    if ($("#editCardProfileForm .error-text").length > 0)
                    {
                        return false;
                    }

                    $("#editCardProfileForm .error-validation[name!=card_vanity_url]").removeClass("error-validation");

                    for(let intFieldIndex in objValidate)
                    {
                        if (objValidate[intFieldIndex].value == "")
                        {
                            intErrors++;
                            $("#editCardProfileForm input[name=" + objValidate[intFieldIndex].name + "], #editCardProfileForm select[name=" + objValidate[intFieldIndex].name + "]").addClass("error-validation");
                        }
                    }

                    if (intErrors > 0)
                    {
                        return false;
                    }

                    return true;
                });
            },

            LoadTabLibrarySelection: function()
            {
                $(".tabSelectionOuter").hide();
                setTimeout(function() {
                        customerApp.DelayedTabLibrarySectionLoad();
                    }
                    ,500);
            },

            LoadTabHtmlSelection: function()
            {
                $(".tabSelectionOuter").hide();
                setTimeout(function() {
                        customerApp.DelayedTabHtmlSectionLoad();
                    }
                    ,100);
            },

            DelayedTabLibrarySectionLoad: function()
            {
                if ($("#addCardPageLibraryTabForm").length > 0)
                {
                    $("#addCardPageLibraryTabForm").show();
                    $("#card_tab_management_type").val("addLibraryTabAdmin");
                    if (typeof engageSelectLibraryTab === "function")
                    {
                        engageSelectLibraryTab();
                    }
                    setTimeout(function() {
                            let objPopUpBox = $(".universal-float-shield").last().children(".zgpopup-dialog-box");
                            app.AlignPopUp(objPopUpBox);
                        }
                        ,100);
                }
                else
                {
                    setTimeout(function() {
                            customerApp.DelayedTabLibrarySectionLoad();
                        }
                        ,100);
                }
            },

            DelayedTabHtmlSectionLoad: function()
            {
                if ($("#addCardPageForm").length > 0)
                {
                    $("#addCardPageForm").show();
                    $("#card_tab_management_type").val("addCardPageAdmin");
                    setTimeout(function() {
                            let objPopUpBox = $(".universal-float-shield").last().children(".zgpopup-dialog-box");
                            app.AlignPopUp(objPopUpBox);
                        }
                        ,100);
                }
                else
                {
                    setTimeout(function() {
                            customerApp.DelayedTabHtmlSectionLoad();
                        }
                        ,100);
                }
            },

            LoadTabSpecialtySelection: function()
            {
                modal.EngageFloatShield();
                setTimeout(function() {
                        modal.CloseFloatShield();
                    }
                    ,500);
            },

            editAccount: function()
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Card Account";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var intEntityId = $('#dashboard-entity-id').val();
                var strViewRequestParameter = "view=editAccount&card_id=" + intEntityId;

                modal.AssignViewToPopup("/cards/card-data/get-card-dashboard-views", strViewRequestParameter);
            },

            MessageSelectedContactsModal: function()
            {
                let intSelectedCount = 0;
                let strSelectedContacts = "";
                let arContactList = [];

                for(let objContact of this.cardContacts)
                {
                    if (objContact.selected === true)
                    {
                        intSelectedCount++;
                        if (objContact.first_name !== "" && typeof objContact.first_name !== "undefined")
                        {
                            strSelectedContacts += "<li><b>" + objContact.first_name + " " + objContact.last_name + "</b></li>";
                        }
                        else
                        {
                            strSelectedContacts += "<li><b>" + formatAsPhoneIfApplicable(objContact.phone_number) + "</b></li>";
                        }
                        arContactList.push(objContact.id);
                    }
                }

                let strViewRequestParameter = {};
                strViewRequestParameter.contact = arContactList;

                if (intSelectedCount === 0)
                {
                    return;
                }

                $("div[aria-labelledby='btnGroupDrop1']").removeClass("show");

                modal.EngageFloatShield();
                var data = {};

                data.title = "Message [" + intSelectedCount + "] Selected Contacts";

                data.html = "Sending a text message from here will go to the selected contacts connected to this card:<hr style='margin-bottom: 10px;'><ul>" + strSelectedContacts + "</ul>";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var intEntityId = $('#dashboard-entity-id').val();
                strViewRequestParameter.card_id = intEntityId;

                modal.AssignViewToPopup("cards/card-data/message-selected-contacts-modal", strViewRequestParameter, function () {
                        modal.EngageFloatShield();
                    },
                    function (objResult) {

                        let data = {};
                        data.title = "Sending Messages Is Complete!";
                        data.html = "We just sent a text message to the selected contacts on your EZcard.";
                        modal.EngagePopUpAlert(data, function() {
                            modal.CloseFloatShield(function() {
                                modal.CloseFloatShield();
                            },500);
                        }, 350, 115);
                    },
                    function (objValidate) {
                        let intErrors = 0;

                        if ($("#editCardAdminForm .error-text").length > 0) {
                            return false;
                        }

                        $("#editCardAdminForm .error-validation[name!=card_vanity_url][name!=card_keyword]").removeClass("error-validation");

                        for (let intFieldIndex in objValidate) {
                            if (objValidate[intFieldIndex].value == "" && objValidate[intFieldIndex].name != "card_vanity_url" && objValidate[intFieldIndex].name != "card_keyword") {
                                intErrors++;
                                $("#editCardAdminForm input[name=" + objValidate[intFieldIndex].name + "], #editCardAdminForm select[name=" + objValidate[intFieldIndex].name + "]").addClass("error-validation");
                            }
                        }

                        if (intErrors > 0) {
                            console.log("errors: " + intErrors);
                            return false;
                        }

                        return true;
                    }
                );
            },

            MessageContactsModal: function(user) {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Message Contacts";
                data.html = "Sending a text message from here will go to all the contacts connected to this card.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var intEntityId = $('#dashboard-entity-id').val();
                var strViewRequestParameter = "card_id=" + intEntityId;

                modal.AssignViewToPopup("cards/card-data/message-contacts-modal", strViewRequestParameter, function () {
                        modal.EngageFloatShield();
                    },
                    function (objResult) {

                        let data = {};
                        data.title = "Sending Messages Is Complete!";
                        data.html = "We just sent a text message to the contacts on your EZcard.";
                        modal.EngagePopUpAlert(data, function() {
                            modal.CloseFloatShield(function() {
                                modal.CloseFloatShield();
                            },500);
                        }, 350, 115);
                    },
                    function (objValidate) {
                        let intErrors = 0;

                        if ($("#editCardAdminForm .error-text").length > 0) {
                            return false;
                        }

                        $("#editCardAdminForm .error-validation[name!=card_vanity_url][name!=card_keyword]").removeClass("error-validation");

                        for (let intFieldIndex in objValidate) {
                            if (objValidate[intFieldIndex].value == "" && objValidate[intFieldIndex].name != "card_vanity_url" && objValidate[intFieldIndex].name != "card_keyword") {
                                intErrors++;
                                $("#editCardAdminForm input[name=" + objValidate[intFieldIndex].name + "], #editCardAdminForm select[name=" + objValidate[intFieldIndex].name + "]").addClass("error-validation");
                            }
                        }

                        if (intErrors > 0) {
                            console.log("errors: " + intErrors);
                            return false;
                        }

                        return true;
                    }
                );
            },

            messageContactModal: function(contact)
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Message Contact: " + contact.first_name;
                data.html = "Sending a text message from here will go to " + contact.first_name + " " + contact.last_name + " on this card.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var intEntityId = $('#dashboard-entity-id').val();
                var strViewRequestParameter = "card_id=" + intEntityId + "&contact_id=" + contact.id;

                modal.AssignViewToPopup("cards/card-data/message-contact-modal", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {
                        let data = {};
                        data.title = "Sending Message Is Complete!";
                        data.html = "We just sent a text message to "  + contact.first_name  + ".";
                        modal.EngagePopUpAlert(data, function() {
                            modal.CloseFloatShield(function() {
                                modal.CloseFloatShield();
                            },500);
                        }, 350, 115);
                    },
                    function (objValidate)
                    {
                        let intErrors = 0;

                        if ($("#editCardAdminForm .error-text").length > 0)
                        {
                            return false;
                        }

                        $("#editCardAdminForm .error-validation[name!=card_vanity_url][name!=card_keyword]").removeClass("error-validation");

                        for(let intFieldIndex in objValidate)
                        {
                            if (objValidate[intFieldIndex].value == "" && objValidate[intFieldIndex].name != "card_vanity_url" && objValidate[intFieldIndex].name != "card_keyword")
                            {
                                intErrors++;
                                $("#editCardAdminForm input[name=" + objValidate[intFieldIndex].name + "], #editCardAdminForm select[name=" + objValidate[intFieldIndex].name + "]").addClass("error-validation");
                            }
                        }

                        if (intErrors > 0)
                        {
                            console.log("errors: " + intErrors);
                            return false;
                        }

                        return true;
                    }
                );
            },

            editContact: function(contact)
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Contact ";
                data.html = "Manage this subscribed contact.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var intEntityId = $('#dashboard-entity-id').val();
                var strViewRequestParameter = "card_id=" + intEntityId + "&contact_id=" + contact.contact_id;

                modal.AssignViewToPopup("contacts/card-data/message-contacts-modal", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {

                        modal.CloseFloatShield(function() {
                            modal.CloseFloatShield();
                        },500);
                    },
                    function (objValidate)
                    {
                        let intErrors = 0;

                        if ($("#editCardAdminForm .error-text").length > 0)
                        {
                            return false;
                        }

                        $("#editCardAdminForm .error-validation[name!=card_vanity_url][name!=card_keyword]").removeClass("error-validation");

                        for(let intFieldIndex in objValidate)
                        {
                            if (objValidate[intFieldIndex].value == "" && objValidate[intFieldIndex].name != "card_vanity_url" && objValidate[intFieldIndex].name != "card_keyword")
                            {
                                intErrors++;
                                $("#editCardAdminForm input[name=" + objValidate[intFieldIndex].name + "], #editCardAdminForm select[name=" + objValidate[intFieldIndex].name + "]").addClass("error-validation");
                            }
                        }

                        if (intErrors > 0)
                        {
                            console.log("errors: " + intErrors);
                            return false;
                        }

                        return true;
                    }
                );
            },

            viewUser: function(user)
            {
                /*window.location = "account/admin/customers/view-customer?id=" + user.user_id*/
            },

            loadColorPicker: function()
            {
                $('#colorpickerHolder').colpick({
                    color:'ff0000',
                    flat:true,
                    layout:'hex'
                });
            },

            UpdateCardData: function(strStyleLabel, objValue, callback)
            {
                let intEntityId = $('#dashboard-entity-id').val();

                if (!intEntityId)
                {
                    return;
                }

                let strCardUpdateDataParameters = "fieldlabels=" + btoa(strStyleLabel) + "&value=" + btoa(objValue);

                ajax.Post("cards/card-data/update-card-data?id=" + intEntityId + "&type=card-data", strCardUpdateDataParameters, function(objCardResult)
                {
                    if(typeof callback === "function")
                    {
                        callback(objCardResult);
                    }
                });
            },

            ReloadMainImage: function()
            {
                let intEntityId = $('#dashboard-entity-id').val();

                ajax.Post("cards/card-data/get-card-images?id=" + intEntityId + "&type=main-image", null, function(objCardResult)
                {
                    console.log(objCardResult.image);
                    $("#entityMainImage").attr("data-src",objCardResult.image);
                    $("#entityMainImage").attr("src",objCardResult.image);
                });
            }
        },

        data:
        {
            orderKeyCard : 'card_id',
            orderKeyCardPage: 'rel_sort_order',
            orderKeyCardGroup: 'order_number',
            orderKeyCardUser: 'first_name',
            orderKeyCardContact: 'first_name',
            orderKeyCardAffiliate: 'epp_level',
            orderKeyTrans: 'transaction_id',

            sortByTypeCard: true,
            sortByTypeCardPage: true,
            sortByTypeCardGroup: true,
            sortByTypeCardUser: true,
            sortByTypeCardContact: false,
            sortByTypeCardAffiliate: true,
            sortByTypeTrans: true,

            cardColumns: ['role','banner', 'card_name', 'card_num', 'card_vanity_url','product_id', 'sponsor_id', 'card_contacts', 'status', 'last_updated'],
            cardTabColumns: ['title', 'card_tab_type_id', 'visibility','permanent', 'created_on', 'last_updated'],
            cardGroupColumns: ['title', 'card_tab_type_id', 'visibility','permanent', 'created_on', 'last_updated'],
            cardUserColumns: ['role','first_name', 'last_name', 'username'],
            cardContactColumns: ['contact_id', 'first_name', 'last_name', 'phone_number', 'email', 'created_on'],
            cardAffiliateColumns: ['epp_level', 'user_id', 'first_name','last_name', 'affiliate_type', 'status', 'epp_value'],
            transColumns: ['transaction_id', 'created_on', 'card_type', 'card_name', 'card_num'],

            searchCardQuery: '',
            searchCardPageQuery: '',
            searchCardGroupQuery: '',
            searchCardUserQuery: '',
            searchCardContactQuery: '',
            searchCardAffiliateQuery: '',
            searchTransQuery: '',

            pageDisplay: 15,
            cardTabDisplay: 5,
            cardGroupDisplay: 5,
            cardUserDisplay: 5,
            cardContactDisplay: 15,
            cardAffiliateDisplay: 5,
            transDisplay: 5,

            pageTotal: 1,
            cardTabTotal: 1,
            cardGroupTotal: 1,
            cardUserTotal: 1,
            cardContactTotal: 1,
            cardAffiliateTotal: 1,
            transTotal: 1,

            pageIndex: 1,
            cardTabIndex: 1,
            cardGroupIndex: 1,
            cardUserIndex: 1,
            contactPageIndex: 1,
            cardAffiliateIndex: 1,
            transIndex: 1,

            cards: <?php echo $objActiveCards->getData()->ConvertToJavaScriptArray([
                    "card_id",
                    "card_num",
                    "card_name",
                    "owner_id",
                    "user_rel_type_id",
                    "card_vanity_url",
                    "card_type_id",
                    "card_contacts",
                    "status",
                    "sponsor_id",
                    "product_id",
                    "template_id",
                    "main_thumb",
                    "created_on",
                    "last_updated",
                ]) . PHP_EOL; ?>,

            cardTabs: <?php if(!empty($colCardPages)) { echo $colCardPages->ConvertToJavaScriptArray([
                    "card_tab_id",
                    "card_tab_rel_id",
                    "title",
                    "rel_sort_order",
                    "card_tab_type_id",
                    "library_tab",
                    "rel_visibility",
                    "permanent",
                    "created_on",
                    "last_updated",
                ]) . PHP_EOL; } else { echo "[]"; } ?>,

            cardGroups: <?php if(!empty($colCardGroups)) { echo $colCardGroups->ConvertToJavaScriptArray([
                    "card_tab_id",
                    "title",
                    "card_tab_type_id",
                    "visibility",
                    "permanent",
                    "created_on",
                    "last_updated",
                ]) . PHP_EOL; } else { echo "[]"; } ?>,

            cardContacts: <?php if(!empty($colCardContacts)) { echo $colCardContacts->ConvertToJavaScriptArray([
                    "id",
                    "contact_id",
                    "phone_number",
                    "email",
                    "first_name",
                    "last_name",
                    "created_on",
                ]) . PHP_EOL; } else { echo "[]"; } ?>,

            cardConnections: <?php if(!empty($colCardConnections)) { echo $colCardConnections->ConvertToJavaScriptArray([
                    "connection_rel_id",
                    "connection_id",
                    "connection_type_id",
                    "connection_value",
                    "action",
                    "display_order",
                    "is_primary",
                    "status",
                ]) . PHP_EOL; } else { echo "[]"; } ?>,

            cardAffiliates: <?php if(!empty($colCardAffiliates)) { echo $colCardAffiliates->ConvertToJavaScriptArray([
                    "epp_level",
                    "user_id",
                    "first_name",
                    "last_name",
                    "affiliate_type",
                    "status",
                    "epp_value",
                ]) . PHP_EOL; } else { echo "[]"; } ?>,

            cardUsers: <?php if(!empty($colCardUsers)) { echo $colCardUsers->ConvertToJavaScriptArray([
                    "user_id",
                    "username",
                    "status",
                    "first_name",
                    "last_name",
                    "display_name",
                    "role"
                ]) . PHP_EOL; } else { echo "[]"; } ?>,

            transactions: []
        },
        mounted() {
            <?php if ( $strApproach === "view") { echo "this.loadColorPicker();"; } ?>
        }
    });

    dash.processTabDisplay(sessionStorage.getItem('dashboard-tab'));

    window.addEventListener('popstate', function(e) {
        // going back from edit?
        if (e.state == null) {
            $(".formwrapper-outer").removeClass("edit-entity");
            $(".breadCrumbsInner").removeClass("edit-entity");
        }
    });

    document.getElementById("backToViewEntityList").addEventListener("click", function(event){
        event.preventDefault()
        dash.goBackToEntityList("/account/cards");
    });

    document.getElementById("back-to-entity-list").addEventListener("click", function(event){
        event.preventDefault()
        dash.goBackToEntityList("/account/cards");
    });

</script>

