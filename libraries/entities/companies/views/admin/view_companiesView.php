<?php
/**
 * Created by PhpStorm.
 * User: Micah.Zak
 * Date: 10/11/2018
 * Time: 9:43 AM
 */

$this->CurrentPage->BodyId            = "view-all-companies-page";
$this->CurrentPage->BodyClasses       = ["admin-page", "view-all-companies-page", "no-columns"];
$this->CurrentPage->Meta->Title       = "Companies | Admin | " . $this->app->objCustomPlatform->getPortalName();
$this->CurrentPage->Meta->Description = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Meta->Keywords    = "";
$this->CurrentPage->SnipIt->Title     = "Companies";
$this->CurrentPage->SnipIt->Excerpt   = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Columns           = 0;

?>
<div class="breadCrumbs">
    <div class="breadCrumbsInner">
        <a href="/account/dashboard/" class="breadCrumbHomeImageLink">
            <img src="/media/images/home-icon-01_white.png" class="breadCrumbHomeImage" width="15" height="15" />
        </a> &#187;
        <a href="/account/dashboard/" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Home</span>
        </a> &#187;
        <a href="/account/dashboard/" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Admin</span>
        </a> &#187;
        <span class="breadCrumbPage">Companies</span>
    </div>
</div>
<div class="BodyContentBox">
</div>


