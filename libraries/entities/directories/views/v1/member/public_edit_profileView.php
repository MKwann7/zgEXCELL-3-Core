<?php

$this->LoadVenderForPageScripts("manage-member-directory-record-publicly", "slim");
$this->LoadVendorForPageStyles("manage-member-directory-record-publicly", "slim");

?>
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
    <title>Manage Your Member Profile | EZ Digital</title>
    <meta data-rh="true" property="og:title" content="Manage Your Member Profile | EZ Digital" />
    <meta data-rh="true" property="og:description" content="Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!" />
    <meta name="description" content="Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!" />
    <meta name="google-site-verification" content="uPqvMg0seV_kjFqKgLX2fHM9kOgE_ihwVUCC2et9-3Q" />
    <meta name="keywords" content="" />
    <meta charset="utf-8">
    <meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta data-rh="true" property="og:title" content="Manage Your Member Profile | EZ Digital" />
    <meta data-rh="true" property="og:description" content="Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!" />
    <meta name="description" content="Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!" />
    <meta name="google-site-verification" content="uPqvMg0seV_kjFqKgLX2fHM9kOgE_ihwVUCC2et9-3Q" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="/website/css/style.css">
    <link rel="stylesheet" href="/portal/css/libraries.css?page=manage-member-directory-record-publicly&_zg4347">
    <link rel="stylesheet" href="/portal/css/core.css?page=manage-member-directory-record-publicly&_zg8169">
    <script type="application/javascript" src="/portal/js/libraries.js?page=manage-member-directory-record-publicly&_zg9037"></script>
    <script type="text/javascript" src="/portal/js/core.js?page=manage-member-directory-record-publicly&_zg1874" ></script>
    <script type="text/javascript" src="/website/js/application.js?_zg8172&__=widget" ></script>
</head>
<body id="dashboard" class="admin-page library-tabs-page no-columns left-side-column">
<div id="app" style="max-width:1250px;margin:0 auto;padding:25px;">
    <div id="this-is-an-id" ref="testIdThing"></div>
</div>
    <script type="application/javascript">
        let vueApp = new Vue({
            el: '#app',
        });
        $(document).ready(function() {
            const widget = new WidgetLoader(
                "4753fc48-e147-2f73-d174-c659179dd294",
                vueApp,
                document.getElementById("this-is-an-id"),
                <?php echo $memberData->ConvertToJavaScriptArrayElement(); ?>,
                {directoryId: '<?php echo $directoryId; ?>', public: true}
            );

            widget.runChild("editMemberComponent", "edit");
        });
    </script>
</body>
</html>
