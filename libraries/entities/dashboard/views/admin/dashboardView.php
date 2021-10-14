<?php

use App\Website\Website;
/** @var Website $this */

$this->CurrentPage->BodyId            = "dashboard-page";
$this->CurrentPage->BodyClasses       = ["admin-page", "dashboard-page", "two-columns", "left-side-column"];
$this->CurrentPage->Meta->Title       = "My ".$this->app->objCustomPlatform->getPortalName()." Dashboard | " . $this->app->objCustomPlatform->getPortalDomain();
$this->CurrentPage->Meta->Description = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Meta->Keywords    = "";
$this->CurrentPage->SnipIt->Title     = "My ".$this->app->objCustomPlatform->getPortalName()." Dashboard";
$this->CurrentPage->SnipIt->Excerpt   = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Columns           = 0;

$this->LoadVenderForPageScripts($this->CurrentPage->BodyId, ["swiper"=>"main/v6.4.5"]);
$this->LoadVendorForPageStyles($this->CurrentPage->BodyId, ["swiper"=>"main/v6.4.5"]);

?>
<div class="BodyContentBox">
    <div id="<?php echo $this->VueApp->getAppId(); ?>-content" class="formwrapper" >
        <?php echo $this->VueApp->renderAppHtml(); ?>
    </div>
</div>
<script type="application/javascript">
    Vue.config.devtools = true;
</script>