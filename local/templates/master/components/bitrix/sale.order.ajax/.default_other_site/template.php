<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CUser $USER
 * @var SaleOrderAjax $component
 * @var string $templateFolder
 */

$context = Main\Application::getInstance()->getContext();
$request = $context->getRequest();


$arParams['SHOW_ORDER_BUTTON'] = (string)($arParams['SHOW_ORDER_BUTTON'] ?? 'final_step');
$arParams['SHOW_TOTAL_ORDER_BUTTON'] = ($arParams['SHOW_TOTAL_ORDER_BUTTON'] ?? 'N') === 'Y' ? 'Y' : 'N';
$arParams['SHOW_PAY_SYSTEM_LIST_NAMES'] = ($arParams['SHOW_PAY_SYSTEM_LIST_NAMES'] ?? 'Y') === 'N' ? 'N' : 'Y';
$arParams['SHOW_PAY_SYSTEM_INFO_NAME'] = ($arParams['SHOW_PAY_SYSTEM_INFO_NAME'] ?? 'Y') === 'N' ? 'N' : 'Y';
$arParams['SHOW_DELIVERY_LIST_NAMES'] = ($arParams['SHOW_DELIVERY_LIST_NAMES'] ?? 'Y') === 'N' ? 'N' : 'Y';
$arParams['SHOW_DELIVERY_INFO_NAME'] = ($arParams['SHOW_DELIVERY_INFO_NAME'] ?? 'Y') === 'N' ? 'N' : 'Y';
$arParams['SHOW_DELIVERY_PARENT_NAMES'] = ($arParams['SHOW_DELIVERY_PARENT_NAMES'] ?? 'Y') === 'N' ? 'N' : 'Y';
$arParams['SHOW_STORES_IMAGES'] = ($arParams['SHOW_STORES_IMAGES'] ?? 'Y') === 'N' ? 'N' : 'Y';
$arParams['SKIP_USELESS_BLOCK'] = ($arParams['SKIP_USELESS_BLOCK'] ?? 'Y') === 'N' ? 'N' : 'Y';
if (!isset($arParams['BASKET_POSITION']) || !in_array($arParams['BASKET_POSITION'], array('before', 'after'))) {
    $arParams['BASKET_POSITION'] = 'after';
}
$arParams['SHOW_BASKET_HEADERS'] = ($arParams['SHOW_BASKET_HEADERS'] ?? 'N') === 'Y' ? 'Y' : 'N';
$arParams['DELIVERY_FADE_EXTRA_SERVICES'] = ($arParams['DELIVERY_FADE_EXTRA_SERVICES'] ?? 'N') === 'Y' ? 'Y' : 'N';
$arParams['SHOW_NEAREST_PICKUP'] = ($arParams['SHOW_NEAREST_PICKUP'] ?? 'N') === 'Y' ? 'Y' : 'N';
$arParams['DELIVERIES_PER_PAGE'] = (int)($arParams['DELIVERIES_PER_PAGE'] ?? 9);
$arParams['PAY_SYSTEMS_PER_PAGE'] = (int)($arParams['PAY_SYSTEMS_PER_PAGE'] ?? 9);
$arParams['PICKUPS_PER_PAGE'] = (int)($arParams['PICKUPS_PER_PAGE'] ?? 5);
$arParams['SHOW_PICKUP_MAP'] = ($arParams['SHOW_PICKUP_MAP'] ?? 'Y') === 'N' ? 'N' : 'Y';
$arParams['SHOW_MAP_IN_PROPS'] = ($arParams['SHOW_MAP_IN_PROPS'] ?? 'N') === 'Y' ? 'Y' : 'N';
$arParams['PICKUP_MAP_TYPE'] = (string)($arParams['PICKUP_MAP_TYPE'] ?? 'yandex');
$arParams['HIDE_ORDER_DESCRIPTION'] = ($arParams['HIDE_ORDER_DESCRIPTION'] ?? 'N') === 'Y' ? 'Y' : 'N';
$arParams['ALLOW_USER_PROFILES'] = ($arParams['ALLOW_USER_PROFILES'] ?? 'N') === 'Y' ? 'Y' : 'N';
$arParams['ALLOW_NEW_PROFILE'] = ($arParams['ALLOW_NEW_PROFILE'] ?? 'N') === 'Y' ? 'Y' : 'N';
$arParams['SHOW_COUPONS'] = ($arParams['SHOW_COUPONS'] ?? 'Y') === 'N' ? 'N' : 'Y';
if ($arParams['SHOW_COUPONS'] === 'N') {
    $arParams['SHOW_COUPONS_BASKET'] = 'N';
    $arParams['SHOW_COUPONS_DELIVERY'] = 'N';
    $arParams['SHOW_COUPONS_PAY_SYSTEM'] = 'N';
} else {
    $arParams['SHOW_COUPONS_BASKET'] = ($arParams['SHOW_COUPONS_BASKET'] ?? 'Y') === 'N' ? 'N' : 'Y';
    $arParams['SHOW_COUPONS_DELIVERY'] = ($arParams['SHOW_COUPONS_DELIVERY'] ?? 'Y') === 'N' ? 'N' : 'Y';
    $arParams['SHOW_COUPONS_PAY_SYSTEM'] = ($arParams['SHOW_COUPONS_PAY_SYSTEM'] ?? 'Y') === 'N' ? 'N' : 'Y';
}

$arParams['USE_YM_GOALS'] = ($arParams['USE_YM_GOALS'] ?? 'N') === 'Y' ? 'Y' : 'N';
$arParams['YM_GOALS_COUNTER'] = (string)($arParams['YM_GOALS_COUNTER'] ?? '');
$arParams['YM_GOALS_INITIALIZE'] = (string)($arParams['YM_GOALS_INITIALIZE'] ?? 'BX-order-init');
$arParams['YM_GOALS_EDIT_REGION'] = (string)($arParams['YM_GOALS_EDIT_REGION'] ?? 'BX-region-edit');
$arParams['YM_GOALS_EDIT_DELIVERY'] = (string)($arParams['YM_GOALS_EDIT_DELIVERY'] ?? 'BX-delivery-edit');
$arParams['YM_GOALS_EDIT_PICKUP'] = (string)($arParams['YM_GOALS_EDIT_PICKUP'] ?? 'BX-pickUp-edit');
$arParams['YM_GOALS_EDIT_PAY_SYSTEM'] = (string)($arParams['YM_GOALS_EDIT_PAY_SYSTEM'] ?? 'BX-paySystem-edit');
$arParams['YM_GOALS_EDIT_PROPERTIES'] = (string)($arParams['YM_GOALS_EDIT_PROPERTIES'] ?? 'BX-properties-edit');
$arParams['YM_GOALS_EDIT_BASKET'] = (string)($arParams['YM_GOALS_EDIT_BASKET'] ?? 'BX-basket-edit');
$arParams['YM_GOALS_NEXT_REGION'] = (string)($arParams['YM_GOALS_NEXT_REGION'] ?? 'BX-region-next');
$arParams['YM_GOALS_NEXT_DELIVERY'] = (string)($arParams['YM_GOALS_NEXT_DELIVERY'] ?? 'BX-delivery-next');
$arParams['YM_GOALS_NEXT_PICKUP'] = (string)($arParams['YM_GOALS_NEXT_PICKUP'] ?? 'BX-pickUp-next');
$arParams['YM_GOALS_NEXT_PAY_SYSTEM'] = (string)($arParams['YM_GOALS_NEXT_PAY_SYSTEM'] ?? 'BX-paySystem-next');
$arParams['YM_GOALS_NEXT_PROPERTIES'] = (string)($arParams['YM_GOALS_NEXT_PROPERTIES'] ?? 'BX-properties-next');
$arParams['YM_GOALS_NEXT_BASKET'] = (string)($arParams['YM_GOALS_NEXT_BASKET'] ?? 'BX-basket-next');
$arParams['YM_GOALS_SAVE_ORDER'] = (string)($arParams['YM_GOALS_SAVE_ORDER'] ?? 'BX-order-save');

$arParams['USE_ENHANCED_ECOMMERCE'] = ($arParams['USE_ENHANCED_ECOMMERCE'] ?? 'N') === 'Y' ? 'Y' : 'N';
$arParams['DATA_LAYER_NAME'] = trim((string)($arParams['DATA_LAYER_NAME'] ?? 'dataLayer'));
$arParams['BRAND_PROPERTY'] = trim((string)($arParams['BRAND_PROPERTY'] ?? ''));

$arParams['SHOW_MAP_FOR_DELIVERIES'] ??= [];
if (!is_array($arParams['SHOW_MAP_FOR_DELIVERIES'])) {
    $arParams['SHOW_MAP_FOR_DELIVERIES'] = [];
}

$arParams['HIDE_DETAIL_PAGE_URL'] = (string)($arParams['HIDE_DETAIL_PAGE_URL'] ?? 'N') === 'Y' ? 'Y' : 'N'; // Unknown parameter

$useDefaultMessages = !isset($arParams['USE_CUSTOM_MAIN_MESSAGES']) || $arParams['USE_CUSTOM_MAIN_MESSAGES'] != 'Y';

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_BLOCK_NAME'])) {
    $arParams['MESS_AUTH_BLOCK_NAME'] = Loc::getMessage('AUTH_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REG_BLOCK_NAME'])) {
    $arParams['MESS_REG_BLOCK_NAME'] = Loc::getMessage('REG_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_BASKET_BLOCK_NAME'])) {
    $arParams['MESS_BASKET_BLOCK_NAME'] = Loc::getMessage('BASKET_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REGION_BLOCK_NAME'])) {
    $arParams['MESS_REGION_BLOCK_NAME'] = Loc::getMessage('REGION_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PAYMENT_BLOCK_NAME'])) {
    $arParams['MESS_PAYMENT_BLOCK_NAME'] = Loc::getMessage('PAYMENT_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_DELIVERY_BLOCK_NAME'])) {
    $arParams['MESS_DELIVERY_BLOCK_NAME'] = Loc::getMessage('DELIVERY_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_BUYER_BLOCK_NAME'])) {
    $arParams['MESS_BUYER_BLOCK_NAME'] = Loc::getMessage('BUYER_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_BACK'])) {
    $arParams['MESS_BACK'] = Loc::getMessage('BACK_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_FURTHER'])) {
    $arParams['MESS_FURTHER'] = Loc::getMessage('FURTHER_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_EDIT'])) {
    $arParams['MESS_EDIT'] = Loc::getMessage('EDIT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ORDER'])) {
    $arParams['MESS_ORDER'] = $arParams['~MESS_ORDER'] = Loc::getMessage('ORDER_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PRICE'])) {
    $arParams['MESS_PRICE'] = Loc::getMessage('PRICE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PERIOD'])) {
    $arParams['MESS_PERIOD'] = Loc::getMessage('PERIOD_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_NAV_BACK'])) {
    $arParams['MESS_NAV_BACK'] = Loc::getMessage('NAV_BACK_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_NAV_FORWARD'])) {
    $arParams['MESS_NAV_FORWARD'] = Loc::getMessage('NAV_FORWARD_DEFAULT');
}

$useDefaultMessages = !isset($arParams['USE_CUSTOM_ADDITIONAL_MESSAGES']) || $arParams['USE_CUSTOM_ADDITIONAL_MESSAGES'] != 'Y';

if ($useDefaultMessages || !isset($arParams['MESS_PRICE_FREE'])) {
    $arParams['MESS_PRICE_FREE'] = Loc::getMessage('PRICE_FREE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ECONOMY'])) {
    $arParams['MESS_ECONOMY'] = Loc::getMessage('ECONOMY_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REGISTRATION_REFERENCE'])) {
    $arParams['MESS_REGISTRATION_REFERENCE'] = Loc::getMessage('REGISTRATION_REFERENCE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_REFERENCE_1'])) {
    $arParams['MESS_AUTH_REFERENCE_1'] = Loc::getMessage('AUTH_REFERENCE_1_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_REFERENCE_2'])) {
    $arParams['MESS_AUTH_REFERENCE_2'] = Loc::getMessage('AUTH_REFERENCE_2_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_REFERENCE_3'])) {
    $arParams['MESS_AUTH_REFERENCE_3'] = Loc::getMessage('AUTH_REFERENCE_3_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ADDITIONAL_PROPS'])) {
    $arParams['MESS_ADDITIONAL_PROPS'] = Loc::getMessage('ADDITIONAL_PROPS_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_USE_COUPON'])) {
    $arParams['MESS_USE_COUPON'] = Loc::getMessage('USE_COUPON_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_COUPON'])) {
    $arParams['MESS_COUPON'] = Loc::getMessage('COUPON_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PERSON_TYPE'])) {
    $arParams['MESS_PERSON_TYPE'] = Loc::getMessage('PERSON_TYPE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_SELECT_PROFILE'])) {
    $arParams['MESS_SELECT_PROFILE'] = Loc::getMessage('SELECT_PROFILE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REGION_REFERENCE'])) {
    $arParams['MESS_REGION_REFERENCE'] = Loc::getMessage('REGION_REFERENCE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PICKUP_LIST'])) {
    $arParams['MESS_PICKUP_LIST'] = Loc::getMessage('PICKUP_LIST_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_NEAREST_PICKUP_LIST'])) {
    $arParams['MESS_NEAREST_PICKUP_LIST'] = Loc::getMessage('NEAREST_PICKUP_LIST_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_SELECT_PICKUP'])) {
    $arParams['MESS_SELECT_PICKUP'] = Loc::getMessage('SELECT_PICKUP_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_INNER_PS_BALANCE'])) {
    $arParams['MESS_INNER_PS_BALANCE'] = Loc::getMessage('INNER_PS_BALANCE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ORDER_DESC'])) {
    $arParams['MESS_ORDER_DESC'] = Loc::getMessage('ORDER_DESC_DEFAULT');
}

$useDefaultMessages = !isset($arParams['USE_CUSTOM_ERROR_MESSAGES']) || $arParams['USE_CUSTOM_ERROR_MESSAGES'] != 'Y';

if ($useDefaultMessages || !isset($arParams['MESS_PRELOAD_ORDER_TITLE'])) {
    $arParams['MESS_PRELOAD_ORDER_TITLE'] = Loc::getMessage('PRELOAD_ORDER_TITLE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_SUCCESS_PRELOAD_TEXT'])) {
    $arParams['MESS_SUCCESS_PRELOAD_TEXT'] = Loc::getMessage('SUCCESS_PRELOAD_TEXT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_FAIL_PRELOAD_TEXT'])) {
    $arParams['MESS_FAIL_PRELOAD_TEXT'] = Loc::getMessage('FAIL_PRELOAD_TEXT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_DELIVERY_CALC_ERROR_TITLE'])) {
    $arParams['MESS_DELIVERY_CALC_ERROR_TITLE'] = Loc::getMessage('DELIVERY_CALC_ERROR_TITLE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_DELIVERY_CALC_ERROR_TEXT'])) {
    $arParams['MESS_DELIVERY_CALC_ERROR_TEXT'] = Loc::getMessage('DELIVERY_CALC_ERROR_TEXT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PAY_SYSTEM_PAYABLE_ERROR'])) {
    $arParams['MESS_PAY_SYSTEM_PAYABLE_ERROR'] = Loc::getMessage('PAY_SYSTEM_PAYABLE_ERROR_DEFAULT');
}

$scheme = $request->isHttps() ? 'https' : 'http';

switch (LANGUAGE_ID) {
    case 'ru':
        $locale = 'ru-RU';
        break;
    case 'ua':
        $locale = 'ru-UA';
        break;
    case 'tk':
        $locale = 'tr-TR';
        break;
    default:
        $locale = 'en-US';
        break;
}

\Bitrix\Main\UI\Extension::load('ui.fonts.opensans');
$APPLICATION->SetAdditionalCSS($templateFolder . '/style.css', true);
$this->addExternalJs($templateFolder . '/order_ajax.js');
\Bitrix\Sale\PropertyValueCollection::initJs();
$this->addExternalJs($templateFolder . '/script.js');
?>
    <NOSCRIPT>
        <div style="color:red"><?= Loc::getMessage('SOA_NO_JS') ?></div>
    </NOSCRIPT>
<?php

if ((string)$request->get('ORDER_ID') !== '') {
    include(Main\Application::getDocumentRoot() . $templateFolder . '/confirm.php');
} elseif ($arParams['DISABLE_BASKET_REDIRECT'] === 'Y' && $arResult['SHOW_EMPTY_BASKET']) {
    include(Main\Application::getDocumentRoot() . $templateFolder . '/empty.php');
} else {
    Main\UI\Extension::load('phone_auth');

    $hideDelivery = empty($arResult['DELIVERY']);
    ?>
    <form action="<?= POST_FORM_ACTION_URI ?>" method="POST" name="ORDER_FORM" id="bx-soa-order-form"
          enctype="multipart/form-data">
        <?php
        echo bitrix_sessid_post();

        if ($arResult['PREPAY_ADIT_FIELDS'] <> '') {
            echo $arResult['PREPAY_ADIT_FIELDS'];
        }
        ?>
        <input type="hidden" name="<?= $arParams['ACTION_VARIABLE'] ?>" value="saveOrderAjax">
        <input type="hidden" name="location_type" value="code">
        <input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?= $arResult['BUYER_STORE'] ?>">
        <div id="bx-soa-order" class="row bx-<?= $arParams['TEMPLATE_THEME'] ?>" style="opacity: 0">
            <div class="heading-complex">
                <div class="heading-complex__cart order-cart">
                    <h1 class="title2">Оформление заказа</h1>
                    <a href="<?= $arParams['PATH_TO_BASKET'] ?>" class="heading-complex__edit">
                        <svg width="20" height="20" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#edit"></use>
                        </svg>
                        <span>Изменить заказ</span>
                    </a>
                </div>
            </div>

            <div class="block-order block2">
                <div class="container">
                    <div class="block-order__content">
                        <!--	MAIN BLOCK	-->
                        <div class="block-order__body">

                            <!--	USER BLOCK	-->
                            <div id="bx-soa-user" class="bx-soa-section bx-active block-order__column"></div>

                            <?php
                            if ($arParams['BASKET_POSITION'] === 'before'):
                                ?>
                                <!--	BASKET ITEMS BLOCK	-->
                                <div id="bx-soa-basket" data-visited="false" class="bx-soa-section bx-active">
                                    <div class="bx-soa-section-title-container">
                                        <h2 class="bx-soa-section-title col-sm-9">
                                            <span class="bx-soa-section-title-count"></span><?= $arParams['MESS_BASKET_BLOCK_NAME'] ?>
                                        </h2>
                                        <div class="col-xs-12 col-sm-3 text-right"><a href="javascript:void(0)"
                                                                                      class="bx-soa-editstep"><?= $arParams['MESS_EDIT'] ?></a>
                                        </div>
                                    </div>
                                    <div class="bx-soa-section-content container-fluid"></div>
                                </div>
                            <?php
                            endif;
                            ?>
                            <div class="block-order__column">
                                <div class="block-order__title">Где и как вы хотите получить заказ?</div>
                                <!--	REGION BLOCK	-->
                                <div id="bx-soa-region" data-visited="false" class="bx-soa-section bx-active">
                                    <div class="bx-soa-section-title-container">
                                        <h2 class="bx-soa-section-title col-sm-9">
                                            <span class="bx-soa-section-title-count"></span><?= $arParams['MESS_REGION_BLOCK_NAME'] ?>
                                        </h2>
                                        <div class="col-xs-12 col-sm-3 text-right"><a href=""
                                                                                      class="bx-soa-editstep"><?= $arParams['MESS_EDIT'] ?></a>
                                        </div>
                                    </div>
                                    <div class="bx-soa-section-content container-fluid"></div>
                                </div>

                                <?php
                                if ($arParams['DELIVERY_TO_PAYSYSTEM'] === 'p2d'):
                                    ?>
                                    <!--	PAY SYSTEMS BLOCK	-->
                                    <div id="bx-soa-paysystem" data-visited="false" class="bx-soa-section bx-active">
                                        <div class="bx-soa-section-title-container">
                                            <h2 class="bx-soa-section-title col-sm-9">
                                                <span class="bx-soa-section-title-count"></span><?= $arParams['MESS_PAYMENT_BLOCK_NAME'] ?>
                                            </h2>
                                            <div class="col-xs-12 col-sm-3 text-right"><a href=""
                                                                                          class="bx-soa-editstep"><?= $arParams['MESS_EDIT'] ?></a>
                                            </div>
                                        </div>
                                        <div class="bx-soa-section-content container-fluid"></div>
                                    </div>
                                    <!--	DELIVERY BLOCK	-->
                                    <div id="bx-soa-delivery" data-visited="false"
                                         class="bx-soa-section bx-active" <?= ($hideDelivery ? 'style="display:none"' : '') ?>>
                                        <div class="bx-soa-section-title-container">
                                            <h2 class="bx-soa-section-title col-sm-9">
                                                <span class="bx-soa-section-title-count"></span><?= $arParams['MESS_DELIVERY_BLOCK_NAME'] ?>
                                            </h2>
                                            <div class="col-xs-12 col-sm-3 text-right"><a href=""
                                                                                          class="bx-soa-editstep"><?= $arParams['MESS_EDIT'] ?></a>
                                            </div>
                                        </div>
                                        <div class="bx-soa-section-content container-fluid"></div>
                                    </div>
                                    <!--	PICKUP BLOCK	-->
                                    <div id="bx-soa-pickup" data-visited="false" class="bx-soa-section"
                                         style="display:none">
                                        <div class="bx-soa-section-title-container">
                                            <h2 class="bx-soa-section-title col-sm-9">
                                                <span class="bx-soa-section-title-count"></span>
                                            </h2>
                                            <div class="col-xs-12 col-sm-3 text-right"><a href=""
                                                                                          class="bx-soa-editstep"><?= $arParams['MESS_EDIT'] ?></a>
                                            </div>
                                        </div>
                                        <div class="bx-soa-section-content container-fluid"></div>
                                    </div>
                                <?php
                                else:
                                    ?>
                                    <!--	DELIVERY BLOCK	-->
                                    <div id="bx-soa-delivery" data-visited="false"
                                         class="bx-soa-section bx-active" <?= ($hideDelivery ? 'style="display:none"' : '') ?>>
                                        <div class="bx-soa-section-content container-fluid"></div>
                                    </div>
                                    <!--	PICKUP BLOCK	-->
                                    <div id="bx-soa-pickup" data-visited="false" class="bx-soa-section bottom-block-order self-delivery-block _active"
                                         style="display:none">
                                        <div class="bottom-block-order__title">Пункты самовывоза</div>
                                        <div class="bx-soa-section-title-container" style="display:none">
                                            <h2 class="bx-soa-section-title col-sm-9">
                                                <span class="bx-soa-section-title-count"></span>
                                            </h2>
                                        </div>
                                        <div class="bx-soa-section-content container-fluid"></div>
                                    </div>
                                    <!--	PAY SYSTEMS BLOCK	-->
                                    <div id="bx-soa-paysystem" data-visited="false" class="bx-soa-section bx-active">
                                        <div class="bx-soa-section-title-container">
                                            <h2 class="bx-soa-section-title col-sm-9">
                                                <span class="bx-soa-section-title-count"></span><?= $arParams['MESS_PAYMENT_BLOCK_NAME'] ?>
                                            </h2>
                                            <div class="col-xs-12 col-sm-3 text-right"><a href=""
                                                                                          class="bx-soa-editstep"><?= $arParams['MESS_EDIT'] ?></a>
                                            </div>
                                        </div>
                                        <div class="bx-soa-section-content container-fluid"></div>
                                    </div>
                                <?php
                                endif;
                                ?>
                                <!--	BUYER PROPS BLOCK	-->
                                <div id="bx-soa-properties" data-visited="false" class="bx-soa-section bx-active">
                                    <div class="bx-soa-section-title-container">
                                        <h2 class="bx-soa-section-title col-sm-9">
                                            <span class="bx-soa-section-title-count"></span>
                                        </h2>
                                        <div class="col-xs-12 col-sm-3 text-right">
                                        </div>
                                    </div>
                                    <div class="bx-soa-section-content container-fluid"></div>
                                </div>


                            </div>

                            <div class="block-order__column">
                                <div class="block-order__title">Товары в заказе</div>
                                <div class="cards-product">
                                    <?php foreach ($arResult['BASKET_ITEMS'] as $item): ?>
                                        <div class="card-product">
                                            <div class="card-product__image">
                                                <?php if (!empty($item["PREVIEW_PICTURE_SRC"]) || !empty($item["DETAIL_PICTURE_SRC"])): ?>
                                                    <img loading="lazy" src="<?= $item["PREVIEW_PICTURE_SRC"] ?: $item["DETAIL_PICTURE_SRC"] ?>"
                                                         alt="<?= $item["NAME"] ?>">
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-product__item">
                                                <a href="<?= $item["DETAIL_PAGE_URL"] ?>" class="card-product__top">
                                                    <?php if (!empty($item["PROPERTY_ARTICLE_VALUE"])): ?>
                                                        <div class="card-product__article">
                                                            Артикул: <?= $item["PROPERTY_ARTICLE_VALUE"] ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="card-product__title">
                                                        <?= $item["NAME"] ?>
                                                    </div>
                                                    <div class="card-product__stock">
                                                        <?= ($item["CAN_BUY"] == 'Y') ? 'В наличии' : 'Нет в наличии' ?>
                                                    </div>
                                                </a>
                                                <div class="card-product__bottom">
                                                    <div class="card-product__stepcounter">
                                                        <span><?= $item["QUANTITY"] ?> <?= $item["MEASURE_TEXT"] ?>.</span>
                                                    </div>
                                                    <div class="card-product__prices">
                                                        <?php if ($item["DISCOUNT_PRICE"] > 0): ?>
                                                            <div class="card-product__old-price">
                                                                <s>
                                                                    <?= $item["BASE_PRICE_FORMATED"] ?>
                                                                </s>
                                                                <div class="card-product__sale">
                                                                    -<?= $item["DISCOUNT_PRICE_PERCENT_FORMATED"] ?>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="card-product__price">
                                                            <?= $item["PRICE_FORMATED"] ?>/<?= $item["MEASURE_TEXT"] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!--	ORDER SAVE BLOCK	-->
                            <div id="bx-soa-orderSave">
                                <div class="checkbox">
                                    <?php
                                    if ($arParams['USER_CONSENT'] === 'Y') {
                                        $APPLICATION->IncludeComponent(
                                            'bitrix:main.userconsent.request',
                                            '',
                                            array(
                                                'ID' => $arParams['USER_CONSENT_ID'],
                                                'IS_CHECKED' => $arParams['USER_CONSENT_IS_CHECKED'],
                                                'IS_LOADED' => $arParams['USER_CONSENT_IS_LOADED'],
                                                'AUTO_SAVE' => 'N',
                                                'SUBMIT_EVENT_NAME' => 'bx-soa-order-save',
                                                'REPLACE' => array(
                                                    'button_caption' => $arParams['~MESS_ORDER'] ?? $arParams['MESS_ORDER'],
                                                    'fields' => $arResult['USER_CONSENT_PROPERTY_DATA']
                                                )
                                            )
                                        );
                                    }
                                    ?>
                                </div>
                                <a href="javascript:void(0)" style="margin: 10px 0"
                                   class="pull-right btn btn-default btn-lg hidden-xs" data-save-button="true">
                                    <?= $arParams['MESS_ORDER'] ?>
                                </a>
                            </div>

                            <div style="display: none;">
                                <div id="bx-soa-main-notifications">
                                    <div class="alert alert-danger" style="display:none"></div>
                                    <div data-type="informer" style="display:none"></div>
                                </div>

                                <!--	DUPLICATE MOBILE ORDER SAVE BLOCK	-->
                                <div id="bx-soa-total-mobile" style="display:none"></div>

                                <!--	AUTH BLOCK	-->
                                <div id="bx-soa-auth" class="bx-soa-section bx-soa-auth" style="display:none">
                                    <div class="bx-soa-section-title-container">
                                        <h2 class="bx-soa-section-title col-sm-9">
                                            <span class="bx-soa-section-title-count"></span><?= $arParams['MESS_AUTH_BLOCK_NAME'] ?>
                                        </h2>
                                    </div>
                                    <div class="bx-soa-section-content container-fluid"></div>
                                </div>

                                <!--	BASKET ITEMS BLOCK	-->
                                <div id="bx-soa-basket" data-visited="false" class="bx-soa-section bx-active">
                                    <div class="bx-soa-section-content container-fluid "></div>
                                </div>

                                <div id='bx-soa-basket-hidden' class="bx-soa-section"></div>
                                <div id='bx-soa-region-hidden' class="bx-soa-section"></div>
                                <div id='bx-soa-paysystem-hidden' class="bx-soa-section"></div>
                                <div id='bx-soa-delivery-hidden' class="bx-soa-section"></div>
                                <div id='bx-soa-pickup-hidden' class="bx-soa-section"></div>
                                <div id="bx-soa-properties-hidden" class="bx-soa-section"></div>
                                <div id="bx-soa-auth-hidden" class="bx-soa-section">
                                    <div class="bx-soa-section-content container-fluid reg"></div>
                                </div>
                            </div>
                        </div>

                        <div id="bx-soa-total" class="detail-cart">
                            <div class="detail-cart__sticky">
                                <div class="detail-cart__title">
                                    Детали заказа
                                </div>
                                <div class="bx-soa-cart-total-ghost"></div>
                                <div class="bx-soa-cart-total"></div>
                                <div class="textblock1 textblock1_small">
                                    <? $APPLICATION->IncludeFile(SITE_DIR . 'include/order/personal.php'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div id="bx-soa-saved-files" style="display:none"></div>
    <div id="bx-soa-soc-auth-services" style="display:none">
        <?php
        $arServices = false;
        $arResult['ALLOW_SOCSERV_AUTHORIZATION'] = Main\Config\Option::get('main', 'allow_socserv_authorization', 'Y') != 'N' ? 'Y' : 'N';
        $arResult['FOR_INTRANET'] = false;

        if (Main\ModuleManager::isModuleInstalled('intranet') || Main\ModuleManager::isModuleInstalled('rest'))
            $arResult['FOR_INTRANET'] = true;

        if (Main\Loader::includeModule('socialservices') && $arResult['ALLOW_SOCSERV_AUTHORIZATION'] === 'Y') {
            $oAuthManager = new CSocServAuthManager();
            $arServices = $oAuthManager->GetActiveAuthServices(array(
                'BACKURL' => $arParams['~CURRENT_PAGE'],
                'FOR_INTRANET' => $arResult['FOR_INTRANET'],
            ));

            if (!empty($arServices)) {
                $APPLICATION->IncludeComponent(
                    'bitrix:socserv.auth.form',
                    'flat',
                    array(
                        'AUTH_SERVICES' => $arServices,
                        'AUTH_URL' => $arParams['~CURRENT_PAGE'],
                        'POST' => $arResult['POST'],
                    ),
                    $component,
                    array('HIDE_ICONS' => 'Y')
                );
            }
        }
        ?>
    </div>

    <div style="display: none">
        <?php
        // we need to have all styles for sale.location.selector.steps, but RestartBuffer() cuts off document head with styles in it
        $APPLICATION->IncludeComponent(
            'bitrix:sale.location.selector.steps',
            '.default',
            array(),
            false
        );
        $APPLICATION->IncludeComponent(
            'bitrix:sale.location.selector.search',
            '.default',
            array(),
            false
        );
        ?>
    </div>
    <?php
    $signer = new Main\Security\Sign\Signer;
    $signedParams = $signer->sign(base64_encode(serialize($arParams)), 'sale.order.ajax');
    $messages = Loc::loadLanguageFile(__FILE__);
    ?>
    <script>
        BX.message(<?=CUtil::PhpToJSObject($messages)?>);
        BX.Sale.OrderAjaxComponent.init({
            result: <?=CUtil::PhpToJSObject($arResult['JS_DATA'])?>,
            locations: <?=CUtil::PhpToJSObject($arResult['LOCATIONS'])?>,
            params: <?=CUtil::PhpToJSObject($arParams)?>,
            signedParamsString: '<?=CUtil::JSEscape($signedParams)?>',
            siteID: '<?=CUtil::JSEscape($component->getSiteId())?>',
            ajaxUrl: '<?=CUtil::JSEscape($component->getPath() . '/ajax.php')?>',
            templateFolder: '<?=CUtil::JSEscape($templateFolder)?>',
            propertyValidation: true,
            showWarnings: true,
            pickUpMap: {
                defaultMapPosition: {
                    lat: 55.76,
                    lon: 37.64,
                    zoom: 14
                },
                secureGeoLocation: false,
                geoLocationMaxTime: 5000,
                minToShowNearestBlock: 3,
                nearestPickUpsToShow: 3
            },
            propertyMap: {
                defaultMapPosition: {
                    lat: 55.76,
                    lon: 37.64,
                    zoom: 14
                }
            },
            orderBlockId: 'bx-soa-order',
            authBlockId: 'bx-soa-auth',
            basketBlockId: 'bx-soa-basket',
            regionBlockId: 'bx-soa-region',
            paySystemBlockId: 'bx-soa-paysystem',
            deliveryBlockId: 'bx-soa-delivery',
            pickUpBlockId: 'bx-soa-pickup',
            propsBlockId: 'bx-soa-properties',
            totalBlockId: 'bx-soa-total'
        });
    </script>
    <script>
        <?php
        // spike: for children of cities we place this prompt
        $city = \Bitrix\Sale\Location\TypeTable::getRow([
            'select' => [
                'ID',
            ],
            'filter' => [
                '=CODE' => 'CITY',
            ],
        ]);
        ?>
        BX.saleOrderAjax.init(<?=CUtil::PhpToJSObject([
            'source' => $component->getPath() . '/get.php',
            'cityTypeId' => (int)($city['ID'] ?? 0),
            'messages' => [
                'otherLocation' => '--- ' . Loc::getMessage('SOA_OTHER_LOCATION'),
                'moreInfoLocation' => '--- ' . Loc::getMessage('SOA_NOT_SELECTED_ALT'), // spike: for children of cities we place this prompt
                'notFoundPrompt' =>
                    '<div class="-bx-popup-special-prompt">'
                    . Loc::getMessage('SOA_LOCATION_NOT_FOUND') . '.<br />'
                    . Loc::getMessage(
                        'SOA_LOCATION_NOT_FOUND_PROMPT',
                        [
                            '#ANCHOR#' => '<a href="javascript:void(0)" class="-bx-popup-set-mode-add-loc">',
                            '#ANCHOR_END#' => '</a>',
                        ]
                    )
                    . '</div>'
                ,
            ],
        ]); ?>);
    </script>
    <?php
    if ($arParams['SHOW_PICKUP_MAP'] === 'Y' || $arParams['SHOW_MAP_IN_PROPS'] === 'Y') {
        if ($arParams['PICKUP_MAP_TYPE'] === 'yandex') {
            $this->addExternalJs($templateFolder . '/scripts/yandex_maps.js');
            $apiKey = htmlspecialcharsbx(Main\Config\Option::get('fileman', 'yandex_map_api_key', ''));
            ?>
            <script src="<?= $scheme ?>://api-maps.yandex.ru/2.1.50/?apikey=<?= $apiKey ?>&load=package.full&lang=<?= $locale ?>"></script>
            <script>
                (function bx_ymaps_waiter() {
                    if (typeof ymaps !== 'undefined' && BX.Sale && BX.Sale.OrderAjaxComponent)
                        ymaps.ready(BX.proxy(BX.Sale.OrderAjaxComponent.initMaps, BX.Sale.OrderAjaxComponent));
                    else
                        setTimeout(bx_ymaps_waiter, 100);
                })();
            </script>
            <?php
        }

        if ($arParams['PICKUP_MAP_TYPE'] === 'google') {
            $this->addExternalJs($templateFolder . '/scripts/google_maps.js');
            $apiKey = htmlspecialcharsbx(Main\Config\Option::get('fileman', 'google_map_api_key', ''));
            ?>
            <script async defer
                    src="<?= $scheme ?>://maps.googleapis.com/maps/api/js?key=<?= $apiKey ?>&callback=bx_gmaps_waiter">
            </script>
            <script>
                function bx_gmaps_waiter() {
                    if (BX.Sale && BX.Sale.OrderAjaxComponent)
                        BX.Sale.OrderAjaxComponent.initMaps();
                    else
                        setTimeout(bx_gmaps_waiter, 100);
                }
            </script>
            <?php
        }
    }

    if ($arParams['USE_YM_GOALS'] === 'Y') {
        ?>
        <script>
            (function bx_counter_waiter(i) {
                i = i || 0;
                if (i > 50)
                    return;

                if (typeof window['yaCounter<?=$arParams['YM_GOALS_COUNTER']?>'] !== 'undefined')
                    BX.Sale.OrderAjaxComponent.reachGoal('initialization');
                else
                    setTimeout(function () {
                        bx_counter_waiter(++i)
                    }, 100);
            })();
        </script>
        <?php
    }
}
