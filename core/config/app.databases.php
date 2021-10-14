<?php

return [
    "Main"=> [
        "Host" => env("MAIN_DB_HOST"),
        "Database" => env("MAIN_DB_NAME"),
        "Port" => "",
        "Username" => env("MAIN_DB_USER"),
        "Password" => env("MAIN_DB_PASS")
    ],
    "Media"=> [
        "Host" => env("MEDIA_DB_HOST"),
        "Database" => env("MEDIA_DB_NAME"),
        "Port" => "",
        "Username" => env("MEDIA_DB_USER"),
        "Password" => env("MEDIA_DB_PASS")
    ],
    "Modules"=> [
        "Host" => env("MODULES_DB_HOST"),
        "Database" => env("MODULES_DB_NAME"),
        "Port" => "",
        "Username" => env("MODULES_DB_USER"),
        "Password" => env("MODULES_DB_PASS")
    ],
    "Apps"=> [
        "Host" => env("WIDGETS_DB_HOST"),
        "Database" => env("WIDGETS_DB_NAME"),
        "Port" => "",
        "Username" => env("WIDGETS_DB_USER"),
        "Password" => env("WIDGETS_DB_PASS")
    ],
    "Communication"=> [
        "Host" => env("COMM_DB_HOST"),
        "Database" => env("COMM_DB_NAME"),
        "Port" => "",
        "Username" => env("COMM_DB_USER"),
        "Password" => env("COMM_DB_PASS")
    ],
    "Crm"=> [
        "Host" => env("CRM_DB_HOST"),
        "Database" => env("CRM_DB_NAME"),
        "Port" => "",
        "Username" => env("CRM_DB_USER"),
        "Password" => env("CRM_DB_PASS")
    ],
    "Activity"=> [
        "Host" => env("ACTIVITY_DB_HOST"),
        "Database" => env("ACTIVITY_DB_NAME"),
        "Port" => "",
        "Username" => env("ACTIVITY_DB_USER"),
        "Password" => env("ACTIVITY_DB_PASSWORD")
    ],
    "Financial"=> [
        "Host" => env("FINANCIAL_DB_HOST"),
        "Database" => env("FINANCIAL_DB_NAME"),
        "Port" => "",
        "Username" => env("FINANCIAL_DB_USER"),
        "Password" => env("FINANCIAL_DB_PASS")
    ],
    "Integration"=> [
        "Host" => env("INTEGRATION_DB_HOST"),
        "Database" => env("INTEGRATION_DB_NAME"),
        "Port" => "",
        "Username" => env("INTEGRATION_DB_USER"),
        "Password" => env("INTEGRATION_DB_PASS")
    ],
    "Traffic"=> [
        "Host" => env("TRAFFIC_DB_HOST"),
        "Database" => env("TRAFFIC_DB_NAME"),
        "Port" => "",
        "Username" => env("TRAFFIC_DB_USER"),
        "Password" => env("TRAFFIC_DB_PASS")
    ],
    "Archive"=> [
        "Host" =>env("ARCHIVE_DB_HOST"),
        "Database" => env("ARCHIVE_DB_NAME"),
        "Port" => "",
        "Username" => env("ARCHIVE_DB_USER"),
        "Password" => env("ARCHIVE_DB_PASS")
    ],
    "Versioning"=> [
        "Host" =>env("VERSIONING_DB_HOST"),
        "Database" => env("VERSIONING_DB_NAME"),
        "Port" => "",
        "Username" => env("VERSIONING_DB_USER"),
        "Password" => env("VERSIONING_DB_PASS")
    ]
];