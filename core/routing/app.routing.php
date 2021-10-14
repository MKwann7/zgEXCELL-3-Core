<?php
/**
 * ENGINECORE Configuration File for zgWeb.Solutions Web.CMS.App
 */

// Binding Custom Routing For Modules
$this->objAppEntities["users"]["Routes"]["customers"]["Alias"] = "customers";
$this->objAppEntities["users"]["Routes"]["brand-partners"]["Alias"] = "brand-partners";
$this->objAppEntities["users"]["Routes"]["affiliates"]["Alias"] = "affiliates";
$this->objAppEntities["users"]["Routes"]["profile"]["Alias"] = "profile";
$this->objAppEntities["companies"]["Routes"]["platforms"]["Alias"] = "platforms";

//dd($this->objAppEntities["companies"]);

// New Custom Aliases for Modules
$this->objAppEntities["profile"] = $this->objAppEntities["users"];
$this->objAppEntities["brand-partners"] = $this->objAppEntities["users"];
$this->objAppEntities["members"] = $this->objAppEntities["users"];
$this->objAppEntities["customers"] = $this->objAppEntities["users"];
$this->objAppEntities["platforms"] = $this->objAppEntities["companies"];
$this->objAppEntities["marketplace"] = $this->objAppEntities["cart"];
$this->objAppEntities["marketplace-products"] = $this->objAppEntities["products"];

// Custom URL Binding For Modules
$this->objAppEntities["cards"]["ControllerRouting"]["Index"]["binding"] = array_merge(["##root"], $this->lstPortalBindings);

$this->objAppEntities["users"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];
$this->objAppEntities["cards"]["ControllerRouting"]["ReportIssue"]["binding"] = ["account"];
$this->objAppEntities["cards"]["ControllerRouting"]["CardGroups"]["binding"] = ["account/admin"];
$this->objAppEntities["cards"]["ControllerRouting"]["CardRelationships"]["binding"] = ["account/admin"];
$this->objAppEntities["cards"]["ControllerRouting"]["CardRelationshipTypes"]["binding"] = ["account/admin"];
$this->objAppEntities["cards"]["ControllerRouting"]["CardTemplates"]["binding"] = ["account/admin"];
$this->objAppEntities["cards"]["ControllerRouting"]["CardTypes"]["binding"] = ["account/admin"];

$this->objAppEntities["modules"]["ControllerRouting"]["Index"]["binding"] = $this->lstPortalBindings;
$this->objAppEntities["communication"]["ControllerRouting"]["Index"]["binding"] = $this->lstPortalBindings;
$this->objAppEntities["notes"]["ControllerRouting"]["Index"]["binding"] = $this->lstPortalBindings;
$this->objAppEntities["tickets"]["ControllerRouting"]["Index"]["binding"] = $this->lstPortalBindings;
$this->objAppEntities["contacts"]["ControllerRouting"]["Index"]["binding"] = $this->lstPortalBindings;
$this->objAppEntities["packages"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];
$this->objAppEntities["reports"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];

$this->objAppEntities["customers"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];
$this->objAppEntities["members"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];
$this->objAppEntities["profile"]["ControllerRouting"]["Index"]["binding"] = ["account"];
$this->objAppEntities["marketplace"]["ControllerRouting"]["Index"]["binding"] = ["account"];
$this->objAppEntities["marketplace-products"]["ControllerRouting"]["Index"]["binding"] = ["account"];
$this->objAppEntities["settings"]["ControllerRouting"]["Index"]["binding"] = ["account"];
$this->objAppEntities["dashboard"]["ControllerRouting"]["Index"]["binding"] = ["##account"];
$this->objAppEntities["companies"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];
$this->objAppEntities["tasks"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];

$this->objAppEntities["platforms"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];