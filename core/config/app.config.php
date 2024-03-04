<?php
/**
 * ENGINECORE Configuration File for zgWeb.Solutions Web.CMS.App
 */

use App\Core\App;
/** @var App $this */

/*
 * Set Custom ApplicationJs Information
 */
$this->blnModuleCache = env("MODULE_CASH");
$this->blnWidgetCache = env("WIDGET_CASH");
$this->objCoreData["Website"]["ShowMetaTitleName"] = true;