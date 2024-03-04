<?php
/**
 * Created by PhpStorm.
 * User: Micah.Zak
 * Date: 10/11/2018
 * Time: 9:43 AM
 */

$this->CurrentPage->BodyId            = "view-all-customers-new-page";
$this->CurrentPage->BodyClasses       = ["admin-page", "view-all-customers-new-page", "no-columns"];
$this->CurrentPage->Meta->Title       = "My Profile | " . $this->app->objCustomPlatform->getPortalName();
$this->CurrentPage->Meta->Description = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Meta->Keywords    = "";
$this->CurrentPage->SnipIt->Title     = "My Profile";
$this->CurrentPage->SnipIt->Excerpt   = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Columns           = 0;

$this->LoadVenderForPageScripts($this->CurrentPage->BodyId, "froala");
$this->LoadVendorForPageStyles($this->CurrentPage->BodyId, "froala");
$this->LoadVenderForPageScripts($this->CurrentPage->BodyId, "slim");
$this->LoadVendorForPageStyles($this->CurrentPage->BodyId, "slim");
$this->LoadVenderForPageScripts($this->CurrentPage->BodyId, ["jquery"=>"input-picker/v1.0"]);
$this->LoadVendorForPageStyles($this->CurrentPage->BodyId, ["jquery"=>"input-picker/v1.0"]);

?>
<div class="BodyContentBox">
    <style type="text/css">
        .vueAppWrapper .entityList.table-striped td:nth-child(4),
        .vueAppWrapper .entityList.table-striped td:nth-child(6) {
            width:8%;
        }

        .vueAppWrapper .entityList.table-striped td:nth-child(2) {
            width:3%;
        }

        .vueAppWrapper .entityList.table-striped td {
            width:10%;
        }
        .vueAppWrapper .entityList.table-striped td:first-child {
            width:4%;
        }
        .vueAppWrapper .entityList.table-striped td:nth-child(7) {
            width:6%;
        }
        .vueAppWrapper .entityList.table-striped td:nth-child(9),
        .vueAppWrapper .entityList.table-striped td:nth-child(10) {
            width:6%;
        }
        .card-main-color-block {
            width:80px;height:160px;cursor:pointer;
        }
        .vueAppWrapper .card-users_role {
            width:10%;
        }
        .vueAppWrapper .card-users_first_name {
            width:12%;
        }
        .vueAppWrapper .card-users_last_name {
            width:12%;
        }
        .vueAppWrapper .contacts_phone {
            width:8%;
        }
        .vueAppWrapper .contacts_email {
            width:15%;
        }
        .vueAppWrapper .contacts_first_name {
            width:15%;
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

        .vueAppWrapper .account-page-title #back-to-entity-list,
        .vueAppWrapper .account-page-title #back-to-entity-list-404 {
            background: #cc0000 url(/website/images/mobile-back.png) center center / auto 75% no-repeat !important;
            text-indent: -99999px;
            padding: 5px 0px !important;
            width: 24px;
            height: 23px;
            display: inline-block;
            top: 2px;
            position: relative;
            border-radius: 5px;
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

        .tab_color_edit_tool {
            position: relative;display: inline-block;width: 20px;height: 20px;top: 6px;
            margin-right: 3px;
            margin-left: -4px;
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
    <div id="<?php echo $this->VueApp->getAppId(); ?>-content" class="formwrapper" >
        <?php echo $this->VueApp->renderAppHtml(); ?>
    </div>
</div>
<script type="application/javascript">
    Vue.config.devtools = true;
</script>
