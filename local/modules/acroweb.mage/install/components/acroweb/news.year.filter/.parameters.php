<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$arComponentParameters = [
    "GROUPS" => [],
    "PARAMETERS" => [
        "IBLOCK_ID" => [
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("NEWS_YEAR_FILTER_IBLOCK_ID"),
            "TYPE" => "STRING",
            "DEFAULT" => '={$_REQUEST["ID"]}',
        ],
    ],
];