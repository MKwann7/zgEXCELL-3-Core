<?php
/**
 * SHELL _site_core Extention for zgWeb.Solutions Web.CMS.App
 */

define("App", true);
define("Appversion", "2.0.0");
define("XT", ".php");

// Autoload Class
require AppCore . "engine/core/auto.load" . XT;

// Load Custom Functions
require AppLibraries . "custom.functions" . XT;


// App Class Instantiation
return require AppCore . "engine/core/app.service" . XT;
