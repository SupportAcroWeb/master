<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */

if (!empty($arResult['SECTIONS'])) {
    $arSectionList = [];
    $arDepthLevel = [];
    $rsSection = CIBlockSection::GetList(
        ['SORT' => 'ASC'],
        ["IBLOCK_ID" => $arParams["IBLOCK_ID"], "GLOBAL_ACTIVE" => "Y", "=UF_MAIN_PAGE" => "1"],
        false,
        ["ID", "NAME", "IBLOCK_SECTION_ID", "PICTURE", "SORT", "SECTION_PAGE_URL", "UF_MAIN_PAGE", "UF_SORT"]
    );
    while ($arSection = $rsSection->GetNext(true, false)) {
        $arSectionList[$arSection['ID']] = $arSection;
    }


    $arResult['arSectionList'] = $arSectionList;
}