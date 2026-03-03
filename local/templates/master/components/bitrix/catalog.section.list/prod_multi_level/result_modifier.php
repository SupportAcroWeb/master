<?php

/** @var array $arResult */
/** @var array $arParams */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Iblock\IblockTable;

$arSectionList = [];
$currentRoot = 0;

foreach ($arResult['SECTIONS'] as $key => $arSection) {
    if ($arSection['DEPTH_LEVEL'] == 1) {
        $currentRoot = $arSection['ID'];
        if (!isset($arSectionList[$arSection['ID']])) {
            $arSectionList[$arSection['ID']] = array_merge($arSection, ['ITEMS' => []]);
        }
    } else if (array_key_exists($currentRoot, $arSectionList)) {
        $arSectionList[$currentRoot]['ITEMS'][$arSection['ID']] = $arSection;
    }
}

$arResult['SECTIONS'] = $arSectionList;

// Получаем описание инфоблока
$arResult['IBLOCK_DESCRIPTION'] = '';
if (!empty($arParams['IBLOCK_ID'])) {
    $iblockResult = IblockTable::getById($arParams['IBLOCK_ID']);
    if ($iblock = $iblockResult->fetch()) {
        $arResult['IBLOCK_DESCRIPTION'] = $iblock['DESCRIPTION'];
    }
}