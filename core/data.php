<?php
/**
 * SHELL _site_core Extention for zgWeb.Solutions Web.CMS.App
 */

$arJavaScriptLibraries = [
    "vendor" => array(
        "jquery" => array(
            "main/v3.2.1" => ["jquery.min.js"],
            "ui/v1.12.1" => ["jquery.ui.min.js"],
            "form/v3.51.0" => ["jquery.form.min.js"],
        ),
        "lodash" => array(
            "main/v4.17.11" => ["lodash.min.js"],
        ),
        "modernizr" => array(
            "main/v2.6.2" => ["modernizer.min.js"]
        ),
        "vue" => array(
            "main/v2.5.17" => ["vue.min.js"],
            "slicksort/v1.1.3" => ["vue-slicksort.min.js"]
        ),
        "froala" => array(
            "main/v2.9.3" => ["froala_editor.pkdg.min.js", "froala_editor.pkgd.min.css", "froala_style.min.css"],
        )
    )
];


$arCssLibraries = [
    "vendor" => array(
        "froala" => array(
            "main/v2.9.3" => ["froala_editor.pkgd.min.css", "froala_style.min.css"],
        )
    )
];


echo json_encode($arCssLibraries);
die;