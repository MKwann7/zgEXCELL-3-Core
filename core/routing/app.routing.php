<?php
/**
 * ENGINECORE Configuration File for zgWeb.Solutions Web.CMS.App
 */

// Binding Custom Routing For Modules
$this->objAppEntities["users"]["Routes"]["customers"]["Alias"] = "customers";
$this->objAppEntities["users"]["Routes"]["brand-partners"]["Alias"] = "brand-partners";
$this->objAppEntities["users"]["Routes"]["affiliates"]["Alias"] = "affiliates";
$this->objAppEntities["users"]["Routes"]["profile"]["Alias"] = "profile";
$this->objAppEntities["users"]["Routes"]["personas"]["Alias"] = "personas";
$this->objAppEntities["users"]["Routes"]["my-personas"]["Alias"] = "my-personas";
$this->objAppEntities["cards"]["Routes"]["my-groups"]["Alias"] = "my-groups";
$this->objAppEntities["directories"]["Routes"]["max-directories"]["Alias"] = "max-directories";
$this->objAppEntities["companies"]["Routes"]["platforms"]["Alias"] = "platforms";
$this->objAppEntities["sharesave"]["Routes"]["share-save"]["Alias"] = "share-save";

//dd($this->objAppEntities["companies"]);

// New Custom Aliases for Modules
$this->objAppEntities["personas"] = $this->objAppEntities["users"];
$this->objAppEntities["my-personas"] = $this->objAppEntities["users"];
$this->objAppEntities["my-groups"] = $this->objAppEntities["cards"];
$this->objAppEntities["max-directories"] = $this->objAppEntities["directories"];
$this->objAppEntities["profile"] = $this->objAppEntities["users"];
$this->objAppEntities["brand-partners"] = $this->objAppEntities["users"];
$this->objAppEntities["members"] = $this->objAppEntities["users"];
$this->objAppEntities["customers"] = $this->objAppEntities["users"];
$this->objAppEntities["platforms"] = $this->objAppEntities["companies"];
$this->objAppEntities["marketplace"] = $this->objAppEntities["cart"];
$this->objAppEntities["marketplace-products"] = $this->objAppEntities["products"];
$this->objAppEntities["contact-me"] = $this->objAppEntities["contactme"];
$this->objAppEntities["share-save"] = $this->objAppEntities["sharesave"];
$this->objAppEntities["talk-to-me"] = $this->objAppEntities["talktome"];

// Custom URL Binding For Modules
$this->objAppEntities["cards"]["ControllerRouting"]["Index"]["binding"] = array_merge(["##root"], $this->lstPortalBindings);

$this->objAppEntities["users"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];
$this->objAppEntities["cards"]["ControllerRouting"]["ReportIssue"]["binding"] = ["account"];
$this->objAppEntities["cards"]["ControllerRouting"]["CardGroups"]["binding"] = ["account/admin"];
$this->objAppEntities["cards"]["ControllerRouting"]["CardRelationships"]["binding"] = ["account/admin"];
$this->objAppEntities["cards"]["ControllerRouting"]["CardRelationshipTypes"]["binding"] = ["account/admin"];
$this->objAppEntities["cards"]["ControllerRouting"]["CardTemplates"]["binding"] = ["account/admin"];
$this->objAppEntities["cards"]["ControllerRouting"]["CardTypes"]["binding"] = ["account/admin"];

$this->objAppEntities["directories"]["ControllerRouting"]["Index"]["binding"] = $this->lstPortalBindings;;
$this->objAppEntities["modules"]["ControllerRouting"]["Index"]["binding"] = $this->lstPortalBindings;
$this->objAppEntities["communication"]["ControllerRouting"]["Index"]["binding"] = $this->lstPortalBindings;
$this->objAppEntities["notes"]["ControllerRouting"]["Index"]["binding"] = $this->lstPortalBindings;
$this->objAppEntities["tickets"]["ControllerRouting"]["Index"]["binding"] = $this->lstPortalBindings;
$this->objAppEntities["contacts"]["ControllerRouting"]["Index"]["binding"] = $this->lstPortalBindings;
$this->objAppEntities["packages"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];
$this->objAppEntities["reports"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];

$this->objAppEntities["customers"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];
$this->objAppEntities["members"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];
$this->objAppEntities["personas"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];
$this->objAppEntities["my-personas"]["ControllerRouting"]["Index"]["binding"] = ["account"];
$this->objAppEntities["my-groups"]["ControllerRouting"]["Index"]["binding"] = ["account"];
$this->objAppEntities["max-groups"]["ControllerRouting"]["Index"]["binding"] = ["account"];
$this->objAppEntities["max-directories"]["ControllerRouting"]["Index"]["binding"] = ["account"];
$this->objAppEntities["profile"]["ControllerRouting"]["Index"]["binding"] = ["account"];
$this->objAppEntities["marketplace"]["ControllerRouting"]["Index"]["binding"] = ["account"];
$this->objAppEntities["marketplace-products"]["ControllerRouting"]["Index"]["binding"] = ["account"];
$this->objAppEntities["settings"]["ControllerRouting"]["Index"]["binding"] = ["account"];
$this->objAppEntities["dashboard"]["ControllerRouting"]["Index"]["binding"] = ["##account"];
$this->objAppEntities["companies"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];
$this->objAppEntities["sharesave"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];
$this->objAppEntities["share-save"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];
$this->objAppEntities["contactme"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];
$this->objAppEntities["talktome"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];
$this->objAppEntities["tasks"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];

$this->objAppEntities["platforms"]["ControllerRouting"]["Index"]["binding"] = ["account/admin"];