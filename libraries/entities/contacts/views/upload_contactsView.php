<?php
/**
 * Created by PhpStorm.
 * User: Micah.Zak
 * Date: 10/11/2018
 * Time: 9:43 AM
 */

$this->CurrentPage->BodyId            = "upload-contacts-page";
$this->CurrentPage->BodyClasses       = ["admin-page", "upload-contacts-page", "two-columns", "left-side-column"];
$this->CurrentPage->Meta->Title       = "Upload Contacts | " . $this->app->objCustomPlatform->getPortalDomainName();
$this->CurrentPage->Meta->Description = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Meta->Keywords    = "";
$this->CurrentPage->SnipIt->Title     = "Upload Contacts Groups";
$this->CurrentPage->SnipIt->Excerpt   = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Columns           = 2;

?>
<div class="breadCrumbs">
    <div class="breadCrumbsInner">
        <a href="/account" class="breadCrumbHomeImageLink">
            <img src="/media/images/home-icon-01_white.png" class="breadCrumbHomeImage" width="15" height="15" />
        </a> &#187;
        <a href="/account" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Home</span>
        </a> &#187;
        <a href="/account" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Contacts</span>
        </a> &#187;
        <span id="view-list">
            <span class="breadCrumbPage">Upload Contacts</span>
        </span>
    </div>
</div>
<?php $this->RenderPortalComponent("content-left-menu"); ?>
<div class="BodyContentBox">
    <style type="text/css">
        .BodyContentBox .entityList.table-striped td:nth-child(3),
        .BodyContentBox .entityList.table-striped td:nth-child(5) {
            width:8%;
        }

        .BodyContentBox .entityList.table-striped td {
            width:10%;
        }
        .BodyContentBox .entityList.table-striped td:first-child {
            width:7%;
        }
        .BodyContentBox .entityList.table-striped td:nth-child(6) {
            width:5%;
        }

        /*[v-cloak] { display: none; }*/
    </style>
    <div id="app" class="formwrapper" >
        <div class="formwrapper-outer<?php if ( $strApproach === "view") { echo " edit-entity"; } ?>">
            <div class="formwrapper-control" v-cloak>
                <div class="fformwrapper-header">
                    <table class="table header-table" style="margin-bottom:0px;">
                        <tbody>
                        <tr>
                            <td>
                                <h3 class="account-page-title">Upload Contacts</h3>
                            </td>
                            <td class="text-right page-count-display" style="vertical-align: middle;">

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>

