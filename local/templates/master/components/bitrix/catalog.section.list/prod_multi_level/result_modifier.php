<?php
declare(strict_types=1);

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */
/** @var array $arParams */

$parents = [];
$rsSection = CIBlockSection::GetList(
    ['SORT' => 'ASC', 'NAME' => 'ASC'],
    [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'GLOBAL_ACTIVE' => 'Y',
        'DEPTH_LEVEL' => 1,
    ],
    false,
    ['ID', 'NAME', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'SORT', 'SECTION_PAGE_URL', 'PICTURE']
);
$rsSection->SetUrlTemplates('', $arParams['SECTION_URL'] ?? '');

while ($arSection = $rsSection->GetNext(true, false)) {
    $arSection['ITEMS'] = [];
    $arSection['PICTURE_SRC'] = !empty($arSection['PICTURE']) ? CFile::GetPath($arSection['PICTURE']) : '';
    $buttons = CIBlock::GetPanelButtons($arSection['IBLOCK_ID'], 0, $arSection['ID'], ['SESSID' => false, 'CATALOG' => true]);
    $arSection['EDIT_LINK'] = $buttons['edit']['edit_section']['ACTION_URL'] ?? '';
    $arSection['DELETE_LINK'] = $buttons['edit']['delete_section']['ACTION_URL'] ?? '';
    $parents[$arSection['ID']] = $arSection;
}

if (!empty($parents)) {
    $parentIds = array_keys($parents);
    $rsSub = CIBlockSection::GetList(
        ['SORT' => 'ASC', 'NAME' => 'ASC'],
        [
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'GLOBAL_ACTIVE' => 'Y',
            'IBLOCK_SECTION_ID' => $parentIds,
        ],
        false,
        ['ID', 'NAME', 'IBLOCK_SECTION_ID', 'SORT', 'SECTION_PAGE_URL']
    );
    $rsSub->SetUrlTemplates('', $arParams['SECTION_URL'] ?? '');

    while ($arSub = $rsSub->GetNext(true, false)) {
        $pid = (int)$arSub['IBLOCK_SECTION_ID'];
        if (isset($parents[$pid])) {
            $parents[$pid]['ITEMS'][] = $arSub;
        }
    }
}

$arResult['SECTIONS'] = array_values($parents);

$arResult['IBLOCK_DESCRIPTION'] = '';
if (!empty($arParams['IBLOCK_ID']) && \Bitrix\Main\Loader::includeModule('iblock')) {
    $iblock = \Bitrix\Iblock\IblockTable::getById($arParams['IBLOCK_ID'])->fetch();
    if ($iblock) {
        $arResult['IBLOCK_DESCRIPTION'] = $iblock['DESCRIPTION'] ?? '';
    }
}
