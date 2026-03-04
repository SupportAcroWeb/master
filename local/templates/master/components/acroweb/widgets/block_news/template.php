<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 */

$this->setFrameMode(true); ?>

<section class="block-portfolio">
    <div data-swiper="container" class="container">
        <div class="heading-cols1">
            <div class="heading-cols1__col">
                <h2 class="title2"><?php $APPLICATION->IncludeFile('/include/home/portfolio_title.php', [], ['MODE' => 'php']); ?></h2>
            </div>
            <div class="heading-cols1__col">
                <div class="heading-cols1__text"><?php $APPLICATION->IncludeFile('/include/home/portfolio_text.php', [], ['MODE' => 'php']); ?></div>
            </div>
        </div>
        <div class="grid2">
            <div class="swiper-navs">
                <button class="swiper-nav swiper-nav_prev" type="button">
                    <svg width="16" height="16" aria-hidden="true">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow2"></use>
                    </svg>
                </button>
                <button class="swiper-nav swiper-nav_next" type="button">
                    <svg width="16" height="16" aria-hidden="true">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow2"></use>
                    </svg>
                </button>
            </div>
            <a class="btn-text btn-text_white" href="/portfolio/">
                <span>Смотреть все</span>
                <svg class="btn-text__icon" width="14" height="14" aria-hidden="true">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                </svg>
            </a>
        </div>
        <?php
        $APPLICATION->IncludeComponent(
            'bitrix:news.list',
            'news_slider',
            [
                'ACTIVE_DATE_FORMAT' => 'j F Y',
                'ADD_SECTIONS_CHAIN' => 'N',
                'AJAX_MODE' => 'N',
                'AJAX_OPTION_ADDITIONAL' => '',
                'AJAX_OPTION_HISTORY' => 'N',
                'AJAX_OPTION_JUMP' => 'N',
                'AJAX_OPTION_STYLE' => 'Y',
                'CACHE_FILTER' => 'N',
                'CACHE_GROUPS' => 'Y',
                'CACHE_TIME' => '36000000',
                'CACHE_TYPE' => 'A',
                'CHECK_DATES' => 'Y',
                'DETAIL_URL' => '',
                'DISPLAY_BOTTOM_PAGER' => 'N',
                'DISPLAY_DATE' => 'Y',
                'DISPLAY_NAME' => 'Y',
                'DISPLAY_PICTURE' => 'Y',
                'DISPLAY_PREVIEW_TEXT' => 'Y',
                'DISPLAY_TOP_PAGER' => 'N',
                'FIELD_CODE' => ['', ''],
                'FILTER_NAME' => $arResult['FILTER_NAME'] ?? '',
                'HIDE_LINK_WHEN_NO_DETAIL' => 'N',
                'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
                'INCLUDE_SUBSECTIONS' => 'Y',
                'MESSAGE_404' => '',
                'NEWS_COUNT' => '20',
                'PAGER_BASE_LINK_ENABLE' => 'N',
                'PAGER_DESC_NUMBERING' => 'N',
                'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
                'PAGER_SHOW_ALL' => 'N',
                'PAGER_SHOW_ALWAYS' => 'N',
                'PAGER_TEMPLATE' => '.default',
                'PAGER_TITLE' => 'Новости',
                'PARENT_SECTION' => '',
                'PARENT_SECTION_CODE' => '',
                'PREVIEW_TRUNCATE_LEN' => '',
                'PROPERTY_CODE' => ['GALLERY'],
                'DETAIL_URL' => $arParams['DETAIL_URL'] ?? '/portfolio/#ELEMENT_CODE#/',
                'SET_BROWSER_TITLE' => 'N',
                'SET_LAST_MODIFIED' => 'N',
                'SET_META_DESCRIPTION' => 'N',
                'SET_META_KEYWORDS' => 'N',
                'SET_STATUS_404' => 'N',
                'SET_TITLE' => 'N',
                'SHOW_404' => 'N',
                'SORT_BY1' => $arParams['SORT_BY1'],
                'SORT_BY2' => $arParams['SORT_BY2'],
                'SORT_ORDER1' => $arParams['SORT_ORDER1'],
                'SORT_ORDER2' => $arParams['SORT_ORDER2'],
                'STRICT_SECTION_CHECK' => 'N',
                "NAME_BLOCK" => $arParams['NAME_BLOCK'],
                "SHOW_ON_MAIN" => $arParams['SHOW_ON_MAIN'],
            ]
        );
        ?>
    </div>
</section>
