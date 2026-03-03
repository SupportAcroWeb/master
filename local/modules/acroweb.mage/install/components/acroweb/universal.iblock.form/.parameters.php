<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if (!Loader::includeModule('iblock')) {
    return;
}

$iblockTypes = CIBlockType::GetList(['SORT' => 'ASC'], ['ACTIVE' => 'Y']);
$iblockTypeList = ['' => Loc::getMessage('ACROWEB_UIBF_IBLOCK_TYPE_ANY')];
while ($iblockType = $iblockTypes->Fetch()) {
    if ($arIBType = CIBlockType::GetByIDLang($iblockType["ID"], LANGUAGE_ID)) {
        $iblockTypeList[$iblockType["ID"]] = "[".$iblockType["ID"]."] ".$arIBType["NAME"];
    }
}

$iblockList = ['' => Loc::getMessage('ACROWEB_UIBF_IBLOCK_ANY')];
$iblocks = CIBlock::GetList(['SORT' => 'ASC'], ['ACTIVE' => 'Y']);
while ($iblock = $iblocks->Fetch()) {
    $iblockList[$iblock['ID']] = "[".$iblock['ID']."] ".$iblock['NAME'];
}

$arComponentParameters = [
    "GROUPS" => [
        "SETTINGS" => [
            "NAME" => Loc::getMessage("ACROWEB_UIBF_GROUP_SETTINGS"),
            "SORT" => 100,
        ],
        "VISUAL" => [
            "NAME" => Loc::getMessage("ACROWEB_UIBF_GROUP_VISUAL"),
            "SORT" => 200,
        ],
    ],
    "PARAMETERS" => [
        "IBLOCK_TYPE" => [
            "PARENT" => "SETTINGS",
            "NAME" => Loc::getMessage("ACROWEB_UIBF_IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $iblockTypeList,
            "REFRESH" => "Y",
        ],
        "IBLOCK_ID" => [
            "PARENT" => "SETTINGS",
            "NAME" => Loc::getMessage("ACROWEB_UIBF_IBLOCK_ID"),
            "TYPE" => "LIST",
            "VALUES" => $iblockList,
        ],
        "ELEMENT_NAME" => [
            "PARENT" => "SETTINGS",
            "NAME" => Loc::getMessage("ACROWEB_UIBF_ELEMENT_NAME"),
            "TYPE" => "STRING",
            "DEFAULT" => Loc::getMessage("ACROWEB_UIBF_ELEMENT_NAME_DEFAULT"),
        ],
        "USE_CAPTCHA" => [
            "PARENT" => "SETTINGS",
            "NAME" => Loc::getMessage("ACROWEB_UIBF_USE_CAPTCHA"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "SUCCESS_URL" => [
            "PARENT" => "SETTINGS",
            "NAME" => Loc::getMessage("ACROWEB_UIBF_SUCCESS_URL"),
            "TYPE" => "STRING",
        ],
        "AJAX" => [
            "PARENT" => "SETTINGS",
            "NAME" => Loc::getMessage("ACROWEB_UIBF_AJAX"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "FORM_TITLE" => [
            "PARENT" => "VISUAL",
            "NAME" => Loc::getMessage("ACROWEB_UIBF_FORM_TITLE"),
            "TYPE" => "STRING",
        ],
        "FORM_DESCRIPTION" => [
            "PARENT" => "VISUAL",
            "NAME" => Loc::getMessage("ACROWEB_UIBF_FORM_DESCRIPTION"),
            "TYPE" => "STRING",
        ],
        "BUTTON_TEXT" => [
            "PARENT" => "VISUAL",
            "NAME" => Loc::getMessage("ACROWEB_UIBF_BUTTON_TEXT"),
            "TYPE" => "STRING",
            "DEFAULT" => Loc::getMessage("ACROWEB_UIBF_BUTTON_TEXT_DEFAULT"),
        ],
    ],
];