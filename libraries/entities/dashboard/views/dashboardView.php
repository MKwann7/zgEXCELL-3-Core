<?php

$this->CurrentPage->BodyId            = "dashboard-page";
$this->CurrentPage->BodyClasses       = ["admin-page", "dashboard-page", "two-columns", "left-side-column"];
$this->CurrentPage->Meta->Title       = "My ".$this->app->objCustomPlatform->getPortalName()." Dashboard | " . $this->app->objCustomPlatform->getPortalDomainName();
$this->CurrentPage->Meta->Description = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Meta->Keywords    = "";
$this->CurrentPage->SnipIt->Title     = "My ".$this->app->objCustomPlatform->getPortalName()." Dashboard";
$this->CurrentPage->SnipIt->Excerpt   = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Columns           = 0;

?>
<div class="breadCrumbs">
    <div class="breadCrumbsInner">
        <a href="/account/" class="breadCrumbHomeImageLink">
            <img src="/media/images/home-icon-01_white.png" class="breadCrumbHomeImage" width="15" height="15" />
        </a> &#187;
        <a href="/account" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Home</span>
        </a> &#187;
        <span class="breadCrumbPage">Dashboard</span>
    </div>
</div>
<?php $this->RenderPortalComponent("content-left-menu"); ?>
<div class="BodyContentBox">
    <div id="app" class="formwrapper" >
        <div class="formwrapper-outer">
            <div class="formwrapper-control">
                <div class="fformwrapper-header">
                    <table class="table header-table" style="margin-bottom:0px;">
                        <tbody>
                        <tr>
                            <td>
                                <h3 class="account-page-title">My Dashboard</h3>
                            </td>
                            <td class="text-right page-count-display" style="vertical-align: middle;">

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="main-body-wrapper" style="padding:5px 15px;">
                    <?php include APP_ENTITIES . "dashboard/views/partials/dashboard.partialView" . XT; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->SetPageStyle($this->CurrentPage->BodyId, "
.embed-container {
    position: relative;
    padding-bottom: 56.25%;
    overflow: hidden;
}

.embed-container iframe,
.embed-container object,
.embed-container embed {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
@media (max-width:750px) {
    .BodyNavigationBox {
        display: block !important;
        width: 100% !important;
    }
    .BodyContentBox {
        display:none !important;
    }
}
"); ?>
