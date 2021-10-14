<?php

use Entities\Cards\Components\Vue\CardWidget\ManageCardPageWidget;

use App\Website\Website;
/** @var Website $this */

$this->CurrentPage->BodyId            = "view-marketplace-page";
$this->CurrentPage->BodyClasses       = ["admin-page", "view-marketplace-page", "no-columns"];
$this->CurrentPage->Meta->Title       = "Marketplace | Admin | " . $this->app->objCustomPlatform->getPortalName();
$this->CurrentPage->Meta->Description = "";
$this->CurrentPage->Meta->Keywords    = "";
$this->CurrentPage->SnipIt->Title     = "Marketplace";
$this->CurrentPage->SnipIt->Excerpt   = "";
$this->CurrentPage->Columns           = 0;

$this->LoadVenderForPageScripts($this->CurrentPage->BodyId, "froala");
$this->LoadVenderForPageScripts($this->CurrentPage->BodyId, "colorpicker");
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
