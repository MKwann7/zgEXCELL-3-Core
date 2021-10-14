<?php

//$this->LoadVenderForPageScripts("public-cart", "slim");
//$this->LoadVendorForPageStyles("public-cart", "slim");

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
    <title>Custom Platform Configuration | <?php echo $this->app->objCustomPlatform->getPortalName(); ?></title>
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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="/widgets/css/cart.css">
    <script type="text/javascript" src="/widgets/js/platform.js" ></script>
</head>
<body id="dashboard" class="admin-page library-tabs-page no-columns left-side-column">
<div id="app" style="max-width:90%;margin:25px auto 0;padding:25px;">
    <div id="customPlatformManager"></div>
</div>
<script type="application/javascript">

    let vueApp = new Vue({el: '#app'});

    Vue.component('v-style', {
        render: function (createElement) {
            return createElement('style', this.$slots.default)
        }
    })

    $(document).ready(function ()
    {
        customPlatformManager("<?php echo $this->app->objCustomPlatform->getCompany()->sys_row_id; ?>", "customPlatformManager");
    });

</script>
</body>
</html>
