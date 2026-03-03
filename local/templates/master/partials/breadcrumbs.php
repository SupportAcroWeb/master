<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;?>

<? $APPLICATION->IncludeComponent(
    "bitrix:breadcrumb",
    ".default",
    array(
        "PATH" => "",
        "SITE_ID" => "s1",
        "START_FROM" => "0",
        "COMPONENT_TEMPLATE" => "",
        "SHOW" => "Y"
    ),
    false
); ?>