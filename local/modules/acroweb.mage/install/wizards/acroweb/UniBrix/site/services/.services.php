<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
    "main" => Array(
        "NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
        "STAGES" => Array(
            "files.php",
            "settings.php",
            "save.php",
            "install.php",
        ),
    ),
    "sale" => Array(
        "NAME" => GetMessage("SERVICE_SALE_DEMO_DATA"),
        "STAGES" => Array(
            "locations.php",
            "step1.php",
            "step2.php",
            "step3.php"
        ),
    ),
    "iblock" => Array(
        "NAME" => GetMessage("SERVICE_IMPORT_DATA"),
        "STAGES" => Array(
            "import.php",
        ),
    ),
);
?>