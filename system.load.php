<?php
/**
 * SHELL _site_core Extention for zgWeb.Solutions Web.CMS.App
 */

const App = true;
const Appversion = "2.0.0";
const XT = ".php";

// Autoload Class
require __DIR__ . "/core/auto.load" . XT;

// Load Custom Functions
require __DIR__ . "/libraries/custom.functions" . XT;


// App Class Instantiation
return require __DIR__ . "/core/app.service" . XT;
