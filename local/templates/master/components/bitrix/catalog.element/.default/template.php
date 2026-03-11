<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\ProductTable;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(true);

$currencyList = '';
$templateLibrary = [];

if (!empty($arResult['CURRENCIES'])) {
    $templateLibrary[] = 'currency';
    $currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

$haveOffers = !empty($arResult['OFFERS']);

$templateData = [
    'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
    'TEMPLATE_LIBRARY' => $templateLibrary,
    'CURRENCIES' => $currencyList,
    'ITEM' => [
        'ID' => $arResult['ID'],
        'IBLOCK_ID' => $arResult['IBLOCK_ID'],
    ],
];

$templateData['FILTER_FOR_NEW_TABS'] = [
    'RECOMMENDATIONS' => $arResult['PROPERTIES']['RECOMMEND']['VALUE'],
];

if ($haveOffers) {
    $templateData['ITEM']['OFFERS_SELECTED'] = $arResult['OFFERS_SELECTED'];
    $templateData['ITEM']['JS_OFFERS'] = $arResult['JS_OFFERS'];
}
unset($currencyList, $templateLibrary);

$mainId = $this->GetEditAreaId($arResult['ID']);
$itemIds = array(
    'ID' => $mainId,
    'ARTICLE_ID' => $mainId . '_article',
    'DISCOUNT_PERCENT_ID' => $mainId . '_dsc_pict',
    'STICKER_ID' => $mainId . '_sticker',
    'BIG_SLIDER_ID' => $mainId . '_big_slider',
    'BIG_IMG_CONT_ID' => $mainId . '_bigimg_cont',
    'SLIDER_CONT_ID' => $mainId . '_slider_cont',
    'OLD_PRICE_ID' => $mainId . '_old_price',
    'PRICE_ID' => $mainId . '_price',
    'DESCRIPTION_ID' => $mainId . '_description',
    'DISCOUNT_PRICE_ID' => $mainId . '_price_discount',
    'PRICE_TOTAL' => $mainId . '_price_total',
    'SLIDER_CONT_OF_ID' => $mainId . '_slider_cont_',
    'QUANTITY_ID' => $mainId . '_quantity',
    'QUANTITY_DOWN_ID' => $mainId . '_quant_down',
    'QUANTITY_UP_ID' => $mainId . '_quant_up',
    'QUANTITY_MEASURE' => $mainId . '_quant_measure',
    'QUANTITY_LIMIT' => $mainId . '_quant_limit',
    'BUY_LINK' => $mainId . '_buy_link',
    'ADD_BASKET_LINK' => $mainId . '_add_basket_link',
    'BASKET_ACTIONS_ID' => $mainId . '_basket_actions',
    'NOT_AVAILABLE_MESS' => $mainId . '_not_avail',
    'COMPARE_LINK' => $mainId . '_compare_link',
    'TREE_ID' => $mainId . '_skudiv',
    'DISPLAY_PROP_DIV' => $mainId . '_sku_prop',
    'DISPLAY_MAIN_PROP_DIV' => $mainId . '_main_sku_prop',
    'OFFER_GROUP' => $mainId . '_set_group_',
    'BASKET_PROP_DIV' => $mainId . '_basket_prop',
    'SUBSCRIBE_LINK' => $mainId . '_subscribe',
    'TABS_ID' => $mainId . '_tabs',
    'TAB_CONTAINERS_ID' => $mainId . '_tab_containers',
    'SMALL_CARD_PANEL_ID' => $mainId . '_small_card_panel',
    'TABS_PANEL_ID' => $mainId . '_tabs_panel'
);

$obName = $templateData['JS_OBJ'] = 'ob' . preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);
$name = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
    : $arResult['NAME'];
$title = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
    : $arResult['NAME'];
$alt = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
    : $arResult['NAME'];

if ($haveOffers) {
    $actualItem = $arResult['OFFERS'][$arResult['OFFERS_SELECTED']] ?? reset($arResult['OFFERS']);
    $showSliderControls = false;

    foreach ($arResult['OFFERS'] as $offer) {
        if ($offer['MORE_PHOTO_COUNT'] > 1) {
            $showSliderControls = true;
            break;
        }
    }
} else {
    $actualItem = $arResult;
    $showSliderControls = $arResult['MORE_PHOTO_COUNT'] > 1;
}

$skuProps = array();
$price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
$measureRatio = $actualItem['ITEM_MEASURE_RATIOS'][$actualItem['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
$showDiscount = $price['PERCENT'] > 0;

if ($arParams['SHOW_SKU_DESCRIPTION'] === 'Y') {
    $skuDescription = false;
    foreach ($arResult['OFFERS'] as $offer) {
        if ($offer['DETAIL_TEXT'] != '' || $offer['PREVIEW_TEXT'] != '') {
            $skuDescription = true;
            break;
        }
    }
    $showDescription = $skuDescription || !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
} else {
    $showDescription = !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
}

$getDimensionSortValue = static function (string $valueName): ?float {
    $normalizedValue = trim(html_entity_decode($valueName, ENT_QUOTES | ENT_HTML5));

    if ($normalizedValue === '' || $normalizedValue === '-') {
        return null;
    }

    if (preg_match('/-?\d+(?:[.,]\d+)?/u', $normalizedValue, $matches) !== 1) {
        return null;
    }

    return (float)str_replace(',', '.', $matches[0]);
};

$prepareDimensionValues = static function (array $values) use ($getDimensionSortValue): array {
    $preparedValues = [];

    foreach ($values as $value) {
        $valueId = (string)($value['ID'] ?? '');
        $valueName = trim((string)($value['NAME'] ?? ''));
        $sortValue = $getDimensionSortValue($valueName);

        if ($valueId === '' || $sortValue === null) {
            continue;
        }

        $preparedValues[$valueId] = [
            'id' => (int)$value['ID'],
            'name' => $valueName,
            'sort' => $sortValue,
        ];
    }

    uasort($preparedValues, static function (array $first, array $second): int {
        if ($first['sort'] === $second['sort']) {
            return strnatcasecmp($first['name'], $second['name']);
        }

        return $first['sort'] <=> $second['sort'];
    });

    return $preparedValues;
};

$sizeTable = [
    'widthPropId' => 0,
    'heightPropId' => 0,
    'widths' => [],
    'heights' => [],
    'cells' => [],
];
$hasSizeTable = false;

if ($haveOffers && !empty($arResult['OFFERS_PROP']) && !empty($arResult['SKU_PROPS'])) {
    $widthSkuProperty = null;
    $heightSkuProperty = null;

    foreach ($arResult['SKU_PROPS'] as $skuProperty) {
        if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']])) {
            continue;
        }

        $propertyNameLower = mb_strtolower((string)$skuProperty['NAME']);
        $propertyCodeUpper = mb_strtoupper((string)$skuProperty['CODE']);

        if (
            $widthSkuProperty === null
            && ($propertyCodeUpper === 'WIDTH' || mb_strpos($propertyNameLower, 'шир') !== false)
        ) {
            $widthSkuProperty = $skuProperty;
            continue;
        }

        if (
            $heightSkuProperty === null
            && ($propertyCodeUpper === 'HEIGHT' || mb_strpos($propertyNameLower, 'выс') !== false)
        ) {
            $heightSkuProperty = $skuProperty;
        }
    }

    if ($widthSkuProperty !== null && $heightSkuProperty !== null) {
        $sizeTable['widthPropId'] = (int)$widthSkuProperty['ID'];
        $sizeTable['heightPropId'] = (int)$heightSkuProperty['ID'];
        $sizeTable['widths'] = $prepareDimensionValues($widthSkuProperty['VALUES']);
        $sizeTable['heights'] = $prepareDimensionValues($heightSkuProperty['VALUES']);

        foreach ($arResult['OFFERS'] as $offer) {
            $widthValueId = (string)($offer['TREE']['PROP_' . $sizeTable['widthPropId']] ?? '');
            $heightValueId = (string)($offer['TREE']['PROP_' . $sizeTable['heightPropId']] ?? '');

            if (
                $widthValueId === ''
                || $heightValueId === ''
                || !isset($sizeTable['widths'][$widthValueId], $sizeTable['heights'][$heightValueId])
            ) {
                continue;
            }

            $offerPrice = $offer['ITEM_PRICES'][$offer['ITEM_PRICE_SELECTED']] ?? null;
            $sizeTable['cells'][$heightValueId][$widthValueId] = [
                'offerId' => (int)$offer['ID'],
                'widthValueId' => (int)$widthValueId,
                'heightValueId' => (int)$heightValueId,
                'price' => $offerPrice
                    ? ((float)$offerPrice['PRICE'] > 0 ? (string)$offerPrice['PRINT_PRICE'] : 'По запросу')
                    : '',
            ];
        }

        $hasSizeTable = !empty($sizeTable['widths']) && !empty($sizeTable['heights']);
    }
}

$showBuyBtn = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']);
$buyButtonClassName = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showAddBtn = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']);
$showButtonClassName = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($arResult['PRODUCT']['SUBSCRIBE'] === 'Y' || $haveOffers);

$arParams['MESS_BTN_BUY'] = $arParams['MESS_BTN_BUY'] ?: Loc::getMessage('CT_BCE_CATALOG_BUY');
$arParams['MESS_BTN_ADD_TO_BASKET'] = $arParams['MESS_BTN_ADD_TO_BASKET'] ?: Loc::getMessage('CT_BCE_CATALOG_ADD');

if ($arResult['MODULES']['catalog'] && $arResult['PRODUCT']['TYPE'] === ProductTable::TYPE_SERVICE) {
    $arParams['~MESS_NOT_AVAILABLE_SERVICE'] ??= '';
    $arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE_SERVICE']
        ?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE');

    $arParams['MESS_NOT_AVAILABLE_SERVICE'] ??= '';
    $arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE_SERVICE']
        ?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE');
} else {
    $arParams['~MESS_NOT_AVAILABLE'] ??= '';
    $arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE']
        ?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE');

    $arParams['MESS_NOT_AVAILABLE'] ??= '';
    $arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE']
        ?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE');
}

$arParams['MESS_BTN_COMPARE'] = $arParams['MESS_BTN_COMPARE'] ?: Loc::getMessage('CT_BCE_CATALOG_COMPARE');
$arParams['MESS_PRICE_RANGES_TITLE'] = $arParams['MESS_PRICE_RANGES_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_PRICE_RANGES_TITLE');
$arParams['MESS_DESCRIPTION_TAB'] = $arParams['MESS_DESCRIPTION_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_DESCRIPTION_TAB');
$arParams['MESS_PROPERTIES_TAB'] = $arParams['MESS_PROPERTIES_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_PROPERTIES_TAB');
$arParams['MESS_COMMENTS_TAB'] = $arParams['MESS_COMMENTS_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_COMMENTS_TAB');
$arParams['MESS_SHOW_MAX_QUANTITY'] = $arParams['MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCE_CATALOG_SHOW_MAX_QUANTITY');
$arParams['MESS_RELATIVE_QUANTITY_MANY'] = $arParams['MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['MESS_RELATIVE_QUANTITY_FEW'] = $arParams['MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_FEW');

$positionClassMap = array(
    'left' => 'product-item-label-left',
    'center' => 'product-item-label-center',
    'right' => 'product-item-label-right',
    'bottom' => 'product-item-label-bottom',
    'middle' => 'product-item-label-middle',
    'top' => 'product-item-label-top'
);

$discountPositionClass = 'product-item-label-big';
if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION'])) {
    foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos) {
        $discountPositionClass .= isset($positionClassMap[$pos]) ? ' ' . $positionClassMap[$pos] : '';
    }
}

$labelPositionClass = 'product-item-label-big';
if (!empty($arParams['LABEL_PROP_POSITION'])) {
    foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos) {
        $labelPositionClass .= isset($positionClassMap[$pos]) ? ' ' . $positionClassMap[$pos] : '';
    }
}

// Получаем артикул
$getArticleValue = static function (array $item): string {
    if (!empty($item['PROPERTIES']['ARTNUMBER']['VALUE'])) {
        return (string) $item['PROPERTIES']['ARTNUMBER']['VALUE'];
    }

    if (!empty($item['PROPERTIES']['CML2_ARTICLE']['VALUE'])) {
        return (string) $item['PROPERTIES']['CML2_ARTICLE']['VALUE'];
    }

    return '';
};

$artic = $getArticleValue($arResult);
$currentArtic = $haveOffers ? ($getArticleValue($actualItem) ?: $artic) : $artic;

// Дата поступления
$arrivalDate = '';
if (!empty($arResult['DISPLAY_PROPERTIES']['DATA_POSTUPLENIYA']['DISPLAY_VALUE'])) {
    $arrivalDateRaw = $arResult['DISPLAY_PROPERTIES']['DATA_POSTUPLENIYA']['DISPLAY_VALUE'];
    $arrivalDate = is_array($arrivalDateRaw) ? implode(' / ', $arrivalDateRaw) : (string)$arrivalDateRaw;
} elseif (!empty($arResult['PROPERTIES']['DATA_POSTUPLENIYA']['VALUE'])) {
    $arrivalDateRaw = $arResult['PROPERTIES']['DATA_POSTUPLENIYA']['VALUE'];
    $arrivalDate = is_array($arrivalDateRaw) ? implode(' / ', $arrivalDateRaw) : (string)$arrivalDateRaw;
}
$showArrivalDate = !$actualItem['CAN_BUY'] && $arrivalDate !== '';
?>

    <?php
    $badgeClasses = [
        'P' => 'badge2_blue',
        'M' => 'badge2_yellow',
        'K' => 'badge2_red',
        'N' => 'badge2_green',
        'S' => 'badge2_orange',
        'H' => 'badge2_grey',
    ];
    $badgeValues = [];
    if (!empty($arResult['DISPLAY_PROPERTIES']['PROP_411']['VALUE'])) {
        $badgeValues = is_array($arResult['DISPLAY_PROPERTIES']['PROP_411']['VALUE'])
            ? $arResult['DISPLAY_PROPERTIES']['PROP_411']['VALUE']
            : [$arResult['DISPLAY_PROPERTIES']['PROP_411']['VALUE']];
    }
    $isPriceZero = !empty($price) && (float)$price['PRICE'] <= 0;
    ?>
    <div class="catalog-detail-wrapper" id="<?= $itemIds['ID'] ?>" itemscope itemtype="http://schema.org/Product">
        <div class="container container_bordered1">
            <div class="grid-detail">
                <div class="grid-detail__data">
                    <div
                        class="catalog-detail__code"
                        id="<?= $itemIds['ARTICLE_ID'] ?>"
                        <?= $currentArtic === '' ? 'style="display: none;"' : '' ?>
                    >
                        Артикул: <?= htmlspecialcharsbx($currentArtic) ?>
                    </div>
                    <h1 class="catalog-detail__name"><?= $name ?></h1>

                    <?php if ($showDescription): ?>
                        <div class="textblock1" id="<?= $itemIds['DESCRIPTION_ID'] ?>_short">
                            <?php
                            if (
                                $arResult['PREVIEW_TEXT'] != ''
                                && (
                                    $arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'S'
                                    || ($arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'E' && $arResult['DETAIL_TEXT'] == '')
                                )
                            ) {
                                echo $arResult['PREVIEW_TEXT_TYPE'] === 'html'
                                    ? $arResult['PREVIEW_TEXT']
                                    : '<p>' . $arResult['PREVIEW_TEXT'] . '</p>';
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($haveOffers && !empty($arResult['OFFERS_PROP'])): ?>
                        <div class="catalog-detail__params catalog-detail__params_offer">
                            <?php foreach ($arResult['SKU_PROPS'] as $skuProperty): ?>
                                <?php
                                if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']])) {
                                    continue;
                                }

                                $propertyId = (int)$skuProperty['ID'];
                                $selectedValueId = (string)($actualItem['TREE']['PROP_' . $propertyId] ?? '');
                                $propertyName = (string)$skuProperty['NAME'];
                                $propertyNameLower = mb_strtolower($propertyName);
                                $isDimensionProp = mb_strpos($propertyNameLower, 'шир') !== false
                                    || mb_strpos($propertyNameLower, 'выс') !== false;

                                if ($isDimensionProp) {
                                    continue;
                                }
                                ?>
                                <fieldset class="catalog-detail__param">
                                    <legend class="catalog-detail__title"><?= htmlspecialcharsbx($propertyName) ?></legend>
                                    <div class="params-list">
                                        <?php foreach ($skuProperty['VALUES'] as $value): ?>
                                            <?php
                                            $valueName = htmlspecialcharsbx((string)$value['NAME']);
                                            $checked = $selectedValueId !== '' && $selectedValueId === (string)$value['ID'];
                                            ?>
                                            <label class="radio-parameter">
                                                <input
                                                    class="radio-parameter__input"
                                                    type="radio"
                                                    name="sku_prop_<?= $propertyId ?>"
                                                    value="<?= (int)$value['ID'] ?>"
                                                    data-sku-radio="<?= $propertyId ?>_<?= (int)$value['ID'] ?>"
                                                    <?= $checked ? 'checked' : '' ?>
                                                >
                                                <span class="radio-parameter__visual">
                                                    <?php if (
                                                        $skuProperty['SHOW_MODE'] === 'PICT'
                                                        && !empty($value['PICT']['SRC'])
                                                    ): ?>
                                                        <span
                                                            class="radio-parameter__color"
                                                            style="background-image: url('<?= htmlspecialcharsbx($value['PICT']['SRC']) ?>');"
                                                        ></span>
                                                    <?php endif; ?>
                                                    <span><?= $valueName ?></span>
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </fieldset>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($badgeValues)): ?>
                        <div class="badges-list1">
                            <?php foreach ($badgeValues as $value): ?>
                                <?php if (isset($badgeClasses[$value])): ?>
                                    <span class="badge2 <?= $badgeClasses[$value] ?>"><?= htmlspecialcharsbx($value) ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($arResult['DISPLAY_PROPERTIES'])): ?>
                        <h2 class="catalog-detail__title">Характеристики</h2>
                        <div class="table-specs1-wrapper">
                            <table class="table-specs1">
                                <?php
                                $propCount = 0;
                                foreach ($arResult['DISPLAY_PROPERTIES'] as $property) {
                                    if ($propCount >= 5) {
                                        break;
                                    }
                                    ?>
                                    <tr>
                                        <td><?= $property['NAME'] ?></td>
                                        <td><?= is_array($property['DISPLAY_VALUE'])
                                                ? implode(' / ', $property['DISPLAY_VALUE'])
                                                : $property['DISPLAY_VALUE'] ?></td>
                                    </tr>
                                    <?php
                                    $propCount++;
                                }
                                ?>
                            </table>
                        </div>
                    <?php endif; ?>

                    <?php if ($haveOffers && !empty($arResult['OFFERS_PROP'])): ?>
                        <div id="<?= $itemIds['TREE_ID'] ?>" style="display: none;">
                            <?php foreach ($arResult['SKU_PROPS'] as $skuProperty): ?>
                                <?php
                                if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']])) {
                                    continue;
                                }

                                $propertyId = $skuProperty['ID'];
                                $skuProps[] = array(
                                    'ID' => $propertyId,
                                    'SHOW_MODE' => $skuProperty['SHOW_MODE'],
                                    'VALUES' => $skuProperty['VALUES'],
                                    'VALUES_COUNT' => $skuProperty['VALUES_COUNT']
                                );
                                ?>
                                <div class="product-item-detail-info-container" data-entity="sku-line-block">
                                    <ul class="product-item-scu-item-list">
                                        <?php foreach ($skuProperty['VALUES'] as &$value): ?>
                                            <?php $value['NAME'] = htmlspecialcharsbx($value['NAME']); ?>
                                            <?php if ($skuProperty['SHOW_MODE'] === 'PICT'): ?>
                                                <li
                                                    class="product-item-scu-item-color-container"
                                                    title="<?= $value['NAME'] ?>"
                                                    data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>"
                                                    data-onevalue="<?= $value['ID'] ?>"
                                                >
                                                    <div class="product-item-scu-item-color-block">
                                                        <div
                                                            class="product-item-scu-item-color"
                                                            title="<?= $value['NAME'] ?>"
                                                            style="background-image: url('<?= $value['PICT']['SRC'] ?>');"
                                                        ></div>
                                                    </div>
                                                </li>
                                            <?php else: ?>
                                                <li
                                                    class="product-item-scu-item-text-container"
                                                    title="<?= $value['NAME'] ?>"
                                                    data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>"
                                                    data-onevalue="<?= $value['ID'] ?>"
                                                >
                                                    <div class="product-item-scu-item-text-block">
                                                        <div class="product-item-scu-item-text"><?= $value['NAME'] ?></div>
                                                    </div>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($arResult['SHOW_OFFERS_PROPS']): ?>
                        <div id="<?= $itemIds['DISPLAY_MAIN_PROP_DIV'] ?>" style="display: none;"></div>
                    <?php endif; ?>
                </div>

                <div class="grid-detail__order">
                    <div class="grid-detail__order-inner">
                        <div class="catalog-detail__status <?= $actualItem['CAN_BUY'] ? 'instock' : 'outstock' ?>">
                            <?= $actualItem['CAN_BUY'] ? 'В наличии' : 'Нет в наличии' ?>
                        </div>

                        <?php if ($showArrivalDate): ?>
                            <div class="catalog-detail__arrival-date">Дата поступления: <?= htmlspecialcharsbx($arrivalDate) ?></div>
                        <?php endif; ?>

                        <div class="catalog-detail__params catalog-detail__params_panel">
                            <?php if ($haveOffers && !empty($arResult['OFFERS_PROP'])): ?>
                                <fieldset class="catalog-detail__param catalog-detail__param_filters">
                                    <legend class="catalog-detail__title">Расчет параметров:</legend>
                                    <div class="form-grid1__wrapper">
                                        <div class="form-grid1">
                                            <?php foreach ($arResult['SKU_PROPS'] as $skuProperty): ?>
                                                <?php
                                                if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']])) {
                                                    continue;
                                                }

                                                $propertyId = (int)$skuProperty['ID'];
                                                $selectedValueId = (string)($actualItem['TREE']['PROP_' . $propertyId] ?? '');
                                                $propertyName = (string)$skuProperty['NAME'];
                                                $propertyNameLower = mb_strtolower($propertyName);
                                                $isDimensionProp = mb_strpos($propertyNameLower, 'шир') !== false
                                                    || mb_strpos($propertyNameLower, 'выс') !== false;
                                                $dimensionValues = $prepareDimensionValues($skuProperty['VALUES']);

                                                if (!$isDimensionProp || empty($dimensionValues)) {
                                                    continue;
                                                }
                                                ?>
                                                <div class="form-grid1__row form-grid1__row_2">
                                                    <div class="form-group1">
                                                        <label class="form-group1__label"><?= htmlspecialcharsbx($propertyName) ?></label>
                                                        <select
                                                            class="select1 select-wide"
                                                            data-select1
                                                            data-sku-select="<?= $propertyId ?>"
                                                            name="sku_select_<?= $propertyId ?>"
                                                            autocomplete="off"
                                                        >
                                                            <?php foreach ($dimensionValues as $value): ?>
                                                                <option
                                                                    value="<?= (int)$value['id'] ?>"
                                                                    <?= $selectedValueId !== '' && $selectedValueId === (string)$value['id'] ? 'selected' : '' ?>
                                                                >
                                                                    <?= htmlspecialcharsbx((string)$value['name']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </fieldset>
                            <?php endif; ?>

                            <?php
                            if ($arParams['SHOW_MAX_QUANTITY'] !== 'N') {
                                if ($haveOffers) {
                                    ?>
                                    <div id="<?= $itemIds['QUANTITY_LIMIT'] ?>" style="display: none;">
                                        <span data-entity="quantity-limit-value"></span>
                                    </div>
                                    <?php
                                } elseif (
                                    $measureRatio
                                    && (float)$actualItem['PRODUCT']['QUANTITY'] > 0
                                    && $actualItem['CHECK_QUANTITY']
                                ) {
                                    ?>
                                    <div id="<?= $itemIds['QUANTITY_LIMIT'] ?>" style="display: none;">
                                        <span data-entity="quantity-limit-value">
                                            <?php
                                            if ($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                                                echo (float)$actualItem['PRODUCT']['QUANTITY'] / $measureRatio >= $arParams['RELATIVE_QUANTITY_FACTOR']
                                                    ? $arParams['MESS_RELATIVE_QUANTITY_MANY']
                                                    : $arParams['MESS_RELATIVE_QUANTITY_FEW'];
                                            } else {
                                                echo $actualItem['PRODUCT']['QUANTITY'] . ' ' . $actualItem['ITEM_MEASURE']['TITLE'];
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <?php
                                }
                            }
                            ?>

                            <div class="catalog-detail__param catalog-detail__param_amount">
                                <?php if (!$isPriceZero): ?>
                                    <table class="order-info">
                                        <?php if ($arParams['SHOW_OLD_PRICE'] === 'Y'): ?>
                                            <tr class="order-info__line1" data-old-price-row style="display: <?= $showDiscount ? '' : 'none' ?>;">
                                                <td>Старая цена (1 <?= htmlspecialcharsbx($actualItem['ITEM_MEASURE']['TITLE']) ?>)</td>
                                                <td>
                                                    <span class="order-info__value1" id="<?= $itemIds['OLD_PRICE_ID'] ?>">
                                                        <?= $showDiscount ? $price['PRINT_BASE_PRICE'] : '' ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr class="order-info__line2">
                                            <td>Новая цена (1 <?= htmlspecialcharsbx($actualItem['ITEM_MEASURE']['TITLE']) ?>)</td>
                                            <td>
                                                <span class="order-info__value2">
                                                    <span id="<?= $itemIds['PRICE_ID'] ?>"><?= $price['PRINT_PRICE'] ?></span>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="order-info__line3">
                                            <td>Общая стоимость:</td>
                                            <td>
                                                <span class="order-info__value3" id="<?= $itemIds['PRICE_TOTAL'] ?>">
                                                    на сумму <strong><?= $price['PRINT_RATIO_PRICE'] ?></strong>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                <?php else: ?>
                                    <table class="order-info">
                                        <tr class="order-info__line2">
                                            <td>Цена</td>
                                            <td><span class="order-info__value2"><span id="<?= $itemIds['PRICE_ID'] ?>">Цена по запросу</span></span></td>
                                        </tr>
                                    </table>
                                <?php endif; ?>

                                <span id="<?= $itemIds['DISCOUNT_PRICE_ID'] ?>" style="display: none;"></span>

                                <?php if ($arParams['USE_PRICE_COUNT']): ?>
                                    <?php $showRanges = !$haveOffers && count($actualItem['ITEM_QUANTITY_RANGES']) > 1; ?>
                                    <div data-entity="price-ranges-block" style="<?= $showRanges ? '' : 'display: none;' ?>">
                                        <?php $useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y'; ?>
                                        <span data-entity="price-ranges-ratio-header" style="display: none;">
                                            <?= Loc::getMessage(
                                                'CT_BCE_CATALOG_RATIO_PRICE',
                                                ['#RATIO#' => ($useRatio ? $measureRatio : '1') . ' ' . $actualItem['ITEM_MEASURE']['TITLE']]
                                            ) ?>
                                        </span>
                                        <dl data-entity="price-ranges-body" style="display: none;">
                                            <?php
                                            if ($showRanges) {
                                                foreach ($actualItem['ITEM_QUANTITY_RANGES'] as $range) {
                                                    if ($range['HASH'] === 'ZERO-INF') {
                                                        continue;
                                                    }

                                                    $itemPrice = false;
                                                    foreach ($arResult['ITEM_PRICES'] as $rangePrice) {
                                                        if ($rangePrice['QUANTITY_HASH'] === $range['HASH']) {
                                                            $itemPrice = $rangePrice;
                                                            break;
                                                        }
                                                    }

                                                    if (!$itemPrice) {
                                                        continue;
                                                    }
                                                    ?>
                                                    <dt>
                                                        <?php
                                                        echo Loc::getMessage(
                                                            'CT_BCE_CATALOG_RANGE_FROM',
                                                            ['#FROM#' => $range['SORT_FROM'] . ' ' . $actualItem['ITEM_MEASURE']['TITLE']]
                                                        ) . ' ';
                                                        echo is_infinite($range['SORT_TO'])
                                                            ? Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE')
                                                            : Loc::getMessage(
                                                                'CT_BCE_CATALOG_RANGE_TO',
                                                                ['#TO#' => $range['SORT_TO'] . ' ' . $actualItem['ITEM_MEASURE']['TITLE']]
                                                            );
                                                        ?>
                                                    </dt>
                                                    <dd><?= $useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE'] ?></dd>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </dl>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="catalog-detail__param catalog-detail__param_btns grid2">
                                <div class="catalog-detail__btns_top">
                                    <?php if ($arParams['USE_PRODUCT_QUANTITY']): ?>
                                        <div
                                            class="stepcounter"
                                            data-entity="quantity-block"
                                            style="<?= !$actualItem['CAN_BUY'] ? 'display: none;' : '' ?>"
                                        >
                                            <button class="stepcounter__btn" type="button" id="<?= $itemIds['QUANTITY_DOWN_ID'] ?>">
                                                <svg aria-hidden="true" width="14" height="2">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#minus2"></use>
                                                </svg>
                                                <span class="v-h">Уменьшить количество</span>
                                            </button>
                                            <input
                                                class="stepcounter__input"
                                                type="number"
                                                id="<?= $itemIds['QUANTITY_ID'] ?>"
                                                value="<?= $price['MIN_QUANTITY'] ?>"
                                                readonly
                                            >
                                            <button class="stepcounter__btn" type="button" id="<?= $itemIds['QUANTITY_UP_ID'] ?>">
                                                <svg aria-hidden="true" width="14" height="14">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#plus2"></use>
                                                </svg>
                                                <span class="v-h">Увеличить количество</span>
                                            </button>
                                            <span id="<?= $itemIds['QUANTITY_MEASURE'] ?>" style="display: none;">
                                                <?= $actualItem['ITEM_MEASURE']['TITLE'] ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <div data-entity="main-button-container">
                                        <div id="<?= $itemIds['BASKET_ACTIONS_ID'] ?>" style="<?= $actualItem['CAN_BUY'] ? '' : 'display: none;' ?>">
                                            <?php if ($showAddBtn): ?>
                                                <a
                                                    href="javascript:void(0);"
                                                    class="btn btn_small btn_black"
                                                    id="<?= $itemIds['ADD_BASKET_LINK'] ?>"
                                                    data-product-id="<?= $arResult['ID'] ?>"
                                                >
                                                    <svg width="16" height="18" aria-hidden="true">
                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#cart1"></use>
                                                    </svg>
                                                    <span>В корзину</span>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <a
                                    href="javascript:void(0);"
                                    class="btn btn_small btn_grey btn_wide"
                                    id="<?= $itemIds['NOT_AVAILABLE_MESS'] ?>"
                                    style="<?= $actualItem['CAN_BUY'] ? 'display: none;' : '' ?>"
                                    rel="nofollow"
                                >
                                    <?= $arParams['MESS_NOT_AVAILABLE'] ?>
                                </a>

                                <?php if ($actualItem['CAN_BUY']): ?>
                                    <button data-hystmodal="#modalBuy1Click" class="btn btn_small btn_grey btn_wide" type="button">
                                        Купить в 1 клик
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <button
                            class="catalog-detail__btn-favorite btn-favorites"
                            data-id="<?= $arResult['ID'] ?>"
                            type="button"
                            id="<?= $itemIds['COMPARE_LINK'] ?>"
                        >
                            <svg aria-hidden="true" width="22" height="20">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#heart1"></use>
                            </svg>
                        </button>

                        <?php if ($showSubscribe): ?>
                            <?php
                            $APPLICATION->IncludeComponent(
                                'bitrix:catalog.product.subscribe',
                                '',
                                array(
                                    'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
                                    'PRODUCT_ID' => $arResult['ID'],
                                    'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
                                    'BUTTON_CLASS' => 'btn btn_grey btn_wide',
                                    'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
                                    'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
                                ),
                                $component,
                                array('HIDE_ICONS' => 'Y')
                            );
                            ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="grid-detail__photo">
                    <div class="grid-detail__photo-inner">
                        <div class="catalog-detail__badges" id="<?= $itemIds['STICKER_ID'] ?>" <?= !$arResult['LABEL'] ? 'style="display: none;"' : '' ?>>
                            <?php
                            if ($arResult['LABEL'] && !empty($arResult['LABEL_ARRAY_VALUE'])) {
                                $arLabelsClass = [
                                    'NEWPRODUCT' => 'badge1 badge1_black',
                                    'SALELEADER' => 'badge1 badge1_orange',
                                    'SPECIALOFFER' => 'badge1 badge1_red',
                                ];
                                foreach ($arResult['LABEL_ARRAY_VALUE'] as $code => $value) {
                                    ?>
                                    <span class="<?= $arLabelsClass[$code] ?: 'badge1 badge1_black' ?>" title="<?= $value ?>">
                                        <?= $value ?>
                                    </span>
                                    <?php
                                }
                            }

                            if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y') {
                                if ($haveOffers) {
                                    ?>
                                    <span class="badge1 badge1_red" id="<?= $itemIds['DISCOUNT_PERCENT_ID'] ?>" style="display: none;"></span>
                                    <?php
                                } elseif ($price['DISCOUNT'] > 0) {
                                    ?>
                                    <span class="badge1 badge1_red" id="<?= $itemIds['DISCOUNT_PERCENT_ID'] ?>" title="<?= -$price['PERCENT'] ?>%">
                                        <?= -$price['PERCENT'] ?>%
                                    </span>
                                    <?php
                                }
                            }
                            ?>
                        </div>

                        <div class="swiper-holder">
                            <div class="swiper-photos-wrapper">
                                <div data-swiper="photos" class="swiper swiper-photos" id="<?= $itemIds['BIG_SLIDER_ID'] ?>">
                                    <div class="swiper-wrapper" data-entity="images-container">
                                        <?php if (!empty($actualItem['MORE_PHOTO'])): ?>
                                            <?php foreach ($actualItem['MORE_PHOTO'] as $key => $photo): ?>
                                                <a
                                                    href="<?= $photo['SRC'] ?>"
                                                    data-fancybox="photo-big"
                                                    class="swiper-slide"
                                                    data-entity="image"
                                                    data-id="<?= $photo['ID'] ?>"
                                                >
                                                    <img
                                                        src="<?= $photo['SRC'] ?>"
                                                        alt="<?= $alt ?>"
                                                        title="<?= $title ?>"<?= $key === 0 ? ' itemprop="image"' : '' ?>
                                                    >
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if ($showSliderControls): ?>
                                    <button class="swiper-nav swiper-nav_prev" type="button" data-entity="slider-control-left">
                                        <svg width="16" height="16" aria-hidden="true">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow2"></use>
                                        </svg>
                                        <span class="v-h">Назад</span>
                                    </button>
                                    <button class="swiper-nav swiper-nav_next" type="button" data-entity="slider-control-right">
                                        <svg width="16" height="16" aria-hidden="true">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow2"></use>
                                        </svg>
                                        <span class="v-h">Вперед</span>
                                    </button>
                                <?php endif; ?>
                                <div class="swiper-pagination"></div>
                            </div>

                            <div data-swiper="preview" class="swiper swiper-preview">
                                <div class="swiper-wrapper">
                                    <?php if (!empty($actualItem['MORE_PHOTO'])): ?>
                                        <?php foreach ($actualItem['MORE_PHOTO'] as $photo): ?>
                                            <div
                                                class="swiper-slide"
                                                data-entity="slider-control"
                                                data-value="<?= $photo['ID'] ?>"
                                            >
                                                <img src="<?= $photo['SRC'] ?>" alt="<?= $alt ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tabs2" data-tab="container" id="<?= $itemIds['TABS_ID'] ?>">
        <div class="tabs2-nav">
            <button data-action="tab1" data-alias="description" class="tabs2-nav__btn active" type="button">Описание</button>
            <button data-action="tab1" data-alias="specs" class="tabs2-nav__btn" type="button">Характеристики</button>
            <?php if ($hasSizeTable): ?>
                <button data-action="tab1" data-alias="sizes" class="tabs2-nav__btn" type="button">Таблица размеров</button>
            <?php endif; ?>
            <button data-action="tab1" data-alias="setup" class="tabs2-nav__btn" type="button">Монтаж</button>
            <button data-action="tab1" data-alias="delivery" class="tabs2-nav__btn" type="button">Доставка и оплата</button>
            <button data-action="tab1" data-alias="certificates" class="tabs2-nav__btn" type="button">Сертификаты</button>
        </div>

        <div id="<?= $itemIds['TAB_CONTAINERS_ID'] ?>">
            <div class="tabs2__content active" data-tab="content" data-alias="description" id="description">
                <div class="grid4">
                    <div>
                        <div class="textblock1" id="<?= $itemIds['DESCRIPTION_ID'] ?>">
                            <h2 class="title6">Описание</h2>
                            <?php
                            if ($arResult['DETAIL_TEXT'] != '') {
                                echo $arResult['DETAIL_TEXT_TYPE'] === 'html'
                                    ? $arResult['DETAIL_TEXT']
                                    : '<p>' . $arResult['~DETAIL_TEXT'] . '</p>';
                            }
                            ?>
                        </div>
                    </div>
                    <div>
                        <div class="rblock1">
                            <div class="textblock1">
                                <h3>Преимущества</h3>
                                <ul>
                                    <li>РАЗМЕЩЕНИЕ - устанавливаем люк, привязываясь к инженерным коммуникациям, а не к раскладке облицовочного материала.</li>
                                    <li>ПРАКТИЧНОСТЬ - установка стандартных размеров люка, которые всегда в наличии.</li>
                                    <li>УНИВЕРСАЛЬНОСТЬ - в люк можно инсталлировать любое покрытие: керамическую плитку, панели, зеркала и т.д.</li>
                                    <li>ИННОВАЦИИ - алюминиевый кант защищает отделочный материал от сколов в момент открывания люка, в отличие от других моделей.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tabs2__content" data-tab="content" data-alias="specs" id="specs">
                <h2 class="title6">Характеристики</h2>
                <div class="specs-list-wrapper">
                    <?php if (!empty($arResult['DISPLAY_PROPERTIES'])): ?>
                        <ul class="specs-list specs-list_columned">
                            <?php foreach ($arResult['DISPLAY_PROPERTIES'] as $property): ?>
                                <li class="specs-list__row">
                                    <div class="specs-list__key"><?= $property['NAME'] ?></div>
                                    <div class="specs-list__value">
                                        <?= is_array($property['DISPLAY_VALUE'])
                                            ? implode(' / ', $property['DISPLAY_VALUE'])
                                            : $property['DISPLAY_VALUE'] ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if ($arResult['SHOW_OFFERS_PROPS']): ?>
                        <ul class="specs-list specs-list_columned" id="<?= $itemIds['DISPLAY_PROP_DIV'] ?>"></ul>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($hasSizeTable): ?>
                <div class="tabs2__content tab-sizes" data-tab="content" data-alias="sizes">
                    <div class="grid5">
                        <h2 class="title6">Таблица размеров</h2>
                        <div class="color-items">
                            <span class="color-item">
                                <span class="color-item__color green"></span>
                                <span class="color-item__name">Стандартный размер</span>
                            </span>
                            <span class="color-item">
                                <span class="color-item__color"></span>
                                <span class="color-item__name">Нестандартный размер</span>
                            </span>
                        </div>
                    </div>
                    <div
                        class="table-variants"
                        data-size-table
                        data-width-prop-id="<?= $sizeTable['widthPropId'] ?>"
                        data-height-prop-id="<?= $sizeTable['heightPropId'] ?>"
                    >
                        <span class="table-variants__label"><span>Ширина, мм</span></span>
                        <span class="table-variants__label vertical"><span>Высота, мм</span></span>
                        <div class="table-variants__scroll">
                            <table>
                                <tr>
                                    <td></td>
                                    <?php foreach ($sizeTable['widths'] as $width): ?>
                                        <td><span class="table-variants__item"><?= htmlspecialcharsbx($width['name']) ?></span></td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php foreach ($sizeTable['heights'] as $heightId => $height): ?>
                                    <tr>
                                        <td><span class="table-variants__item"><?= htmlspecialcharsbx($height['name']) ?></span></td>
                                        <?php foreach ($sizeTable['widths'] as $widthId => $width): ?>
                                            <?php
                                            $cell = $sizeTable['cells'][$heightId][$widthId] ?? null;
                                            $isActiveSize = $cell !== null && (int)$cell['offerId'] === (int)$actualItem['ID'];
                                            ?>
                                            <td>
                                                <?php if ($cell !== null): ?>
                                                    <button
                                                        type="button"
                                                        class="table-variants__item green<?= $isActiveSize ? ' active' : '' ?>"
                                                        data-size-offer-id="<?= $cell['offerId'] ?>"
                                                        data-width-value-id="<?= $cell['widthValueId'] ?>"
                                                        data-height-value-id="<?= $cell['heightValueId'] ?>"
                                                    >
                                                        <?= $cell['price'] ?>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="table-variants__item disabled"></span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="tabs2__content tab-setup" data-tab="content" data-alias="setup">
                <h2 class="title6">Монтаж</h2>
                <div class="grid4">
                    <div>
                        <div class="mounting-title">Заголовок 1</div>
                        <a data-fancybox href="<?= SITE_TEMPLATE_PATH ?>/img/about.webp">
                            <img class="tab-setup__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/about.webp" alt="">
                        </a>
                    </div>
                    <div>
                        <div class="mounting-title">Заголовок 2</div>
                        <a data-fancybox href="<?= SITE_TEMPLATE_PATH ?>/img/category1.webp">
                            <img class="tab-setup__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/category1.webp" alt="">
                        </a>
                    </div>
                </div>
            </div>

            <div class="tabs2__content tab-delivery" data-tab="content" data-alias="delivery">
                <h2 class="title6">Доставка и оплата</h2>
                <div class="grid4">
                    <div class="rblock2">
                        <div class="textblock1">
                            <h3 class="title2">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/img/pic-delivery1.svg" alt="">
                                <span>Доставка</span>
                            </h3>
                            <p><strong>Экономьте время на получении заказа:</strong></p>
                            <ul>
                                <li>Курьерская доставка работает с 9:00 до 19:00. После поступления товара с вами свяжутся для согласования удобного времени и адреса.</li>
                                <li>Самовывоз из магазина. Когда заказ поступит на склад, вы получите уведомление и сможете забрать его в выбранной точке.</li>
                                <li>Постамат. После доставки на точку выдачи на телефон или e-mail придет код получения.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="rblock2">
                        <div class="textblock1">
                            <h3 class="title2">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/img/pic-payment1.svg" alt="">
                                <span>Оплата</span>
                            </h3>
                            <p><strong>Оплачивайте покупки удобным способом:</strong></p>
                            <ul>
                                <li>Наличными при самовывозе или доставке курьером.</li>
                                <li>Банковской картой при оформлении заказа на сайте или при получении.</li>
                                <li>Безналичным расчетом по выставленному счету.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tabs2__content tab-certificates" data-tab="content" data-alias="certificates">
                <h2 class="title6">Сертификаты</h2>
                <div class="grid4">
                    <a class="card-file" href="javascript:void(0);">
                        <span>Сертификат соответствия товара</span>
                        <svg width="21" height="21" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#download1"></use>
                        </svg>
                    </a>
                    <a class="card-file" href="javascript:void(0);">
                        <span>Паспорт изделия</span>
                        <svg width="21" height="21" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#download1"></use>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php
// Дополнительные компоненты - наборы товаров
if ($haveOffers) {
    if ($arResult['OFFER_GROUP']) {
        foreach ($arResult['OFFER_GROUP_VALUES'] as $offerId) {
            ?>
            <span id="<?= $itemIds['OFFER_GROUP'] . $offerId ?>" style="display: none;">
				<?php
                $APPLICATION->IncludeComponent(
                    'bitrix:catalog.set.constructor',
                    '.default',
                    array(
                        'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
                        'IBLOCK_ID' => $arResult['OFFERS_IBLOCK'],
                        'ELEMENT_ID' => $offerId,
                        'PRICE_CODE' => $arParams['PRICE_CODE'],
                        'BASKET_URL' => $arParams['BASKET_URL'],
                        'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
                        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                        'CACHE_TIME' => $arParams['CACHE_TIME'],
                        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                        'TEMPLATE_THEME' => $arParams['~TEMPLATE_THEME'],
                        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                        'CURRENCY_ID' => $arParams['CURRENCY_ID']
                    ),
                    $component,
                    array('HIDE_ICONS' => 'Y')
                );
                ?>
			</span>
            <?php
        }
    }
} else {
    if ($arResult['MODULES']['catalog'] && $arResult['OFFER_GROUP']) {
        $APPLICATION->IncludeComponent(
            'bitrix:catalog.set.constructor',
            '.default',
            array(
                'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
                'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                'ELEMENT_ID' => $arResult['ID'],
                'PRICE_CODE' => $arParams['PRICE_CODE'],
                'BASKET_URL' => $arParams['BASKET_URL'],
                'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                'CACHE_TIME' => $arParams['CACHE_TIME'],
                'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                'TEMPLATE_THEME' => $arParams['~TEMPLATE_THEME'],
                'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                'CURRENCY_ID' => $arParams['CURRENCY_ID']
            ),
            $component,
            array('HIDE_ICONS' => 'Y')
        );
    }
}

// Персональные рекомендации
if ($arResult['CATALOG'] && $actualItem['CAN_BUY'] && \Bitrix\Main\ModuleManager::isModuleInstalled('sale')) {
    $APPLICATION->IncludeComponent(
        'bitrix:sale.prediction.product.detail',
        '.default',
        array(
            'BUTTON_ID' => $showBuyBtn ? $itemIds['BUY_LINK'] : $itemIds['ADD_BASKET_LINK'],
            'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
            'POTENTIAL_PRODUCT_TO_BUY' => array(
                'ID' => $arResult['ID'] ?? null,
                'MODULE' => $arResult['MODULE'] ?? 'catalog',
                'PRODUCT_PROVIDER_CLASS' => $arResult['~PRODUCT_PROVIDER_CLASS'] ?? \Bitrix\Catalog\Product\Basket::getDefaultProviderName(),
                'QUANTITY' => $arResult['QUANTITY'] ?? null,
                'IBLOCK_ID' => $arResult['IBLOCK_ID'] ?? null,

                'PRIMARY_OFFER_ID' => $arResult['OFFERS'][0]['ID'] ?? null,
                'SECTION' => array(
                    'ID' => $arResult['SECTION']['ID'] ?? null,
                    'IBLOCK_ID' => $arResult['SECTION']['IBLOCK_ID'] ?? null,
                    'LEFT_MARGIN' => $arResult['SECTION']['LEFT_MARGIN'] ?? null,
                    'RIGHT_MARGIN' => $arResult['SECTION']['RIGHT_MARGIN'] ?? null,
                ),
            )
        ),
        $component,
        array('HIDE_ICONS' => 'Y')
    );
}

// Подарки к товару
if ($arResult['CATALOG'] && $arParams['USE_GIFTS_DETAIL'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale')) {
    ?>
    <div data-entity="parent-container">
        <?php
        if (!isset($arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE']) || $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'] !== 'Y') {
            ?>
            <div class="catalog-block-header" data-entity="header" data-showed="false"
                 style="display: none; opacity: 0;">
                <?= ($arParams['GIFTS_DETAIL_BLOCK_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_GIFT_BLOCK_TITLE_DEFAULT')) ?>
            </div>
            <?php
        }

        CBitrixComponent::includeComponentClass('bitrix:sale.products.gift');
        $APPLICATION->IncludeComponent(
            'bitrix:sale.products.gift',
            '.default',
            array(
                'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
                'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
                'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],

                'PRODUCT_ROW_VARIANTS' => "",
                'PAGE_ELEMENT_COUNT' => 0,
                'DEFERRED_PRODUCT_ROW_VARIANTS' => \Bitrix\Main\Web\Json::encode(
                    SaleProductsGiftComponent::predictRowVariants(
                        $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
                        $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT']
                    )
                ),
                'DEFERRED_PAGE_ELEMENT_COUNT' => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],

                'SHOW_DISCOUNT_PERCENT' => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
                'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
                'SHOW_OLD_PRICE' => $arParams['GIFTS_SHOW_OLD_PRICE'],
                'PRODUCT_DISPLAY_MODE' => 'Y',
                'PRODUCT_BLOCKS_ORDER' => $arParams['GIFTS_PRODUCT_BLOCKS_ORDER'],
                'SHOW_SLIDER' => $arParams['GIFTS_SHOW_SLIDER'],
                'SLIDER_INTERVAL' => $arParams['GIFTS_SLIDER_INTERVAL'] ?? '',
                'SLIDER_PROGRESS' => $arParams['GIFTS_SLIDER_PROGRESS'] ?? '',

                'TEXT_LABEL_GIFT' => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],

                'LABEL_PROP_' . $arParams['IBLOCK_ID'] => array(),
                'LABEL_PROP_MOBILE_' . $arParams['IBLOCK_ID'] => array(),
                'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],

                'ADD_TO_BASKET_ACTION' => ($arParams['ADD_TO_BASKET_ACTION'] ?? ''),
                'MESS_BTN_BUY' => $arParams['~GIFTS_MESS_BTN_BUY'],
                'MESS_BTN_ADD_TO_BASKET' => $arParams['~GIFTS_MESS_BTN_BUY'],
                'MESS_BTN_DETAIL' => $arParams['~MESS_BTN_DETAIL'],
                'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
                'MESS_BTN_COMPARE' => $arParams['~MESS_BTN_COMPARE'],
                'MESS_NOT_AVAILABLE' => $arParams['~MESS_NOT_AVAILABLE'],
                'MESS_SHOW_MAX_QUANTITY' => $arParams['~MESS_SHOW_MAX_QUANTITY'],
                'MESS_RELATIVE_QUANTITY_MANY' => $arParams['~MESS_RELATIVE_QUANTITY_MANY'],
                'MESS_RELATIVE_QUANTITY_FEW' => $arParams['~MESS_RELATIVE_QUANTITY_FEW'],

                'SHOW_PRODUCTS_' . $arParams['IBLOCK_ID'] => 'Y',
                'PROPERTY_CODE_' . $arParams['IBLOCK_ID'] => [],
                'PROPERTY_CODE_MOBILE' . $arParams['IBLOCK_ID'] => [],
                'PROPERTY_CODE_' . $arResult['OFFERS_IBLOCK'] => $arParams['OFFER_TREE_PROPS'],
                'OFFER_TREE_PROPS_' . $arResult['OFFERS_IBLOCK'] => $arParams['OFFER_TREE_PROPS'],
                'CART_PROPERTIES_' . $arResult['OFFERS_IBLOCK'] => $arParams['OFFERS_CART_PROPERTIES'],
                'ADDITIONAL_PICT_PROP_' . $arParams['IBLOCK_ID'] => ($arParams['ADD_PICT_PROP'] ?? ''),
                'ADDITIONAL_PICT_PROP_' . $arResult['OFFERS_IBLOCK'] => ($arParams['OFFER_ADD_PICT_PROP'] ?? ''),

                'HIDE_NOT_AVAILABLE' => 'Y',
                'HIDE_NOT_AVAILABLE_OFFERS' => 'Y',
                'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
                'PRICE_CODE' => $arParams['PRICE_CODE'],
                'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],
                'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
                'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                'BASKET_URL' => $arParams['BASKET_URL'],
                'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'],
                'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
                'PARTIAL_PRODUCT_PROPERTIES' => $arParams['PARTIAL_PRODUCT_PROPERTIES'],
                'USE_PRODUCT_QUANTITY' => 'N',
                'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                'POTENTIAL_PRODUCT_TO_BUY' => array(
                    'ID' => $arResult['ID'] ?? null,
                    'MODULE' => $arResult['MODULE'] ?? 'catalog',
                    'PRODUCT_PROVIDER_CLASS' => $arResult['~PRODUCT_PROVIDER_CLASS'] ?? \Bitrix\Catalog\Product\Basket::getDefaultProviderName(),
                    'QUANTITY' => $arResult['QUANTITY'] ?? null,
                    'IBLOCK_ID' => $arResult['IBLOCK_ID'] ?? null,

                    'PRIMARY_OFFER_ID' => $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'] ?? null,
                    'SECTION' => array(
                        'ID' => $arResult['SECTION']['ID'] ?? null,
                        'IBLOCK_ID' => $arResult['SECTION']['IBLOCK_ID'] ?? null,
                        'LEFT_MARGIN' => $arResult['SECTION']['LEFT_MARGIN'] ?? null,
                        'RIGHT_MARGIN' => $arResult['SECTION']['RIGHT_MARGIN'] ?? null,
                    ),
                ),

                'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
                'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
                'BRAND_PROPERTY' => $arParams['BRAND_PROPERTY']
            ),
            $component,
            array('HIDE_ICONS' => 'Y')
        );
        ?>
    </div>
    <?php
}

// Подарки к покупке
if ($arResult['CATALOG'] && $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale')) {
    ?>
    <div data-entity="parent-container">
        <?php
        if (!isset($arParams['GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE']) || $arParams['GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE'] !== 'Y') {
            ?>
            <div class="catalog-block-header" data-entity="header" data-showed="false"
                 style="display: none; opacity: 0;">
                <?= ($arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_GIFTS_MAIN_BLOCK_TITLE_DEFAULT')) ?>
            </div>
            <?php
        }

        $APPLICATION->IncludeComponent(
            'bitrix:sale.gift.main.products',
            '.default',
            array(
                'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
                'PAGE_ELEMENT_COUNT' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
                'LINE_ELEMENT_COUNT' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
                'HIDE_BLOCK_TITLE' => 'Y',
                'BLOCK_TITLE' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],

                'OFFERS_FIELD_CODE' => $arParams['OFFERS_FIELD_CODE'],
                'OFFERS_PROPERTY_CODE' => $arParams['OFFERS_PROPERTY_CODE'],

                'AJAX_MODE' => $arParams['AJAX_MODE'] ?? '',
                'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                'IBLOCK_ID' => $arParams['IBLOCK_ID'],

                'ELEMENT_SORT_FIELD' => 'ID',
                'ELEMENT_SORT_ORDER' => 'DESC',
                'FILTER_NAME' => 'searchFilter',
                'SECTION_URL' => $arParams['SECTION_URL'],
                'DETAIL_URL' => $arParams['DETAIL_URL'],
                'BASKET_URL' => $arParams['BASKET_URL'],
                'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
                'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
                'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],

                'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                'CACHE_TIME' => $arParams['CACHE_TIME'],

                'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                'SET_TITLE' => $arParams['SET_TITLE'],
                'PROPERTY_CODE' => $arParams['PROPERTY_CODE'],
                'PRICE_CODE' => $arParams['PRICE_CODE'],
                'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
                'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],

                'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
                'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                'HIDE_NOT_AVAILABLE' => 'Y',
                'HIDE_NOT_AVAILABLE_OFFERS' => 'Y',
                'TEMPLATE_THEME' => ($arParams['TEMPLATE_THEME'] ?? ''),
                'PRODUCT_BLOCKS_ORDER' => $arParams['GIFTS_PRODUCT_BLOCKS_ORDER'],

                'SHOW_SLIDER' => $arParams['GIFTS_SHOW_SLIDER'],
                'SLIDER_INTERVAL' => $arParams['GIFTS_SLIDER_INTERVAL'] ?? '',
                'SLIDER_PROGRESS' => $arParams['GIFTS_SLIDER_PROGRESS'] ?? '',

                'ADD_PICT_PROP' => ($arParams['ADD_PICT_PROP'] ?? ''),
                'LABEL_PROP' => ($arParams['LABEL_PROP'] ?? ''),
                'LABEL_PROP_MOBILE' => ($arParams['LABEL_PROP_MOBILE'] ?? ''),
                'LABEL_PROP_POSITION' => ($arParams['LABEL_PROP_POSITION'] ?? ''),
                'OFFER_ADD_PICT_PROP' => ($arParams['OFFER_ADD_PICT_PROP'] ?? ''),
                'OFFER_TREE_PROPS' => ($arParams['OFFER_TREE_PROPS'] ?? ''),
                'SHOW_DISCOUNT_PERCENT' => ($arParams['SHOW_DISCOUNT_PERCENT'] ?? ''),
                'DISCOUNT_PERCENT_POSITION' => ($arParams['DISCOUNT_PERCENT_POSITION'] ?? ''),
                'SHOW_OLD_PRICE' => ($arParams['SHOW_OLD_PRICE'] ?? ''),
                'MESS_BTN_BUY' => ($arParams['~MESS_BTN_BUY'] ?? ''),
                'MESS_BTN_ADD_TO_BASKET' => ($arParams['~MESS_BTN_ADD_TO_BASKET'] ?? ''),
                'MESS_BTN_DETAIL' => ($arParams['~MESS_BTN_DETAIL'] ?? ''),
                'MESS_NOT_AVAILABLE' => ($arParams['~MESS_NOT_AVAILABLE'] ?? ''),
                'ADD_TO_BASKET_ACTION' => ($arParams['ADD_TO_BASKET_ACTION'] ?? ''),
                'SHOW_CLOSE_POPUP' => ($arParams['SHOW_CLOSE_POPUP'] ?? ''),
                'DISPLAY_COMPARE' => ($arParams['DISPLAY_COMPARE'] ?? ''),
                'COMPARE_PATH' => ($arParams['COMPARE_PATH'] ?? ''),
            )
            + array(
                'OFFER_ID' => empty($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'])
                    ? $arResult['ID']
                    : $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'],
                'SECTION_ID' => $arResult['SECTION']['ID'],
                'ELEMENT_ID' => $arResult['ID'],

                'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
                'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
                'BRAND_PROPERTY' => $arParams['BRAND_PROPERTY']
            ),
            $component,
            array('HIDE_ICONS' => 'Y')
        );
        ?>
    </div>
    <?php
}
?>

    <div class="product-item-detail-short-card-fixed hidden-xs" id="<?= $itemIds['SMALL_CARD_PANEL_ID'] ?>"
         style="display: none;">
        <div class="product-item-detail-short-card-content-container">
            <table>
                <tr>
                    <td rowspan="2" class="product-item-detail-short-card-image">
                        <img src="" data-entity="panel-picture">
                    </td>
                    <td class="product-item-detail-short-title-container" data-entity="panel-title">
                        <span class="product-item-detail-short-title-text"><?= $name ?></span>
                    </td>
                    <td rowspan="2" class="product-item-detail-short-card-price">
                        <?php
                        if ($arParams['SHOW_OLD_PRICE'] === 'Y') {
                            ?>
                            <div class="product-item-detail-price-old"
                                 style="display: <?= ($showDiscount ? '' : 'none') ?>;"
                                 data-entity="panel-old-price">
                                <?= ($showDiscount ? $price['PRINT_RATIO_BASE_PRICE'] : '') ?>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="product-item-detail-price-current" data-entity="panel-price">
                            <?= $price['PRINT_RATIO_PRICE'] ?>
                        </div>
                    </td>
                    <?php
                    if ($showAddBtn) {
                        ?>
                        <td rowspan="2" class="product-item-detail-short-card-btn"
                            style="display: <?= ($actualItem['CAN_BUY'] ? '' : 'none') ?>;"
                            data-entity="panel-add-button">
                            <a class="btn <?= $showButtonClassName ?> product-item-detail-buy-button"
                               id="<?= $itemIds['ADD_BASKET_LINK'] ?>"
                               href="javascript:void(0);">
                                <span><?= $arParams['MESS_BTN_ADD_TO_BASKET'] ?></span>
                            </a>
                        </td>
                        <?php
                    }

                    if ($showBuyBtn) {
                        ?>
                        <td rowspan="2" class="product-item-detail-short-card-btn"
                            style="display: <?= ($actualItem['CAN_BUY'] ? '' : 'none') ?>;"
                            data-entity="panel-buy-button">
                            <a class="btn <?= $buyButtonClassName ?> product-item-detail-buy-button"
                               id="<?= $itemIds['BUY_LINK'] ?>"
                               href="javascript:void(0);">
                                <span><?= $arParams['MESS_BTN_BUY'] ?></span>
                            </a>
                        </td>
                        <?php
                    }
                    ?>
                    <td rowspan="2" class="product-item-detail-short-card-btn"
                        style="display: <?= (!$actualItem['CAN_BUY'] ? '' : 'none') ?>;"
                        data-entity="panel-not-available-button">
                        <a class="btn btn-link product-item-detail-buy-button" href="javascript:void(0)"
                           rel="nofollow">
                            <?= $arParams['MESS_NOT_AVAILABLE'] ?>
                        </a>
                    </td>
                </tr>
                <?php
                if ($haveOffers) {
                    ?>
                    <tr>
                        <td>
                            <div class="product-item-selected-scu-container" data-entity="panel-sku-container">
                                <?php
                                $i = 0;

                                foreach ($arResult['SKU_PROPS'] as $skuProperty) {
                                    if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']])) {
                                        continue;
                                    }

                                    $propertyId = $skuProperty['ID'];

                                    foreach ($skuProperty['VALUES'] as $value) {
                                        $value['NAME'] = htmlspecialcharsbx($value['NAME']);
                                        if ($skuProperty['SHOW_MODE'] === 'PICT') {
                                            ?>
                                            <div class="product-item-selected-scu product-item-selected-scu-color selected"
                                                 title="<?= $value['NAME'] ?>"
                                                 style="background-image: url('<?= $value['PICT']['SRC'] ?>'); display: none;"
                                                 data-sku-line="<?= $i ?>"
                                                 data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>"
                                                 data-onevalue="<?= $value['ID'] ?>">
                                            </div>
                                            <?php
                                        } else {
                                            ?>
                                            <div class="product-item-selected-scu product-item-selected-scu-text selected"
                                                 title="<?= $value['NAME'] ?>"
                                                 style="display: none;"
                                                 data-sku-line="<?= $i ?>"
                                                 data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>"
                                                 data-onevalue="<?= $value['ID'] ?>">
                                                <?= $value['NAME'] ?>
                                            </div>
                                            <?php
                                        }
                                    }

                                    $i++;
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>

    <div class="product-item-detail-tabs-container-fixed hidden-xs" id="<?= $itemIds['TABS_PANEL_ID'] ?>"
         style="display: none;">
        <ul class="product-item-detail-tabs-list">
            <?php
            if ($showDescription) {
                ?>
                <li class="product-item-detail-tab active" data-entity="tab" data-value="description">
                    <a href="javascript:void(0);" class="product-item-detail-tab-link">
                        <span><?= $arParams['MESS_DESCRIPTION_TAB'] ?></span>
                    </a>
                </li>
                <?php
            }

            if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS']) {
                ?>
                <li class="product-item-detail-tab" data-entity="tab" data-value="properties">
                    <a href="javascript:void(0);" class="product-item-detail-tab-link">
                        <span><?= $arParams['MESS_PROPERTIES_TAB'] ?></span>
                    </a>
                </li>
                <?php
            }

            if ($arParams['USE_COMMENTS'] === 'Y') {
                ?>
                <li class="product-item-detail-tab" data-entity="tab" data-value="comments">
                    <a href="javascript:void(0);" class="product-item-detail-tab-link">
                        <span><?= $arParams['MESS_COMMENTS_TAB'] ?></span>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>

<?php
// Скрытые данные для корзины
$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
if (!$haveOffers && $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !$emptyProductProperties) {
    ?>
    <div id="<?= $itemIds['BASKET_PROP_DIV'] ?>" style="display: none;">
        <?php
        if (!empty($arResult['PRODUCT_PROPERTIES_FILL'])) {
            foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propId => $propInfo) {
                ?>
                <input type="hidden" name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]"
                       value="<?= htmlspecialcharsbx($propInfo['ID']) ?>">
                <?php
                unset($arResult['PRODUCT_PROPERTIES'][$propId]);
            }
        }

        $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
        if (!$emptyProductProperties) {
            ?>
            <table>
                <?php
                foreach ($arResult['PRODUCT_PROPERTIES'] as $propId => $propInfo) {
                    ?>
                    <tr>
                        <td><?= $arResult['PROPERTIES'][$propId]['NAME'] ?></td>
                        <td>
                            <?php
                            if (
                                $arResult['PROPERTIES'][$propId]['PROPERTY_TYPE'] === 'L'
                                && $arResult['PROPERTIES'][$propId]['LIST_TYPE'] === 'C'
                            ) {
                                foreach ($propInfo['VALUES'] as $valueId => $value) {
                                    ?>
                                    <label>
                                        <input type="radio"
                                               name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]"
                                               value="<?= $valueId ?>" <?= ($valueId == $propInfo['SELECTED'] ? 'checked' : '') ?>>
                                        <?= $value ?>
                                    </label>
                                    <br>
                                    <?php
                                }
                            } else {
                                ?>
                                <select name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]">
                                    <?php
                                    foreach ($propInfo['VALUES'] as $valueId => $value) {
                                        ?>
                                        <option value="<?= $valueId ?>" <?= ($valueId == $propInfo['SELECTED'] ? 'selected' : '') ?>>
                                            <?= $value ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
        }
        ?>
    </div>
    <?php
}
?>

    <meta itemprop="name" content="<?= $name ?>"/>
    <meta itemprop="category" content="<?= $arResult['CATEGORY_PATH'] ?>"/>
<?php
if ($haveOffers) {
    foreach ($arResult['JS_OFFERS'] as $offer) {
        $currentOffersList = array();

        if (!empty($offer['TREE']) && is_array($offer['TREE'])) {
            foreach ($offer['TREE'] as $propName => $skuId) {
                $propId = (int)mb_substr($propName, 5);

                foreach ($skuProps as $prop) {
                    if ($prop['ID'] == $propId) {
                        foreach ($prop['VALUES'] as $propId => $propValue) {
                            if ($propId == $skuId) {
                                $currentOffersList[] = $propValue['NAME'];
                                break;
                            }
                        }
                    }
                }
            }
        }

        $offerPrice = $offer['ITEM_PRICES'][$offer['ITEM_PRICE_SELECTED']];
        ?>
        <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<meta itemprop="sku" content="<?= htmlspecialcharsbx(implode('/', $currentOffersList)) ?>"/>
			<meta itemprop="price" content="<?= $offerPrice['RATIO_PRICE'] ?>"/>
			<meta itemprop="priceCurrency" content="<?= $offerPrice['CURRENCY'] ?>"/>
			<link itemprop="availability"
                  href="http://schema.org/<?= ($offer['CAN_BUY'] ? 'InStock' : 'OutOfStock') ?>"/>
		</span>
        <?php
    }

    unset($offerPrice, $currentOffersList);
} else {
    ?>
    <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
		<meta itemprop="price" content="<?= $price['RATIO_PRICE'] ?>"/>
		<meta itemprop="priceCurrency" content="<?= $price['CURRENCY'] ?>"/>
		<link itemprop="availability"
              href="http://schema.org/<?= ($actualItem['CAN_BUY'] ? 'InStock' : 'OutOfStock') ?>"/>
	</span>
    <?php
}
?>

<?php if ($actualItem['CAN_BUY']): ?>
<div class="hystmodal" id="modalBuy1Click" aria-hidden="true">
        <div class="hystmodal__wrap">
            <div class="hystmodal__window" role="dialog" aria-modal="true">
                <div class="hystmodal__inner">
                    <h2 class="hystmodal__title">Купить в 1 клик</h2>
                    <div class="hystmodal__product">
                        <?php
                        // Получаем изображение с приоритетом: превью -> детальное -> из доп.картинок -> заглушка
                        $productImage = '';
                        if (!empty($actualItem['PREVIEW_PICTURE']['SRC']))
                        {
                            $productImage = $actualItem['PREVIEW_PICTURE']['SRC'];
                        }
                        elseif (!empty($actualItem['DETAIL_PICTURE']['SRC']))
                        {
                            $productImage = $actualItem['DETAIL_PICTURE']['SRC'];
                        }
                        elseif (!empty($actualItem['MORE_PHOTO'][0]['SRC']))
                        {
                            $productImage = $actualItem['MORE_PHOTO'][0]['SRC'];
                        }
                        else
                        {
                            $productImage = SITE_TEMPLATE_PATH.'/img/no-photo.png';
                        }

                        $productDate = [
                            'PRODUCT_ID'    => $arResult['ID'],
                            'PRODUCT_NAME'  => htmlspecialcharsbx($name),
                            'PRODUCT_URL'   => $arResult['DETAIL_PAGE_URL'],
                        ];
                        ?>
                        <img src="<?=$productImage?>" alt="<?=htmlspecialcharsbx($name)?>">
                        <div><?=htmlspecialcharsbx($name)?></div>
                    </div>
                    <?
                    $APPLICATION->IncludeComponent(
                        "acroweb:universal.form",
                        "one_click",
                        [
                            "FORM_SID" => "acroweb_one_click_s1",
                            "PRODUCT_DATA" => $productDate,
                            "AJAX" => "Y",
                        ]
                    );
                    ?>
                </div>
                <button data-hystclose class="hystmodal__close">
                    <svg aria-hidden="true" width="20" height="20">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#close1"></use>
                    </svg>
                    <span class="v-h">Закрыть</span>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
// JS параметры
if ($haveOffers) {
    $offerIds = array();
    $offerCodes = array();

    $useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';

    foreach ($arResult['JS_OFFERS'] as $ind => &$jsOffer) {
        $offerIds[] = (int)$jsOffer['ID'];
        $offerCodes[] = $jsOffer['CODE'];

        $fullOffer = $arResult['OFFERS'][$ind];
        $measureName = $fullOffer['ITEM_MEASURE']['TITLE'];

        $strAllProps = '';
        $strMainProps = '';
        $strPriceRangesRatio = '';
        $strPriceRanges = '';

        if ($arResult['SHOW_OFFERS_PROPS']) {
            if (!empty($jsOffer['DISPLAY_PROPERTIES'])) {
                foreach ($jsOffer['DISPLAY_PROPERTIES'] as $property) {
                    $current = '<li class="specs-list__row"><div class="specs-list__key">' . $property['NAME'] . '</div><div class="specs-list__value">' . (
                        is_array($property['VALUE'])
                            ? implode(' / ', $property['VALUE'])
                            : $property['VALUE']
                        ) . '</div></li>';
                    $strAllProps .= $current;

                    if (isset($arParams['MAIN_BLOCK_OFFERS_PROPERTY_CODE'][$property['CODE']])) {
                        $strMainProps .= $current;
                    }
                }

                unset($current);
            }
        }

        if ($arParams['USE_PRICE_COUNT'] && count($jsOffer['ITEM_QUANTITY_RANGES']) > 1) {
            $strPriceRangesRatio = '(' . Loc::getMessage(
                    'CT_BCE_CATALOG_RATIO_PRICE',
                    array('#RATIO#' => ($useRatio
                            ? $fullOffer['ITEM_MEASURE_RATIOS'][$fullOffer['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']
                            : '1'
                        ) . ' ' . $measureName)
                ) . ')';

            foreach ($jsOffer['ITEM_QUANTITY_RANGES'] as $range) {
                if ($range['HASH'] !== 'ZERO-INF') {
                    $itemPrice = false;

                    foreach ($jsOffer['ITEM_PRICES'] as $itemPrice) {
                        if ($itemPrice['QUANTITY_HASH'] === $range['HASH']) {
                            break;
                        }
                    }

                    if ($itemPrice) {
                        $strPriceRanges .= '<dt>' . Loc::getMessage(
                                'CT_BCE_CATALOG_RANGE_FROM',
                                array('#FROM#' => $range['SORT_FROM'] . ' ' . $measureName)
                            ) . ' ';

                        if (is_infinite($range['SORT_TO'])) {
                            $strPriceRanges .= Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
                        } else {
                            $strPriceRanges .= Loc::getMessage(
                                'CT_BCE_CATALOG_RANGE_TO',
                                array('#TO#' => $range['SORT_TO'] . ' ' . $measureName)
                            );
                        }

                        $strPriceRanges .= '</dt><dd>' . ($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE']) . '</dd>';
                    }
                }
            }

            unset($range, $itemPrice);
        }

        $jsOffer['ARTICLE'] = $getArticleValue($fullOffer) ?: $artic;
        $jsOffer['DISPLAY_PROPERTIES'] = $strAllProps;
        $jsOffer['DISPLAY_PROPERTIES_MAIN_BLOCK'] = $strMainProps;
        $jsOffer['PRICE_RANGES_RATIO_HTML'] = $strPriceRangesRatio;
        $jsOffer['PRICE_RANGES_HTML'] = $strPriceRanges;
    }

    $templateData['OFFER_IDS'] = $offerIds;
    $templateData['OFFER_CODES'] = $offerCodes;
    unset($jsOffer, $strAllProps, $strMainProps, $strPriceRanges, $strPriceRangesRatio, $useRatio);

    $jsParams = array(
        'CONFIG' => array(
            'USE_CATALOG' => $arResult['CATALOG'],
            'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
            'SHOW_PRICE' => true,
            'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
            'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
            'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
            'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
            'SHOW_SKU_PROPS' => $arResult['SHOW_OFFERS_PROPS'],
            'OFFER_GROUP' => $arResult['OFFER_GROUP'],
            'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
            'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
            'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
            'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
            'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
            'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
            'USE_STICKERS' => true,
            'USE_SUBSCRIBE' => $showSubscribe,
            'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
            'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
            'ALT' => $alt,
            'TITLE' => $title,
            'MAGNIFIER_ZOOM_PERCENT' => 200,
            'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
            'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
            'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
                ? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
                : null,
            'SHOW_SKU_DESCRIPTION' => $arParams['SHOW_SKU_DESCRIPTION'],
            'DISPLAY_PREVIEW_TEXT_MODE' => $arParams['DISPLAY_PREVIEW_TEXT_MODE']
        ),
        'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
        'VISUAL' => $itemIds,
        'DEFAULT_PICTURE' => array(
            'PREVIEW_PICTURE' => $arResult['DEFAULT_PICTURE'],
            'DETAIL_PICTURE' => $arResult['DEFAULT_PICTURE']
        ),
        'PRODUCT' => array(
            'ID' => $arResult['ID'],
            'ACTIVE' => $arResult['ACTIVE'],
            'NAME' => $arResult['~NAME'],
            'CATEGORY' => $arResult['CATEGORY_PATH'],
            'DETAIL_TEXT' => $arResult['DETAIL_TEXT'],
            'DETAIL_TEXT_TYPE' => $arResult['DETAIL_TEXT_TYPE'],
            'PREVIEW_TEXT' => $arResult['PREVIEW_TEXT'],
            'PREVIEW_TEXT_TYPE' => $arResult['PREVIEW_TEXT_TYPE']
        ),
        'BASKET' => array(
            'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
            'BASKET_URL' => $arParams['BASKET_URL'],
            'SKU_PROPS' => $arResult['OFFERS_PROP_CODES'],
            'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
            'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
        ),
        'OFFERS' => $arResult['JS_OFFERS'],
        'OFFER_SELECTED' => $arResult['OFFERS_SELECTED'],
        'TREE_PROPS' => $skuProps
    );
} else {
    $jsParams = array(
        'CONFIG' => array(
            'USE_CATALOG' => $arResult['CATALOG'],
            'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
            'SHOW_PRICE' => !empty($arResult['ITEM_PRICES']),
            'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
            'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
            'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
            'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
            'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
            'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
            'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
            'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
            'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
            'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
            'USE_STICKERS' => true,
            'USE_SUBSCRIBE' => $showSubscribe,
            'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
            'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
            'ALT' => $alt,
            'TITLE' => $title,
            'MAGNIFIER_ZOOM_PERCENT' => 200,
            'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
            'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
            'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
                ? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
                : null
        ),
        'VISUAL' => $itemIds,
        'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
        'PRODUCT' => array(
            'ID' => $arResult['ID'],
            'ACTIVE' => $arResult['ACTIVE'],
            'PICT' => reset($arResult['MORE_PHOTO']),
            'NAME' => $arResult['~NAME'],
            'SUBSCRIPTION' => true,
            'ITEM_PRICE_MODE' => $arResult['ITEM_PRICE_MODE'],
            'ITEM_PRICES' => $arResult['ITEM_PRICES'],
            'ITEM_PRICE_SELECTED' => $arResult['ITEM_PRICE_SELECTED'],
            'ITEM_QUANTITY_RANGES' => $arResult['ITEM_QUANTITY_RANGES'],
            'ITEM_QUANTITY_RANGE_SELECTED' => $arResult['ITEM_QUANTITY_RANGE_SELECTED'],
            'ITEM_MEASURE_RATIOS' => $arResult['ITEM_MEASURE_RATIOS'],
            'ITEM_MEASURE_RATIO_SELECTED' => $arResult['ITEM_MEASURE_RATIO_SELECTED'],
            'SLIDER_COUNT' => $arResult['MORE_PHOTO_COUNT'],
            'SLIDER' => $arResult['MORE_PHOTO'],
            'CAN_BUY' => $arResult['CAN_BUY'],
            'CHECK_QUANTITY' => $arResult['CHECK_QUANTITY'],
            'QUANTITY_FLOAT' => is_float($arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']),
            'MAX_QUANTITY' => $arResult['PRODUCT']['QUANTITY'],
            'STEP_QUANTITY' => $arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'],
            'CATEGORY' => $arResult['CATEGORY_PATH']
        ),
        'BASKET' => array(
            'ADD_PROPS' => $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y',
            'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
            'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
            'EMPTY_PROPS' => $emptyProductProperties,
            'BASKET_URL' => $arParams['BASKET_URL'],
            'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
            'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
        )
    );
    unset($emptyProductProperties);
}

if ($arParams['DISPLAY_COMPARE']) {
    $jsParams['COMPARE'] = array(
        'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
        'COMPARE_DELETE_URL_TEMPLATE' => $arResult['~COMPARE_DELETE_URL_TEMPLATE'],
        'COMPARE_PATH' => $arParams['COMPARE_PATH']
    );
}

$jsParams["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"] =
    $arResult["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"];

?>
    <script>
        BX.message({
            ECONOMY_INFO_MESSAGE: '<?=GetMessageJS('CT_BCE_CATALOG_ECONOMY_INFO2')?>',
            TITLE_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR')?>',
            TITLE_BASKET_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS')?>',
            BASKET_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR')?>',
            BTN_SEND_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS')?>',
            BTN_MESSAGE_DETAIL_BASKET_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_BASKET_REDIRECT')?>',
            BTN_MESSAGE_CLOSE: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE')?>',
            BTN_MESSAGE_DETAIL_CLOSE_POPUP: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE_POPUP')?>',
            TITLE_SUCCESSFUL: '<?=GetMessageJS('CT_BCE_CATALOG_ADD_TO_BASKET_OK')?>',
            COMPARE_MESSAGE_OK: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_OK')?>',
            COMPARE_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_UNKNOWN_ERROR')?>',
            COMPARE_TITLE: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_TITLE')?>',
            BTN_MESSAGE_COMPARE_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT')?>',
            PRODUCT_GIFT_LABEL: '<?=GetMessageJS('CT_BCE_CATALOG_PRODUCT_GIFT_LABEL')?>',
            PRICE_TOTAL_PREFIX: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_PRICE_TOTAL_PREFIX')?>',
            RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY'])?>',
            RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW'])?>',
            SITE_ID: '<?=CUtil::JSEscape($component->getSiteId())?>'
        });

        var <?=$obName?> = new JCCatalogElement(<?=CUtil::PhpToJSObject($jsParams, false, true)?>);

        (function () {
            var root = document.getElementById('<?=CUtil::JSEscape($itemIds['ID'])?>');
            var tabsRoot = document.getElementById('<?=CUtil::JSEscape($itemIds['TABS_ID'])?>');
            var hiddenTree = document.getElementById('<?=CUtil::JSEscape($itemIds['TREE_ID'])?>');
            var articleNode = document.getElementById('<?=CUtil::JSEscape($itemIds['ARTICLE_ID'])?>');
            var oldPriceRow = root ? root.querySelector('[data-old-price-row]') : null;
            var sizeTableNode = tabsRoot ? tabsRoot.querySelector('[data-size-table]') : null;
            var sizeWidthPropId = sizeTableNode ? String(sizeTableNode.getAttribute('data-width-prop-id') || '') : '';
            var sizeHeightPropId = sizeTableNode ? String(sizeTableNode.getAttribute('data-height-prop-id') || '') : '';
            var offersById = {};
            var offerIndexById = {};

            if (!root || !hiddenTree || !<?=$obName?>.offers) {
                return;
            }

            <?=$obName?>.offers.forEach(function (offer) {
                offersById[String(offer.ID)] = offer;
            });

            <?=$obName?>.offers.forEach(function (offer, index) {
                offerIndexById[String(offer.ID)] = index;
            });

            function setSelectValue(select, value, silent) {
                if (!select) {
                    return;
                }

                silent = silent === true;

                if (select.tomselect) {
                    select.tomselect.setValue(String(value), silent);
                    return;
                }

                select.value = String(value);
            }

            function clickHiddenTreeItem(treeValue) {
                var hiddenItem = hiddenTree.querySelector('[data-treevalue="' + treeValue + '"]');
                if (hiddenItem) {
                    hiddenItem.click();
                }
            }

            function syncArticle(offer) {
                if (!articleNode) {
                    return;
                }

                if (offer && offer.ARTICLE) {
                    articleNode.style.display = '';
                    articleNode.textContent = 'Артикул: ' + offer.ARTICLE;
                    return;
                }

                articleNode.style.display = 'none';
                articleNode.textContent = '';
            }

            function syncOldPriceRow(offer) {
                if (!oldPriceRow || !offer || !offer.ITEM_PRICES || !offer.ITEM_PRICES[offer.ITEM_PRICE_SELECTED]) {
                    return;
                }

                var offerPrice = offer.ITEM_PRICES[offer.ITEM_PRICE_SELECTED];
                oldPriceRow.style.display = offerPrice.PRICE !== offerPrice.BASE_PRICE ? '' : 'none';
            }

            function syncSizeTable(offerId) {
                if (!sizeTableNode) {
                    return;
                }

                sizeTableNode.querySelectorAll('[data-size-offer-id]').forEach(function (cell) {
                    cell.classList.toggle('active', cell.getAttribute('data-size-offer-id') === String(offerId));
                });
            }

            function selectSizeOffer(cell) {
                var offerId;
                var offerIndex;
                var rootTop;

                if (!cell) {
                    return;
                }

                offerId = String(cell.getAttribute('data-size-offer-id') || '');
                offerIndex = offerIndexById[offerId];

                if (!offerId || offerIndex === undefined) {
                    return;
                }

                <?=$obName?>.setOffer(offerIndex);

                rootTop = Math.max(root.getBoundingClientRect().top + window.pageYOffset - 20, 0);
                window.scrollTo({ top: rootTop, behavior: 'smooth' });
            }

            function syncVisibleControls(offerId) {
                var offer = offersById[String(offerId)];

                if (!offer || !offer.TREE) {
                    return;
                }

                Object.keys(offer.TREE).forEach(function (propCode) {
                    var propId = propCode.replace('PROP_', '');
                    var valueId = String(offer.TREE[propCode]);
                    var radio = root.querySelector('[data-sku-radio="' + propId + '_' + valueId + '"]');
                    var select = root.querySelector('[data-sku-select="' + propId + '"]');

                    if (radio) {
                        radio.checked = true;
                    }

                    if (select) {
                        setSelectValue(select, valueId, true);
                    }
                });

                syncArticle(offer);
                syncOldPriceRow(offer);
                syncSizeTable(offerId);
            }

            root.querySelectorAll('[data-sku-radio]').forEach(function (input) {
                input.addEventListener('change', function () {
                    if (this.checked) {
                        clickHiddenTreeItem(this.getAttribute('data-sku-radio'));
                    }
                });
            });

            root.querySelectorAll('[data-sku-select]').forEach(function (select) {
                select.addEventListener('change', function () {
                    clickHiddenTreeItem(this.getAttribute('data-sku-select') + '_' + this.value);
                });
            });

            if (sizeTableNode) {
                sizeTableNode.addEventListener('click', function (event) {
                    var cell = event.target.closest('[data-size-offer-id]');

                    if (!cell || !sizeTableNode.contains(cell)) {
                        return;
                    }

                    event.preventDefault();
                    selectSizeOffer(cell);
                });
            }

            if (<?=$obName?>.offers[<?=$obName?>.offerNum]) {
                syncVisibleControls(<?=$obName?>.offers[<?=$obName?>.offerNum].ID);
            }

            BX.addCustomEvent('onCatalogElementChangeOffer', function (eventData) {
                if (!eventData || !eventData.newId || !offersById[String(eventData.newId)]) {
                    return;
                }

                syncVisibleControls(eventData.newId);
            });
        })();
    </script>
<?php
unset($actualItem, $itemIds, $jsParams);
