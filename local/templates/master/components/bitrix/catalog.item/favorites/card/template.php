<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $item
 * @var array $actualItem
 * @var array $minOffer
 * @var array $itemIds
 * @var array|null $price
 * @var float|int|null $measureRatio
 * @var bool $haveOffers
 * @var bool $showSubscribe
 * @var array $morePhoto
 * @var bool $showSlider
 * @var bool $itemHasDetailUrl
 * @var string $imgTitle
 * @var string $productTitle
 * @var string $buttonSizeClass
 * @var string $discountPositionClass
 * @var string $labelPositionClass
 * @var CatalogSectionComponent $component
 */
?>
        <div class="card-product__inner">
            <? if ($itemHasDetailUrl): ?>
            <a href="<?= $item['DETAIL_PAGE_URL'] ?>" class="card-product__photo" title="<?= $imgTitle ?>"
               data-entity="image-wrapper">
                <? else: ?>
                <span class="card-product__photo" data-entity="image-wrapper">
        <? endif; ?>
            <!-- Скрытые элементы слайдера для возможности включения слайдера -->
            <span class="product-item-image-slider-slide-container slide" id="<?= $itemIds['PICT_SLIDER'] ?>"
                  style="display: none;"
                  data-slider-interval="<?= $arParams['SLIDER_INTERVAL'] ?>" data-slider-wrap="true">
                <?
                if ($showSlider) {
                    foreach ($morePhoto as $key => $photo) {
                        ?>
                        <img class="product-item-image-slide item <?= ($key == 0 ? 'active' : '') ?>"
                             src="<?= $photo['SRC'] ?>" alt="<?= $imgTitle ?>">
                        <?
                    }
                }
                ?>
            </span>

                    <!-- Основное изображение -->
            <img src="<?= $item['PREVIEW_PICTURE']['SRC'] ?>" loading="lazy" alt="<?= $imgTitle ?>"
                 id="<?= $itemIds['PICT'] ?>">

                    <!-- Скрытые элементы для возможности включения слайдера -->
            <?
            if ($item['SECOND_PICT']) {
                $bgImage = !empty($item['PREVIEW_PICTURE_SECOND']) ? $item['PREVIEW_PICTURE_SECOND']['SRC'] : $item['PREVIEW_PICTURE']['SRC'];
                ?>
                <img class="product-item-image-alternative" id="<?= $itemIds['SECOND_PICT'] ?>"
                     src="<?= $bgImage ?>" alt="<?= $imgTitle ?>" style="display: none;">
                <?
            }

            if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($price) && false) {
                ?>
                <div class="product-item-label-ring <?= $discountPositionClass ?>" id="<?= $itemIds['DSC_PERC'] ?>"
                     style="display: none;"
                    <?= ($price['PERCENT'] > 0 ? '' : 'style="display: none;"') ?>>
                    <span><?= -$price['PERCENT'] ?>%</span>
                </div>
                <?
            }
            ?>
            
            <div class="product-item-image-slider-control-container" id="<?= $itemIds['PICT_SLIDER'] ?>_indicator"
                 style="display: none;">
                <?
                if ($showSlider) {
                    foreach ($morePhoto as $key => $photo) {
                        ?>
                        <div class="product-item-image-slider-control<?= ($key == 0 ? ' active' : '') ?>"
                             data-go-to="<?= $key ?>"></div>
                        <?
                    }
                }
                ?>
            </div>
            
            <?
            if ($arParams['SLIDER_PROGRESS'] === 'Y') {
                ?>
                <div class="product-item-image-slider-progress-bar-container" style="display: none;">
                    <div class="product-item-image-slider-progress-bar" id="<?= $itemIds['PICT_SLIDER'] ?>_progress_bar"
                         style="width: 0;"></div>
                </div>
                <?
            }
            ?>
                    <? if ($itemHasDetailUrl): ?>
            </a>
        <? else: ?>
            </span>
        <? endif; ?>

            <div class="card-product__badges" id="<?= $itemIds['STICKER_ID'] ?>">
                <?
                if ($item['LABEL'] && !empty($item['LABEL_ARRAY_VALUE'])) {
                    $arLabelsClass = [
                        'NEWPRODUCT' => 'badge1 badge1_black',
                        'SALELEADER' => 'badge1 badge1_orange',
                        'SPECIALOFFER' => 'badge1 badge1_red',
                    ];
                    foreach ($item['LABEL_ARRAY_VALUE'] as $code => $value) {
                        ?>
                        <span title="<?= $value ?>"
                              class="<?= $arLabelsClass[$code] ?: 'badge1 badge1_black' ?>"><?= $value ?></span>
                        <?
                    }
                }
                ?>
            </div>

            <div class="card-product__name">
                <? if ($itemHasDetailUrl): ?>
                    <a href="<?= $item['DETAIL_PAGE_URL'] ?>" title="<?= $productTitle ?>">
                        <?= $productTitle ?>
                    </a>
                <? else: ?>
                    <?= $productTitle ?>
                <? endif; ?>
            </div>

            <? if (!empty($item['PREVIEW_TEXT'])): ?>
                <div class="card-product__description"><?= $item['PREVIEW_TEXT'] ?></div>
            <? endif; ?>

            <? if ($actualItem['CAN_BUY']): ?>
                <div class="card-product__status status_instock">В наличии</div>
            <? else: ?>
                <div class="card-product__status status_outstock">Нет в наличии</div>
            <? endif; ?>

            <?
            $arrivalDate = '';
            if (!empty($item['DISPLAY_PROPERTIES']['DATA_POSTUPLENIYA']['DISPLAY_VALUE'])) {
                $arrivalDateRaw = $item['DISPLAY_PROPERTIES']['DATA_POSTUPLENIYA']['DISPLAY_VALUE'];
                $arrivalDate = is_array($arrivalDateRaw) ? implode(' / ', $arrivalDateRaw) : (string)$arrivalDateRaw;
            } elseif (!empty($item['PROPERTIES']['DATA_POSTUPLENIYA']['VALUE'])) {
                $arrivalDateRaw = $item['PROPERTIES']['DATA_POSTUPLENIYA']['VALUE'];
                $arrivalDate = is_array($arrivalDateRaw) ? implode(' / ', $arrivalDateRaw) : (string)$arrivalDateRaw;
            }
            $showArrivalDate = !$actualItem['CAN_BUY'] && $arrivalDate !== '';
            ?>
            <?
            $isPriceZero = !empty($price) && (float)$price['PRICE'] <= 0;
            ?>
            <div class="card-product__prices" data-entity="price-block" <?= $isPriceZero ? 'style="display: none;"' : '' ?>>
                <div>
                    <span class="card-product__label1">С НДС (1 <?= $actualItem['ITEM_MEASURE']['TITLE'] ?>)</span>
                    <span class="card-product__price1" id="<?= $itemIds['PRICE'] ?>">
                    <?
                    if (!empty($price)) {
                        if ($arParams['PRODUCT_DISPLAY_MODE'] === 'N' && $haveOffers) {
                            echo Loc::getMessage(
                                'CT_BCI_TPL_MESS_PRICE_SIMPLE_MODE',
                                array(
                                    '#PRICE#' => $price['PRINT_PRICE'],
                                    '#VALUE#' => $measureRatio,
                                    '#UNIT#' => $minOffer['ITEM_MEASURE']['TITLE']
                                )
                            );
                        } else {
                            echo $price['PRINT_PRICE'];
                        }
                    }
                    ?>
                </span>
                    <?
                    if ($arParams['SHOW_OLD_PRICE'] === 'Y' && !empty($price)) {
                        ?>
                        <span class="card-product__price3" id="<?= $itemIds['PRICE_OLD'] ?>"
                        <?= ($price['PRICE'] >= $price['BASE_PRICE'] ? 'style="display: none;"' : '') ?>>
                        <?= $price['PRINT_BASE_PRICE'] ?>
                    </span>
                        <?
                    }
                    ?>
                </div>
                <div>
                    <? if ($showArrivalDate): ?>
                        <span class="card-product__label1">Дата поступления: <?= htmlspecialcharsbx($arrivalDate) ?></span>
                    <? endif; ?>
                    <span class="card-product__label1">На сумму</span>
                    <span class="card-product__price2" id="<?= $itemIds['PRICE_TOTAL'] ?>">
                        <?= $price['PRINT_PRICE'] ?>
                    </span>
                </div>
                <? /*
                if ($measureRatio > 1 && !empty($price)) {
                    ?>
                    <div>
                        <span class="card-product__label1">Упаковка (<?= $measureRatio ?> <?= $actualItem['ITEM_MEASURE']['TITLE'] ?>)</span>
                        <span class="card-product__price2" id="<?= $itemIds['PRICE_TOTAL'] ?>">
                        <?= $price['PRINT_RATIO_PRICE'] ?>
                    </span>
                    </div>
                    <?
                } */
                ?>
            </div>
            <? if ($isPriceZero): ?>
            <div class="card-product__prices">
                <div>
                    <span class="card-product__label1">С НДС (1 <?= $actualItem['ITEM_MEASURE']['TITLE'] ?>)</span>
                    <span class="card-product__price1 no_price">Цена по запросу</span>
                </div>
                <div>
                    <span class="card-product__price2 no_price">Цена по запросу</span>
                </div>
            </div>
            <? endif; ?>
            <button class="card-product__btn-favorite btn-favorites" type="button" data-id="<?= $item["ID"] ?>">
                <svg aria-hidden="true" width="25" height="23">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#heart1"></use>
                </svg>
                <span class="v-h">Добавить в избранное</span>
            </button>
        </div>

        <div class="card-product__bottom">
            <?
            if (!$haveOffers) {
                if ($actualItem['CAN_BUY'] && $arParams['USE_PRODUCT_QUANTITY']) {
                    ?>
                    <div class="stepcounter" data-entity="quantity-block">
                        <button class="stepcounter__btn" type="button" id="<?= $itemIds['QUANTITY_DOWN'] ?>">
                            <svg aria-hidden="true" width="14" height="2">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#minus1"></use>
                            </svg>
                            <span class="v-h">Уменьшить количество</span>
                        </button>
                        <input class="stepcounter__input" id="<?= $itemIds['QUANTITY'] ?>" type="number"
                               name="<?= $arParams['PRODUCT_QUANTITY_VARIABLE'] ?>"
                               value="<?= $measureRatio ?>" readonly>
                        <button class="stepcounter__btn" type="button" id="<?= $itemIds['QUANTITY_UP'] ?>">
                            <svg aria-hidden="true" width="14" height="14">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#plus1"></use>
                            </svg>
                            <span class="v-h">Увеличить количество</span>
                        </button>
                        <span style="display: none;" id="<?= $itemIds['QUANTITY_MEASURE'] ?>">
                        <?= $actualItem['ITEM_MEASURE']['TITLE'] ?>
                    </span>
                    </div>
                    <?
                }
            } elseif ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y') {
                if ($arParams['USE_PRODUCT_QUANTITY']) {
                    ?>
                    <div class="stepcounter" data-entity="quantity-block">
                        <button class="stepcounter__btn" type="button" id="<?= $itemIds['QUANTITY_DOWN'] ?>">
                            <svg aria-hidden="true" width="14" height="2">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#minus1"></use>
                            </svg>
                            <span class="v-h">Уменьшить количество</span>
                        </button>
                        <input class="stepcounter__input" id="<?= $itemIds['QUANTITY'] ?>" type="number"
                               name="<?= $arParams['PRODUCT_QUANTITY_VARIABLE'] ?>"
                               value="<?= $measureRatio ?>" readonly>
                        <button class="stepcounter__btn" type="button" id="<?= $itemIds['QUANTITY_UP'] ?>">
                            <svg aria-hidden="true" width="14" height="14">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#plus1"></use>
                            </svg>
                            <span class="v-h">Увеличить количество</span>
                        </button>
                        <span style="display: none;" id="<?= $itemIds['QUANTITY_MEASURE'] ?>">
                        <?= $actualItem['ITEM_MEASURE']['TITLE'] ?>
                    </span>
                    </div>
                    <?
                }
            }
            ?>

            <div data-entity="buttons-block" class="button-submit-card">
                <?
                $classBtn = 'btn btn_primary';
                if (!$haveOffers) {
                    if ($actualItem['CAN_BUY']) {
                        ?>
                        <div id="<?= $itemIds['BASKET_ACTIONS'] ?>">
                            <a href="javascript:void(0)" data-product-id="<?= $actualItem['ID'] ?>" class="<?= $classBtn ?>" id="<?= $itemIds['BUY_LINK'] ?>"
                               rel="nofollow">
                                <?= ($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']) ?>
                            </a>
                        </div>
                        <?
                    } else {
                        if ($showSubscribe) {
                            $APPLICATION->IncludeComponent(
                                'bitrix:catalog.product.subscribe',
                                '',
                                array(
                                    'PRODUCT_ID' => $actualItem['ID'],
                                    'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
                                    'BUTTON_CLASS' => 'btn btn_grey',
                                    'DEFAULT_DISPLAY' => true,
                                    'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
                                ),
                                $component,
                                array('HIDE_ICONS' => 'Y')
                            );
                        }
                    }
                } else {
                    if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y') {
                        if ($showSubscribe) {
                            $APPLICATION->IncludeComponent(
                                'bitrix:catalog.product.subscribe',
                                '',
                                array(
                                    'PRODUCT_ID' => $item['ID'],
                                    'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
                                    'BUTTON_CLASS' => $classBtn,
                                    'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
                                    'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
                                ),
                                $component,
                                array('HIDE_ICONS' => 'Y')
                            );
                        }
                        ?>
                        <a class="<?= $classBtn ?>" id="<?= $itemIds['NOT_AVAILABLE_MESS'] ?>" href="javascript:void(0)"
                           rel="nofollow"
                            <?= ($actualItem['CAN_BUY'] ? 'style="display: none;"' : '') ?>>
                            <?= $arParams['MESS_NOT_AVAILABLE'] ?>
                        </a>
                        <div id="<?= $itemIds['BASKET_ACTIONS'] ?>" <?= ($actualItem['CAN_BUY'] ? '' : 'style="display: none;"') ?>>
                            <a class="<?= $classBtn ?>" id="<?= $itemIds['BUY_LINK'] ?>" href="javascript:void(0)"
                               rel="nofollow">
                                <?= ($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']) ?>
                            </a>
                        </div>
                        <?
                    } else {
                        ?>
                        <div>
                            <a class="<?= $classBtn ?>" href="<?= $item['DETAIL_PAGE_URL'] ?>">
                                <?= $arParams['MESS_BTN_DETAIL'] ?>
                            </a>
                        </div>
                        <?
                    }
                }
                ?>
            </div>
        </div>

<?
// Скрытые элементы для корректной работы компонента
if ($arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !empty($item['PRODUCT_PROPERTIES'])) {
    ?>
    <div id="<?= $itemIds['BASKET_PROP_DIV'] ?>" style="display: none;">
        <?
        if (!empty($item['PRODUCT_PROPERTIES_FILL'])) {
            foreach ($item['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo) {
                ?>
                <input type="hidden" name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propID ?>]"
                       value="<?= htmlspecialcharsbx($propInfo['ID']) ?>">
                <?
                unset($item['PRODUCT_PROPERTIES'][$propID]);
            }
        }

        if (!empty($item['PRODUCT_PROPERTIES'])) {
            ?>
            <table>
                <?
                foreach ($item['PRODUCT_PROPERTIES'] as $propID => $propInfo) {
                    ?>
                    <tr>
                        <td><?= $item['PROPERTIES'][$propID]['NAME'] ?></td>
                        <td>
                            <?
                            if (
                                $item['PROPERTIES'][$propID]['PROPERTY_TYPE'] === 'L'
                                && $item['PROPERTIES'][$propID]['LIST_TYPE'] === 'C'
                            ) {
                                foreach ($propInfo['VALUES'] as $valueID => $value) {
                                    ?>
                                    <label>
                                        <? $checked = $valueID === $propInfo['SELECTED'] ? 'checked' : ''; ?>
                                        <input type="radio"
                                               name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propID ?>]"
                                               value="<?= $valueID ?>" <?= $checked ?>>
                                        <?= $value ?>
                                    </label>
                                    <br/>
                                    <?
                                }
                            } else {
                                ?>
                                <select name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propID ?>]">
                                    <?
                                    foreach ($propInfo['VALUES'] as $valueID => $value) {
                                        $selected = $valueID === $propInfo['SELECTED'] ? 'selected' : '';
                                        ?>
                                        <option value="<?= $valueID ?>" <?= $selected ?>>
                                            <?= $value ?>
                                        </option>
                                        <?
                                    }
                                    ?>
                                </select>
                                <?
                            }
                            ?>
                        </td>
                    </tr>
                    <?
                }
                ?>
            </table>
            <?
        }
        ?>
    </div>
    <?
}

// SKU блоки для торговых предложений
if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y' && $haveOffers && !empty($item['OFFERS_PROP'])) {
    ?>
    <div id="<?= $itemIds['PROP_DIV'] ?>" style="display: none;">
        <?
        foreach ($arParams['SKU_PROPS'] as $skuProperty) {
            $propertyId = $skuProperty['ID'];
            $skuProperty['NAME'] = htmlspecialcharsbx($skuProperty['NAME']);
            if (!isset($item['SKU_TREE_VALUES'][$propertyId]))
                continue;
            ?>
            <div class="product-item-info-container product-item-hidden" data-entity="sku-block">
                <div class="product-item-scu-container" data-entity="sku-line-block">
                    <?= $skuProperty['NAME'] ?>
                    <div class="product-item-scu-block">
                        <div class="product-item-scu-list">
                            <ul class="product-item-scu-item-list">
                                <?
                                foreach ($skuProperty['VALUES'] as $value) {
                                    if (!isset($item['SKU_TREE_VALUES'][$propertyId][$value['ID']]))
                                        continue;

                                    $value['NAME'] = htmlspecialcharsbx($value['NAME']);

                                    if ($skuProperty['SHOW_MODE'] === 'PICT') {
                                        ?>
                                        <li class="product-item-scu-item-color-container" title="<?= $value['NAME'] ?>"
                                            data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>"
                                            data-onevalue="<?= $value['ID'] ?>">
                                            <div class="product-item-scu-item-color-block">
                                                <div class="product-item-scu-item-color" title="<?= $value['NAME'] ?>"
                                                     style="background-image: url('<?= $value['PICT']['SRC'] ?>');">
                                                </div>
                                            </div>
                                        </li>
                                        <?
                                    } else {
                                        ?>
                                        <li class="product-item-scu-item-text-container" title="<?= $value['NAME'] ?>"
                                            data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>"
                                            data-onevalue="<?= $value['ID'] ?>">
                                            <div class="product-item-scu-item-text-block">
                                                <div class="product-item-scu-item-text"><?= $value['NAME'] ?></div>
                                            </div>
                                        </li>
                                        <?
                                    }
                                }
                                ?>
                            </ul>
                            <div style="clear: both;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?
        }
        ?>
    </div>
    <?
    foreach ($arParams['SKU_PROPS'] as $skuProperty) {
        if (!isset($item['OFFERS_PROP'][$skuProperty['CODE']]))
            continue;

        $skuProps[] = array(
            'ID' => $skuProperty['ID'],
            'SHOW_MODE' => $skuProperty['SHOW_MODE'],
            'VALUES' => $skuProperty['VALUES'],
            'VALUES_COUNT' => $skuProperty['VALUES_COUNT']
        );
    }

    unset($skuProperty, $value);

    if ($item['OFFERS_PROPS_DISPLAY']) {
        foreach ($item['JS_OFFERS'] as $keyOffer => $jsOffer) {
            $strProps = '';

            if (!empty($jsOffer['DISPLAY_PROPERTIES'])) {
                foreach ($jsOffer['DISPLAY_PROPERTIES'] as $displayProperty) {
                    $strProps .= '<dt>' . $displayProperty['NAME'] . '</dt><dd>'
                        . (is_array($displayProperty['VALUE'])
                            ? implode(' / ', $displayProperty['VALUE'])
                            : $displayProperty['VALUE'])
                        . '</dd>';
                }
            }

            $item['JS_OFFERS'][$keyOffer]['DISPLAY_PROPERTIES'] = $strProps;
        }
        unset($jsOffer, $strProps);
    }
}

// Блок сравнения
if (
    $arParams['DISPLAY_COMPARE']
    && (!$haveOffers || $arParams['PRODUCT_DISPLAY_MODE'] === 'Y')
) {
    ?>
    <div class="product-item-compare-container" style="display: none;">
        <div class="product-item-compare">
            <div class="checkbox">
                <label id="<?= $itemIds['COMPARE_LINK'] ?>">
                    <input type="checkbox" data-entity="compare-checkbox">
                    <span data-entity="compare-title"><?= $arParams['MESS_BTN_COMPARE'] ?></span>
                </label>
            </div>
        </div>
    </div>
    <?
}

// Блоки для лимитов количества (скрытые)
if ($arParams['SHOW_MAX_QUANTITY'] !== 'N') {
    if ($haveOffers) {
        if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y') {
            ?>
            <div class="product-item-info-container product-item-hidden" id="<?= $itemIds['QUANTITY_LIMIT'] ?>"
                 style="display: none;" data-entity="quantity-limit-block">
                <div class="product-item-info-container-title">
                    <?= $arParams['MESS_SHOW_MAX_QUANTITY'] ?>:
                    <span class="product-item-quantity" data-entity="quantity-limit-value"></span>
                </div>
            </div>
            <?
        }
    } else {
        if (
            $measureRatio
            && (float)$actualItem['CATALOG_QUANTITY'] > 0
            && $actualItem['CATALOG_QUANTITY_TRACE'] === 'Y'
            && $actualItem['CATALOG_CAN_BUY_ZERO'] === 'N'
        ) {
            ?>
            <div class="product-item-info-container product-item-hidden" id="<?= $itemIds['QUANTITY_LIMIT'] ?>"
                 style="display: none;">
                <div class="product-item-info-container-title">
                    <?= $arParams['MESS_SHOW_MAX_QUANTITY'] ?>:
                    <span class="product-item-quantity">
                        <?
                        if ($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                            if ((float)$actualItem['CATALOG_QUANTITY'] / $measureRatio >= $arParams['RELATIVE_QUANTITY_FACTOR']) {
                                echo $arParams['MESS_RELATIVE_QUANTITY_MANY'];
                            } else {
                                echo $arParams['MESS_RELATIVE_QUANTITY_FEW'];
                            }
                        } else {
                            echo $actualItem['CATALOG_QUANTITY'] . ' ' . $actualItem['ITEM_MEASURE']['TITLE'];
                        }
                        ?>
                    </span>
                </div>
            </div>
            <?
        }
    }
}

// Скрытый блок для отображения свойств торговых предложений
if ($haveOffers && $item['OFFERS_PROPS_DISPLAY']) {
    ?>
    <span id="<?= $itemIds['DISPLAY_PROP_DIV'] ?>" style="display: none;"></span>
    <?
}
?>