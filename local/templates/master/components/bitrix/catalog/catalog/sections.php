<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true); ?>

<div class="container">
    <div class="grid1">
        <?php
        $APPLICATION->IncludeComponent(
            "bitrix:catalog.section.list",
            "prod_multi_level",
            array(
                "ADDITIONAL_COUNT_ELEMENTS_FILTER" => "additionalCountFilter",
                "ADD_SECTIONS_CHAIN" => "Y",
                "CACHE_FILTER" => "N",
                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                "CACHE_TIME" => $arParams["CACHE_TIME"],
                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                "COUNT_ELEMENTS" => "N",
                "COUNT_ELEMENTS_FILTER" => "CNT_ACTIVE",
                "FILTER_NAME" => "sectionsFilter",
                "HIDE_SECTIONS_WITH_ZERO_COUNT_ELEMENTS" => "N",
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                "SECTION_CODE" => "",
                "SECTION_FIELDS" => array("", "PICTURE", ""),
                "SECTION_ID" => $_REQUEST["SECTION_ID"],
                "SECTION_URL" => "",
                "SECTION_USER_FIELDS" => array("", ""),
                "SHOW_PARENT_NAME" => "Y",
                "TOP_DEPTH" => "2",
                "VIEW_MODE" => "LINE"
            )
        );
        ?>
    </div>
</div>