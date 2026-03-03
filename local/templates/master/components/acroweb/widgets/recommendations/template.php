<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 */

$this->setFrameMode(true);

if (!$arResult['HAS_ELEMENTS']) {
    return;
}

$activeTabs = $arResult['ACTIVE_TABS'];
$firstActiveTab = $arResult['FIRST_ACTIVE_TAB'];

$baseCatalogParams = [
    "ACTION_VARIABLE" => "action",
    "ADD_PICT_PROP" => "-",
    "ADD_PROPERTIES_TO_BASKET" => "Y",
    "ADD_SECTIONS_CHAIN" => "N",
    "ADD_TO_BASKET_ACTION" => "ADD",
    "AJAX_MODE" => "N",
    "AJAX_OPTION_ADDITIONAL" => "",
    "AJAX_OPTION_HISTORY" => "N",
    "AJAX_OPTION_JUMP" => "N",
    "AJAX_OPTION_STYLE" => "Y",
    "BACKGROUND_IMAGE" => "-",
    "BASKET_URL" => "/personal/basket/",
    "BROWSER_TITLE" => "-",
    "CACHE_FILTER" => "N",
    "CACHE_GROUPS" => "Y",
    "CACHE_TIME" => "36000000",
    "CACHE_TYPE" => "A",
    "COMPATIBLE_MODE" => "N",
    "CONVERT_CURRENCY" => "N",
    "CUSTOM_FILTER" => "",
    "DETAIL_URL" => "/produktsiya/#ELEMENT_CODE#/",
    "DISABLE_INIT_JS_IN_COMPONENT" => "N",
    "DISCOUNT_PERCENT_POSITION" => "top-left",
    "DISPLAY_BOTTOM_PAGER" => "N",
    "DISPLAY_COMPARE" => "N",
    "DISPLAY_TOP_PAGER" => "N",
    "ELEMENT_SORT_FIELD" => $arParams["SORT_BY1"],
    "ELEMENT_SORT_FIELD2" => $arParams["SORT_BY2"],
    "ELEMENT_SORT_ORDER" => $arParams["SORT_ORDER1"],
    "ELEMENT_SORT_ORDER2" => $arParams["SORT_ORDER2"],
    "ENLARGE_PRODUCT" => "STRICT",
    "HIDE_NOT_AVAILABLE" => "L",
    "HIDE_NOT_AVAILABLE_OFFERS" => "N",
    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
    "INCLUDE_SUBSECTIONS" => "Y",
    "LABEL_PROP" => array("SPECIALOFFER", "NEWPRODUCT", "SALELEADER"),
    "LABEL_PROP_MOBILE" => array("SPECIALOFFER", "NEWPRODUCT", "SALELEADER"),
    "LABEL_PROP_POSITION" => "top-left",
    "LAZY_LOAD" => "N",
    "LINE_ELEMENT_COUNT" => "3",
    "LOAD_ON_SCROLL" => "N",
    "MESSAGE_404" => "",
    "MESS_BTN_ADD_TO_BASKET" => "В корзину",
    "MESS_BTN_BUY" => "Купить",
    "MESS_BTN_DETAIL" => "Подробнее",
    "MESS_BTN_LAZY_LOAD" => "Показать ещё",
    "MESS_BTN_SUBSCRIBE" => "Подписаться",
    "MESS_NOT_AVAILABLE" => "Нет в наличии",
    "MESS_NOT_AVAILABLE_SERVICE" => "Недоступно",
    "META_DESCRIPTION" => "-",
    "META_KEYWORDS" => "-",
    "OFFERS_LIMIT" => "5",
    "PAGER_BASE_LINK_ENABLE" => "N",
    "PAGER_DESC_NUMBERING" => "N",
    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
    "PAGER_SHOW_ALL" => "N",
    "PAGER_SHOW_ALWAYS" => "N",
    "PAGER_TEMPLATE" => ".default",
    "PAGER_TITLE" => "Товары",
    "PAGE_ELEMENT_COUNT" => "18",
    "PARTIAL_PRODUCT_PROPERTIES" => "Y",
    "PRICE_CODE" => array("BASE"),
    "PRICE_VAT_INCLUDE" => "Y",
    "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
    "PRODUCT_ID_VARIABLE" => "id",
    "PRODUCT_PROPS_VARIABLE" => "prop",
    "PRODUCT_QUANTITY_VARIABLE" => "quantity",
    "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
    "PRODUCT_SUBSCRIPTION" => "Y",
    "PROPERTY_CODE_MOBILE" => array(),
    "SECTION_CODE" => "",
    "SECTION_CODE_PATH" => "",
    "SECTION_ID" => "",
    "SECTION_ID_VARIABLE" => "SECTION_ID",
    "SECTION_URL" => "/produktsiya/#SECTION_CODE_PATH#/",
    "SECTION_USER_FIELDS" => array("", ""),
    "SEF_MODE" => "N",
    "SEF_RULE" => "",
    "SET_BROWSER_TITLE" => "N",
    "SET_LAST_MODIFIED" => "N",
    "SET_META_DESCRIPTION" => "N",
    "SET_META_KEYWORDS" => "N",
    "SET_STATUS_404" => "N",
    "SET_TITLE" => "N",
    "SHOW_404" => "N",
    "SHOW_ALL_WO_SECTION" => "Y",
    "SHOW_CLOSE_POPUP" => "N",
    "SHOW_DISCOUNT_PERCENT" => "N",
    "SHOW_MAX_QUANTITY" => "N",
    "SHOW_OLD_PRICE" => "Y",
    "SHOW_PRICE_COUNT" => "1",
    "SHOW_SLIDER" => "N",
    "SLIDER_INTERVAL" => "3000",
    "SLIDER_PROGRESS" => "N",
    "TEMPLATE_THEME" => "blue",
    "USE_ENHANCED_ECOMMERCE" => "N",
    "USE_MAIN_ELEMENT_SECTION" => "N",
    "USE_PRICE_COUNT" => "N",
    "USE_PRODUCT_QUANTITY" => "Y"
];
?>

<section data-container="tab1" class="block-products">
    <div class="container">
        <div class="heading-cols1">
            <h2 class="title2">Рекомендуем</h2>
            <div class="tab1-nav">
                <?php foreach ($activeTabs as $tabKey => $tabConfig): ?>
                    <button data-action="showTab1" data-tab="<?= $tabKey ?>"
                            class="btn-text btn-text_1<?= $tabKey === $firstActiveTab ? ' active' : '' ?>"
                            type="button">
                        <?= $tabConfig['title'] ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <a class="btn-text btn-text_primary" href="/produktsiya/">
                <span>Перейти в каталог</span>
                <svg aria-hidden="true" width="13" height="13">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                </svg>
            </a>
        </div>
    </div>
    <div class="container container_bordered1">
        <?php foreach ($activeTabs as $tabKey => $tabConfig): ?>
            <div data-tab-content="<?= $tabKey ?>"
                 style="display: <?= $tabKey === $firstActiveTab ? 'block' : 'none' ?>;">
                <?php
                $tabCatalogParams = $baseCatalogParams;
                $tabCatalogParams['FILTER_NAME'] = $tabConfig['filter_name'];

$APPLICATION->IncludeComponent(
                    "bitrix:catalog.section",
                    "slide",
                    $tabCatalogParams
                );
                ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>