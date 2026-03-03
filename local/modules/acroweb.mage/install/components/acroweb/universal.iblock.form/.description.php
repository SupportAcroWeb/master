<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    "NAME" => Loc::getMessage("ACROWEB_UIBF_COMPONENT_NAME"),
    "DESCRIPTION" => Loc::getMessage("ACROWEB_UIBF_COMPONENT_DESCRIPTION"),
    "ICON" => "/images/icon.gif",
    "SORT" => 10,
    "CACHE_PATH" => "Y",
    "PATH" => [
        "ID" => "acroweb",
        "NAME" => Loc::getMessage("ACROWEB_UIBF_COMPONENT_PATH_NAME"),
    ],
];