<?php
?>
<head>
    <title>My Hub</title>

    <meta charset="utf-8">
    <meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta name="author" content="<?php echo $this->app->objCustomPlatform->getPublicName(); ?>" />
    <meta name="description" content="Digital Business Card" />
    <meta property="og:title" content="My Hub" />
    <meta property="og:site_name" content="<?php echo $this->app->objCustomPlatform->getPublicName(); ?>" />

    <meta name="google-site-verification" content="" />
    <meta name="Copyright" content="EZ Digital, LLC" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
    <link rel="stylesheet" href="/default/css/default.css" />
    <link rel="stylesheet" href="/app/css/template.min.css?version=<?php echo env("CSS_VERSION_APPTEMPLATE"); ?>" />
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="/app/js/template.min.js?version=<?php echo env("JS_VERSION_APPTEMPLATE"); ?>"></script>
</head>
<body class="cp_<?php echo $this->app->objCustomPlatform->getCompanyId(); ?> theme_shade_light handed-left digital-app">
<div class="app-wrapper">
    <div class="app-wrapper-inner">
        <div id="app-vue" class="app-card">
        </div>
    </div>
</div>

<script type="text/javascript">
    let vueApp = new Vue({
        el: '#app',
    });

    document.addEventListener("DOMContentLoaded", function() {
        const widget = new WidgetLoader(
            "122160fe-9981-4d3d-8218-fabdd279713a",
            vueApp,
            document.getElementById("app-vue"),
            null,
            {public: true}
        );

        widget.runMain("view");
    });
</script>
</body>
</html>
