<?php

use Entities\Cards\Classes\Cards;
use Entities\Packages\Classes\PackageLineSettings; ?>
<!DOCTYPE html>
<!--
//============================================================================
// EZcard V2 CRM System
// Built on the Excell Web Framework
// Copyright <c> 2019. All rights reserved.
//============================================================================
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Shopping Cart | <?php echo $this->app->objCustomPlatform->getPortalName(); ?></title>
    <meta data-rh="true" property="og:title" content="Shopping Cart | <?php echo $this->app->objCustomPlatform->getPortalName(); ?>" />
    <meta data-rh="true" property="og:description" content="Welcome to the NEW AMAZING WORLD of <?php echo $this->app->objCustomPlatform->getPortalName(); ?> Cards, where you can create and manage your own cards!" />
    <meta name="description" content="Welcome to the NEW AMAZING WORLD of <?php echo $this->app->objCustomPlatform->getPortalName(); ?> Cards, where you can create and manage your own cards!" />
    <meta name="google-site-verification" content="uPqvMg0seV_kjFqKgLX2fHM9kOgE_ihwVUCC2et9-3Q" />
    <meta name="keywords" content="" />
    <meta charset="utf-8">
    <meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta data-rh="true" property="og:title" content="Shopping Cart | <?php echo $this->app->objCustomPlatform->getPortalName(); ?>" />
    <meta data-rh="true" property="og:description" content="Welcome to the NEW AMAZING WORLD of <?php echo $this->app->objCustomPlatform->getPortalName(); ?>, where you can create and manage your own cards!" />
    <meta name="description" content="Welcome to the NEW AMAZING WORLD of <?php echo $this->app->objCustomPlatform->getPortalName(); ?> Cards, where you can create and manage your own cards!" />
    <meta name="google-site-verification" content="uPqvMg0seV_kjFqKgLX2fHM9kOgE_ihwVUCC2et9-3Q" />
    <meta name="keywords" content="" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
    <link rel="stylesheet" href="/_ez/templates/2/css/template.min.css?card_id=0" />
    <link rel="stylesheet" href="/default/css/default.css">
    <link rel="stylesheet" href="/portal/template/1/css/style.css">
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="/_ez/templates/2/js/template.min.js?card_id=0"></script>
    <style type="text/css">
        .cart-display-box {
            padding: 0px 10px;
        }
        .appCartWrapper.products .cart-display-box {
            padding: 0px 10px 0 45px;
        }
        .account-page-title {
            line-height: 1.5;
        }
        #dashboard .create-new-customer-wrapper > p {
            margin-top: 0 !important;
            line-height: 1.8;
            text-align: center;
        }
    </style>
</head>
<body id="dashboard" class="admin-page library-tabs-page no-columns left-side-column">
    <div id="app-wrapper" style="max-width:100%;">
        <div id="app-vue" class="app-cart"></div>
    </div>
    <script type="text/javascript">
        let vueApp = new Vue({
            el: '#app',
        });
        document.addEventListener("DOMContentLoaded", function() {
            const widget = new WidgetLoader(
                "f878dc91-2ed7-4252-b5d8-ac25e92dabb8",
                vueApp,
                document.getElementById("app-vue"),
                null,
                {public: true}
            );

            widget.runMain("view", function (component) {
                component.instance.selectPackagesByClass("card", true);
            });
        });
    </script>
</body>
</html>
