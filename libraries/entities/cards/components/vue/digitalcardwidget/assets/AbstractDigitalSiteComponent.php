<?php

namespace Entities\Cards\Components\Vue\DigitalCardWidget\Assets;

use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;

abstract class AbstractDigitalSiteComponent extends VueComponent
{
    protected string $title = "Digital Card";
    protected string $endpointUriAbstract = "{card_num}";
    protected string $cssPrefix = ".app-template-X";
    protected DigitalPageComponent $cardPage;

    public function __construct(?AppModel $entity = null)
    {
        parent::__construct($entity);

        $this->cardPage->setMountType("no_mount");
        $this->cardPage->addParentId($this->getInstanceId(), ["edit"]);

        $this->addComponentsList($this->cardPage->getDynamicComponentsForParent());
        $this->addComponent($this->cardPage, true);
    }

    public function getCardPage() : DigitalPageComponent
    {
        return $this->cardPage;
    }

    protected function renderComponentMountedScript(): string
    {
        return '
            dispatch.register("screen_resize", this, "screenSizeUpdate")
            dispatch.register("reload_active_page_title", this, "reloadActivePageTitle");
            dispatch.register("reload_active_page_widget", this, "reloadActivePageWidget");
            dispatch.register("reload_site_profile_in_editor", this, "reloadCardProfile");
            dispatch.register("reload_site_settings", this, "reloadSiteConfiguration");
            dispatch.register("reload_site_media", this, "reloadSiteMedia");
            dispatch.register("reload_site_logos", this, "reloadSiteLogos");
            dispatch.register("move_into_portal", this, "moveIntoPortal")
            dispatch.register("log_member_out", this, "logMemberOut")
        ';
    }

    protected function renderMobileCss(): string
    {
        return $this->renderResponsiveCss($this->getMobileCss(),["320", "400", "480", "568", "640"]).
            $this->renderResponsiveCss($this->getTabletCss(),["768","850", "1024"]);
    }
    
    protected function renderResponsiveCss(array $cssArray, array $widths): string
    {
        $returnCss = "";
        $widths = array_reverse($widths);
        foreach($cssArray as $currCssClasses => $currCssValues) {
            foreach($widths as $currWidths) {
                $cssClasses = explode(",", $currCssClasses);
                foreach($cssClasses as $currCssClass) {
                    $returnCss .= ".media-hub-" .$currWidths." " .$this->cssPrefix." ".str_replace(["\n"], "",trim($currCssClass)) ."  {".$currCssValues."}" . PHP_EOL;
                }
            }
        }

        return $returnCss;
    }

    protected function cssPrefixClass(): string
    {
        return str_replace(".","", $this->cssPrefix);
    }
}