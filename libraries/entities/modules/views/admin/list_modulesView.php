<?php

use App\Website\Website;
/** @var Website $this */

$this->CurrentPage->BodyId            = "view-modules-admin-page";
$this->CurrentPage->BodyClasses       = ["admin-page", "view-modules-admin-page", "no-columns"];
$this->CurrentPage->Meta->Title       = "Modules | Admin | " . $this->app->objCustomPlatform->getPortalName();
$this->CurrentPage->Meta->Description = "Welcome to the NEW AMAZING WORLD of digital cards, where you can create and manage your own cards!";
$this->CurrentPage->Meta->Keywords    = "";
$this->CurrentPage->SnipIt->Title     = "Modules";
$this->CurrentPage->SnipIt->Excerpt   = "Welcome to the NEW AMAZING WORLD of digital cards, where you can create and manage your own cards!";
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
<div class="BodyContentBox">
    <div id="<?php echo $this->VueApp->getAppId(); ?>-content" class="formwrapper" >
        <?php echo $this->VueApp->renderAppHtml(); ?>
    </div>
</div>
<script type="application/javascript">
    Vue.config.devtools = true;
</script>