<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */

$arResult['arSectionList'] = [];

$rsSection = CIBlockSection::GetList(
    ['SORT' => 'ASC', 'NAME' => 'ASC'],
    [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'GLOBAL_ACTIVE' => 'Y',
        '=UF_SHOW_MAIN' => '1',
    ],
    false,
    ['ID', 'NAME', 'IBLOCK_SECTION_ID', 'PICTURE', 'SORT', 'SECTION_PAGE_URL', 'UF_SHOW_MAIN', 'UF_BIG_IMG']
);
$rsSection->SetUrlTemplates('', $arParams['SECTION_URL'] ?? '');

$parents = [];
while ($arSection = $rsSection->GetNext(true, false)) {
    $arSection['SUBSECTIONS'] = [];
    $arSection['PICTURE_SRC'] = !empty($arSection['PICTURE']) ? CFile::GetPath($arSection['PICTURE']) : '';
    $buttons = CIBlock::GetPanelButtons($arSection['IBLOCK_ID'], 0, $arSection['ID'], ['SESSID' => false, 'CATALOG' => true]);
    $arSection['EDIT_LINK'] = $buttons['edit']['edit_section']['ACTION_URL'] ?? '';
    $arSection['DELETE_LINK'] = $buttons['edit']['delete_section']['ACTION_URL'] ?? '';
    $parents[$arSection['ID']] = $arSection;
}

if (empty($parents)) {
    return;
}

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
        $parents[$pid]['SUBSECTIONS'][] = $arSub;
    }
}

$arResult['arSectionList'] = array_values($parents);
