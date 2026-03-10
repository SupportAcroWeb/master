<?php
/**
 * Детальная страница заказа
 *
 * @var CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var string $templateFolder
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Catalog\ProductTable;
use Bitrix\Main\Loader;

\Bitrix\Main\UI\Extension::load([
    'ui.design-tokens',
    'ui.fonts.opensans',
    'clipboard',
    'fx',
]);

if ($arParams['GUEST_MODE'] !== 'Y') {
    Asset::getInstance()->addJs("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/script.js");
    Asset::getInstance()->addCss("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/style.css");
}

$APPLICATION->SetTitle("");

Loader::includeModule('catalog');
Loader::includeModule('iblock');

if (!empty($arResult['ERRORS']['FATAL'])) {
    foreach ($arResult['ERRORS']['FATAL'] as $error) {
        ShowError($error);
    }

    $component = $this->__component;

    if ($arParams['AUTH_FORM_IN_TEMPLATE'] && isset($arResult['ERRORS']['FATAL'][$component::E_NOT_AUTHORIZED])) {
        $APPLICATION->AuthForm('', false, false, 'N', false);
    }
} else {
    if (!empty($arResult['ERRORS']['NONFATAL'])) {
        foreach ($arResult['ERRORS']['NONFATAL'] as $error) {
            ShowError($error);
        }
    }

    // Определяем статус заказа и оплаты
    $orderStatus = $arResult['STATUS'];
    $isCanceled = $arResult['CANCELED'] === 'Y';
    $isPaid = false;
    $paymentStatus = '';
    $bShowPayButton = false;
    $payLink = null;
    $paySystemName = '';
    $paymentAccountNumber = '';

    foreach ($arResult['PAYMENT'] as $payment) {
        $isPaid = $payment['PAID'] === 'Y';
        $paySystemName = $payment['PAY_SYSTEM_NAME'];
        $paymentAccountNumber = $payment['ACCOUNT_NUMBER'];

        // Проверяем, не является ли платеж наличным
        $isCashPayment = (
            (!empty($payment['PAY_SYSTEM']['IS_CASH']) && $payment['PAY_SYSTEM']['IS_CASH'] === 'Y') ||
            (isset($payment['PAY_SYSTEM']['ACTION_FILE']) && strpos($payment['PAY_SYSTEM']['ACTION_FILE'], 'cash') !== false)
        );

        if (!$isPaid && !$isCashPayment && !$isCanceled && $arResult["IS_ALLOW_PAY"] !== "N") {
            $bShowPayButton = true;
            
            // Формируем ссылку на оплату
            $orderAccountNumber = urlencode(urlencode($arResult["ACCOUNT_NUMBER"]));
            $payLink = ($arParams["PATH_TO_PAYMENT"] ?? '/personal/order/payment/') . "?ORDER_ID={$orderAccountNumber}&PAYMENT_ID={$paymentAccountNumber}";
            
            // Если это не новое окно, подготавливаем форму
            if ($payment['PAY_SYSTEM']['PSA_NEW_WINDOW'] !== 'Y') {
                ?>
                <div data-entity="payment-form" style="display:none">
                    <?= $payment['BUFFERED_OUTPUT'] ?>
                </div>
                <?php
            }
        }
    }

    // Определяем визуальный статус заказа (для верстки order-status)
    $statusText = htmlspecialcharsbx($orderStatus['NAME']);
    $orderStatusClassVisual = 'order-status order-status--pending';
    $orderStatusIconSprite = 'status-pending';

    if ($isCanceled) {
        $orderStatusClassVisual = 'order-status order-status--cancel';
        $orderStatusIconSprite = 'status-cancel';
        $statusText = Loc::getMessage('SPOD_ORDER_CANCELED') ?: 'Отменен';
    } elseif (!empty($orderStatus['SEMANTICS']) && $orderStatus['SEMANTICS'] === 'F') {
        $orderStatusClassVisual = 'order-status order-status--success';
        $orderStatusIconSprite = 'status-success';
        $statusText = Loc::getMessage('SPOD_ORDER_COMPLETED') ?: 'Успешно завершен';
    } else {
        $statusText = Loc::getMessage('SPOD_ORDER_PENDING') ?: $statusText;
    }

    // Статус оплаты для sidebar (order-status)
    $paymentStatusText = $isPaid ? 'Оплачен' : 'Не оплачен';
    $paymentStatusClassVisual = $isPaid
        ? 'order-status order-status--success'
        : 'order-status order-status--pending';
    $paymentStatusIconSprite = $isPaid ? 'status-success' : 'status-pending';

    // Получаем изображения товаров
    $basketItemIds = array_column($arResult['BASKET'], 'PRODUCT_ID');
    $offerIds = [];
    $productIDs = [];

    if (!empty($basketItemIds)) {
        $productsData = ProductTable::getList([
            'filter' => ['ID' => $basketItemIds],
            'select' => ['ID', 'TYPE'],
        ]);

        while ($type = $productsData->fetch()) {
            if ($type['TYPE'] == ProductTable::TYPE_OFFER) {
                $offerIds[] = $type['ID'];
            } else {
                $productIDs[] = $type['ID'];
            }
        }
    }

    $productLinks = [];
    $productImages = [];

    if (!empty($offerIds)) {
        $offer = CIBlockElement::GetList(
            [],
            ['ID' => $offerIds],
            false,
            false,
            ['ID', 'IBLOCK_ID', 'PROPERTY_CML2_LINK']
        );
        while ($offerItem = $offer->fetch()) {
            $productLinks[$offerItem['ID']] = $offerItem['PROPERTY_CML2_LINK_VALUE'];
            $productIDs[] = $offerItem['PROPERTY_CML2_LINK_VALUE'];
        }
    }

    foreach ($basketItemIds as $id) {
        if (!isset($productLinks[$id])) {
            $productLinks[$id] = $id;
        }
    }

    if (!empty($productIDs)) {
        $products = CIBlockElement::GetList(
            [],
            ['ID' => $productIDs],
            false,
            false,
            ['ID', 'IBLOCK_ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE']
        );
        while ($productItem = $products->fetch()) {
            $imageId = $productItem['PREVIEW_PICTURE'] ?: $productItem['DETAIL_PICTURE'];
            if ($imageId) {
                $productImages[$productItem['ID']] = CFile::GetPath($imageId);
            }
        }
    }

    // Рассчитываем суммы
    $basketTotalPrice = 0;
    $basketTotalDiscount = 0;

    foreach ($arResult['BASKET'] as $item) {
        $basketTotalPrice += $item['PRICE'] * $item['QUANTITY'];
        $basketTotalDiscount += ($item['BASE_PRICE'] - $item['PRICE']) * $item['QUANTITY'];
    }

    $basketBasePrice = $basketTotalPrice + $basketTotalDiscount;
    $deliveryPrice = (float)$arResult['PRICE_DELIVERY'];
    $totalPrice = (float)$arResult['PRICE'];

    // Группируем свойства по кодам
    $buyerProps = [];
    $deliveryProps = [];
    $additionalProps = [];

    if (isset($arResult["ORDER_PROPS"])) {
        foreach ($arResult["ORDER_PROPS"] as $property) {
            $code = $property['CODE'] ?? '';
            $value = '';

            if ($property["TYPE"] == "Y/N") {
                $value = Loc::getMessage('SPOD_' . ($property["VALUE"] == "Y" ? 'YES' : 'NO'));
            } elseif ($property['MULTIPLE'] == 'Y' && $property['TYPE'] !== 'FILE' && $property['TYPE'] !== 'LOCATION') {
                $propertyList = unserialize($property["VALUE"], ['allowed_classes' => false]);
                $value = implode(', ', $propertyList);
            } elseif ($property['TYPE'] == 'FILE') {
                $value = $property["VALUE"];
            } else {
                if (is_array($property["VALUE"])) {
                    $value = implode(', ', array_map('htmlspecialcharsbx', $property["VALUE"]));
                } else {
                    $value = htmlspecialcharsbx($property["VALUE"]);
                }
            }

            $prop = [
                'NAME' => htmlspecialcharsbx($property['NAME']),
                'VALUE' => $value,
                'CODE' => $code
            ];

            // Распределяем по группам
            if (in_array($code, ['FIO', 'PHONE', 'EMAIL'], true)) {
                $buyerProps[] = $prop;
            } elseif (in_array($code, ['ADDRESS', 'ENTRANCE', 'FLOOR', 'APARTMENT'], true)) {
                $deliveryProps[] = $prop;
            } elseif (in_array($code, ['FIO_MANAGER', 'EMAIL_MANAGER', 'DESCRIPTION'], true)) {
                $additionalProps[] = $prop;
            }
        }
    }
    ?>

    <div class="columns-grid2 block-orderstatus">
        <div class="columns-grid2__content">
            <div class="columns-grid2__content-inner">
                <div class="container-grid1">

                    <!-- Статус заказа -->
                    <div class="container-grid1__row border-container orderstatus">
                        <div class="orderstatus__inner">
                            <h2>
                                Заказ №<?= htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"]) ?>
                                от <?= $arResult["DATE_INSERT_FORMATED"] ?>
                            </h2>
                            <span class="<?= $orderStatusClassVisual ?>">
                                <svg aria-hidden="true" width="30" height="30">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#<?= $orderStatusIconSprite ?>"></use>
                                </svg>
                                <?= $statusText ?>
                            </span>
                        </div>
                    </div>

                    <!-- Способ доставки -->
                    <div class="container-grid1__row_2 border-container">
                        <h2 class="title2 title">Способ доставки</h2>
                        <div class="container-grid1__body">
                            <div class="form-grid1">
                                <div class="form-grid1__row">
                                    <?php
                                    $deliveryName = '';
                                    $deliveryPeriod = '';

                                    if (!empty($arResult['DELIVERY']['NAME'])) {
                                        $deliveryName = htmlspecialcharsbx($arResult['DELIVERY']['NAME']);
                                    } elseif (!empty($arResult['SHIPMENT']) && is_array($arResult['SHIPMENT'])) {
                                        foreach ($arResult['SHIPMENT'] as $shipment) {
                                            if (!empty($shipment['DELIVERY_NAME'])) {
                                                $deliveryName = htmlspecialcharsbx($shipment['DELIVERY_NAME']);
                                                break;
                                            }
                                        }
                                    } elseif (!empty($arResult['DELIVERY']) && is_array($arResult['DELIVERY'])) {
                                        foreach ($arResult['DELIVERY'] as $delivery) {
                                            if (is_array($delivery) && !empty($delivery['NAME'])) {
                                                $deliveryName = htmlspecialcharsbx($delivery['NAME']);
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                    <?php if ($deliveryName !== ''): ?>
                                        <div class="card-richbox">
                                            <div class="card-richbox__inner">
                                                <div class="card-richbox__col1">
                                                    <span class="card-richbox__img">
                                                        <img src="<?= SITE_TEMPLATE_PATH ?>/img/delivery.svg" alt="">
                                                    </span>
                                                    <span class="card-richbox__visual card-richbox__checkout"></span>
                                                </div>
                                                <div class="card-richbox__col2">
                                                    <div class="card-richbox__l">
                                                        <span class="card-richbox__label1"><?= $deliveryName ?></span>
                                                        <?php if ($deliveryPeriod !== ''): ?>
                                                            <span class="card-richbox__label2"><?= $deliveryPeriod ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Способ оплаты -->
                    <div class="container-grid1__row_2 border-container">
                        <h2 class="title2 title">Способ оплаты</h2>
                        <div class="container-grid1__body">
                            <div class="form-grid1">
                                <div class="form-grid1__row">
                                    <div class="card-richbox">
                                        <div class="card-richbox__inner">
                                            <div class="card-richbox__col1">
                                                <span class="card-richbox__img">
                                                    <img src="<?= SITE_TEMPLATE_PATH ?>/img/cash.svg" alt="">
                                                </span>
                                                <span class="card-richbox__visual card-richbox__checkout"></span>
                                            </div>
                                            <div class="card-richbox__col2">
                                                <div class="card-richbox__l">
                                                    <span class="card-richbox__label1"><?= htmlspecialcharsbx($paySystemName) ?></span>
                                                </div>
                                                <?php if ($bShowPayButton && $payLink): ?>
                                                    <div class="card-richbox__r">
                                                        <a href="<?= $payLink ?>" target="_blank" class="card-richbox__btn">Оплатить</a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Покупатель и адрес -->
                    <?php if (!empty($buyerProps) || !empty($deliveryProps) || !empty($additionalProps)): ?>
                        <div class="container-grid1__row border-container">
                            <div class="order-list">
                                <h2 class="title3">Покупатель</h2>
                                <table class="info-table">
                                    <tbody>
                                    <?php foreach ($buyerProps as $prop): ?>
                                        <?php if (!empty($prop['VALUE'])): ?>
                                            <tr>
                                                <td class="label"><?= $prop['NAME'] ?></td>
                                                <td class="value"><?= $prop['VALUE'] ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                    <?php if (!empty($deliveryProps)): ?>
                                        <tr class="section-header">
                                            <td colspan="2">
                                                <strong>Адрес доставки:</strong>
                                            </td>
                                        </tr>
                                        <?php foreach ($deliveryProps as $prop): ?>
                                            <?php if (!empty($prop['VALUE'])): ?>
                                                <tr>
                                                    <td class="label"><?= $prop['NAME'] ?></td>
                                                    <td class="value"><?= $prop['VALUE'] ?></td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php if (!empty($additionalProps)): ?>
                                        <tr class="section-header">
                                            <td colspan="2">
                                                <strong>Дополнительная информация:</strong>
                                            </td>
                                        </tr>
                                        <?php foreach ($additionalProps as $prop): ?>
                                            <?php if (!empty($prop['VALUE'])): ?>
                                                <tr>
                                                    <td class="label"><?= $prop['NAME'] ?></td>
                                                    <td class="value"><?= $prop['VALUE'] ?></td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Товары в заказе -->
                    <div class="container-grid1__row border-container order-products">
                        <h2 class="title2 title">Товары в заказе</h2>

                        <table class="order-products__table">
                            <tbody class="order-products__body">
                            <?php foreach ($arResult['BASKET'] as $basketItem):
                                $productId = $basketItem['PRODUCT_ID'];
                                $linkedProductId = $productLinks[$productId] ?? $productId;
                                $imagePath = $productImages[$linkedProductId] ?? SITE_TEMPLATE_PATH . '/img/no-image.png';
                                $quantity = $basketItem['QUANTITY'];
                                $price = $basketItem['PRICE'];
                                $basePrice = $basketItem['BASE_PRICE'];
                                $sum = $price * $quantity;
                                $measureName = $basketItem['MEASURE_NAME'] ?: 'шт.';
                                $isPriceZero = (float)$price <= 0;
                                ?>
                                <tr class="order-products__item">
                                    <td class="order-products__cell order-products__cell--preview">
                                        <div class="order-products__preview">
                                            <a href="<?= htmlspecialcharsbx($basketItem['DETAIL_PAGE_URL']) ?>">
                                                <img alt="<?= htmlspecialcharsbx($basketItem['NAME']) ?>" src="<?= htmlspecialcharsbx($imagePath) ?>">
                                            </a>
                                        </div>
                                    </td>

                                    <td class="order-products__cell order-products__cell--info">
                                        <div class="order-products__name">
                                            <a href="<?= htmlspecialcharsbx($basketItem['DETAIL_PAGE_URL']) ?>"><?= htmlspecialcharsbx($basketItem['NAME']) ?></a>
                                        </div>
                                    </td>

                                    <td class="order-products__cell order-products__cell--qty">
                                        <div class="order-products__quantity">
                                            <div class="order-products__price-per-unit">
                                                <?= $quantity ?> <?= htmlspecialcharsbx($measureName) ?>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="order-products__cell order-products__cell--price">
                                        <?php if ($isPriceZero): ?>
                                            <div class="order-products__price2 no_price">Цена по запросу</div>
                                        <?php else: ?>
                                            <div class="order-products__price2">
                                                <?= number_format($sum, 0, '', ' ') ?> ₽
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="columns-grid2__aside">
            <div class="order-summary">
                <h2 class="order-summary__title">Детали заказа</h2>
                <div class="order-summary__order-status">
                    <div class="order-summary__order-status_inner">
                        <span class="<?= $paymentStatusClassVisual ?>">
                            <svg aria-hidden="true" width="16" height="16">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#<?= $paymentStatusIconSprite ?>"></use>
                            </svg>
                            <?= $paymentStatusText ?>
                        </span>
                    </div>
                </div>
                <div class="order-summary__inner">
                    <div class="order-summary__row">
                        <div class="order-summary__info order-summary__row_prise">
                            <span class="order-summary__label">
                                Товары, <?= count($arResult['BASKET']) ?> шт
                            </span>
                            <span class="order-summary__value">
                                <?= number_format($basketBasePrice, 0, '', ' ') ?>
                                <span class="rub">₽</span>
                            </span>
                        </div>
                        <?php if ($basketTotalDiscount > 0): ?>
                            <div class="order-summary__info order-summary__row_old-prise">
                                <span class="order-summary__label">Скидка</span>
                                <span class="order-summary__value order-summary__value_discount">
                                    -<?= number_format($basketTotalDiscount, 0, '', ' ') ?> ₽
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php if ($deliveryPrice > 0): ?>
                            <div class="order-summary__info order-summary__row_delivery">
                                <span class="order-summary__label">Доставка</span>
                                <span class="order-summary__value">
                                    <?= number_format($deliveryPrice, 0, '', ' ') ?>
                                    <span class="rub">₽</span>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="order-summary__total">
                        <span class="order-summary__total-label">Итого:</span>
                        <span class="order-summary__total-value">
                            <?= number_format($totalPrice, 0, '', ' ') ?>
                            <span class="rub">₽</span>
                        </span>
                    </div>
                </div>

                <div class="order-summary__btns">
                    <a
                        class="btn btn_small btn_black btn_wide"
                        href="<?= htmlspecialcharsbx($arResult["URL_TO_COPY"]) ?>"
                    >
                        Заказать повторно
                    </a>
                </div>

            </div>
        </div>

    </div>

    <?php
    $javascriptParams = [
        "url" => CUtil::JSEscape($this->__component->GetPath() . '/ajax.php'),
        "templateFolder" => CUtil::JSEscape($templateFolder),
        "templateName" => $this->__component->GetTemplateName(),
        "paymentList" => $paymentData ?? [],
        "returnUrl" => $arResult['RETURN_URL'],
    ];
    $javascriptParams = CUtil::PhpToJSObject($javascriptParams);
    ?>
    <script>
        BX.Sale.PersonalOrderComponent.PersonalOrderDetail.init(<?= $javascriptParams ?>);
    </script>
    <?php
}
