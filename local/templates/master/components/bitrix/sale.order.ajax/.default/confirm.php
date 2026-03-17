<?php
/**
 * Страница подтверждения заказа
 * 
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Order;
use Bitrix\Iblock\ElementTable;
use Bitrix\Catalog\ProductTable;
use Bitrix\Main\Loader;

if ($arParams["SET_TITLE"] == "Y") {
    $APPLICATION->SetTitle("Заказ оформлен");
}

Loader::includeModule('sale');
Loader::includeModule('catalog');
Loader::includeModule('iblock');

if (!empty($arResult["ORDER"])):
    $orderId = $arResult["ORDER"]['ID'];
    $order = Order::load($orderId);

    if ($order):
        // Получаем свойства заказа
        $propertyCollection = $order->getPropertyCollection();
        $properties = [];
        foreach ($propertyCollection as $property) {
            $properties[$property->getField('CODE')] = $property->getValue();
        }

        // Получаем доставку
        $shipmentCollection = $order->getShipmentCollection();
        $shipment = null;
        foreach ($shipmentCollection as $shipmentItem) {
            if (!$shipmentItem->isSystem()) {
                $shipment = $shipmentItem;
                break;
            }
        }
        $deliveryService = $shipment ? $shipment->getDelivery() : null;

        // Получаем оплату
        $paymentCollection = $order->getPaymentCollection();
        $payment = null;
        foreach ($paymentCollection as $paymentItem) {
            if (!$paymentItem->isInner()) {
                $payment = $paymentItem;
                break;
            }
        }
        $paySystemName = $payment ? $payment->getPaymentSystemName() : '';

        // Получаем корзину
        $basket = $order->getBasket();
        $basketItems = $basket->getBasketItems();

        // Получаем ID товаров
        $basketItemIds = [];
        foreach ($basketItems as $item) {
            $basketItemIds[] = $item->getProductId();
        }

        // Определяем типы товаров (товар или торговое предложение)
        $offerIds = [];
        $productIDs = [];
        if (!empty($basketItemIds)) {
            $productsData = ProductTable::getList([
                'filter' => ['ID' => $basketItemIds],
                'select' => ['ID', 'TYPE'],
            ]);

            while ($type = $productsData->fetch()) {
                if ($type['TYPE'] == \Bitrix\Catalog\ProductTable::TYPE_OFFER) {
                    $offerIds[] = $type['ID'];
                } else {
                    $productIDs[] = $type['ID'];
                }
            }
        }

        // Получаем связи торговых предложений с товарами
        $productLinks = [];
        $productImages = [];
        if (!empty($offerIds)) {
            $offer = \CIBlockElement::GetList(
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

        // Получаем изображения товаров
        if (!empty($productIDs)) {
            $products = \CIBlockElement::GetList(
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
        $basketPrice = $basket->getPrice();
        $basketBasePrice = $basket->getBasePrice();
        $basketDiscountSum = $basketBasePrice - $basketPrice;
        $deliveryPrice = $order->getDeliveryPrice();
        $totalPrice = $order->getPrice();
        $personalOrdersUrl = $arParams['PATH_TO_PERSONAL'] ?? '/personal/';
        $personalOrdersUrl = rtrim($personalOrdersUrl, '/') . '/moi-zakazy/detail/' . $arResult["ORDER"]["ACCOUNT_NUMBER"] . '/';
        ?>

<div class="order-header">
    <h1 class="title3">Заказ успешно оформлен</h1>
</div>
<div class="textblock textblock_1">
    <p>Номер Вашего заказа <a class="primary" href="<?= htmlspecialcharsbx($personalOrdersUrl) ?>"><?= htmlspecialcharsbx($arResult["ORDER"]["ACCOUNT_NUMBER"]) ?></a>. За ходом выполнения заказа можно следить в разделе личного кабинета <a class="primary" href="<?= htmlspecialcharsbx($personalOrdersUrl) ?>">«Мои заказы»</a></p>
</div>
<div class="columns-grid2 block-order3">
    <div class="columns-grid2__content">
        <div class="columns-grid2__content-inner">
            <div class="container-grid1">
                <!-- Способ доставки -->
                <div class="container-grid1__row_2 border-container">
                    <h2 class="title2 title">Способ доставки</h2>
                    <div class="container-grid1__body">
                        <div class="form-grid1">
                            <div class="form-grid1__row">
                                <div class="card-richbox">
                                    <div class="card-richbox__inner">
                                        <div class="card-richbox__col1">
                                            <span class="card-richbox__img">
                                                <?php
                                                $deliveryName = $shipment ? $shipment->getDeliveryName() : '';
                                                $deliveryIcon = (stripos((string)$deliveryName, 'самовывоз') !== false)
                                                    ? SITE_TEMPLATE_PATH . '/img/pickup.svg'
                                                    : SITE_TEMPLATE_PATH . '/img/delivery.svg';
                                                ?>
                                                <img src="<?= htmlspecialcharsbx($deliveryIcon) ?>" alt="">
                                            </span>
                                            <span class="card-richbox__visual card-richbox__checkout"></span>
                                        </div>
                                        <div class="card-richbox__col2">
                                            <div class="card-richbox__l">
                                                <span class="card-richbox__label1"><?= $shipment ? htmlspecialcharsbx($shipment->getDeliveryName()) : '' ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                                <?php
                                                $paySystemIcon = SITE_TEMPLATE_PATH . '/img/cards.svg';
                                                if ($payment && !empty($arResult['PAY_SYSTEM_LIST_BY_PAYMENT_ID'][$payment->getId()])) {
                                                    $arPaySystemLogo = $arResult['PAY_SYSTEM_LIST_BY_PAYMENT_ID'][$payment->getId()];
                                                    if (!empty($arPaySystemLogo['LOGOTIP']['SRC'])) {
                                                        $paySystemIcon = $arPaySystemLogo['LOGOTIP']['SRC'];
                                                    } elseif (!empty($arPaySystemLogo['PSA_LOGOTIP_SRC'])) {
                                                        $paySystemIcon = $arPaySystemLogo['PSA_LOGOTIP_SRC'];
                                                    } elseif (!empty($arPaySystemLogo['LOGOTIP_SRC'])) {
                                                        $paySystemIcon = $arPaySystemLogo['LOGOTIP_SRC'];
                                                    } elseif (
                                                        (!empty($arPaySystemLogo['IS_CASH']) && $arPaySystemLogo['IS_CASH'] === 'Y') ||
                                                        (isset($arPaySystemLogo['ACTION_FILE']) && strpos($arPaySystemLogo['ACTION_FILE'], '/cash') !== false)
                                                    ) {
                                                        $paySystemIcon = SITE_TEMPLATE_PATH . '/img/cash.svg';
                                                    }
                                                }
                                                ?>
                                                <img src="<?= htmlspecialcharsbx($paySystemIcon) ?>" alt="<?= htmlspecialcharsbx($paySystemName) ?>">
                                            </span>
                                            <span class="card-richbox__visual card-richbox__checkout"></span>
                                        </div>
                                        <div class="card-richbox__col2">
                                            <div class="card-richbox__l">
                                                <span class="card-richbox__label1"><?= htmlspecialcharsbx($paySystemName) ?></span>
                                            </div>
                                            <?php
                                            if ($arResult["ORDER"]["IS_ALLOW_PAY"] === 'Y' && $payment && $payment->getField('PAID') != 'Y') {
                                                if (!empty($arResult['PAY_SYSTEM_LIST']) && array_key_exists($payment->getPaymentSystemId(), $arResult['PAY_SYSTEM_LIST'])) {
                                                    $arPaySystem = $arResult['PAY_SYSTEM_LIST_BY_PAYMENT_ID'][$payment->getId()];
                                                    if (empty($arPaySystem["ERROR"])) {
                                                        $isCashPayment = (
                                                            (!empty($arPaySystem["IS_CASH"]) && $arPaySystem["IS_CASH"] === 'Y') ||
                                                            (isset($arPaySystem["ACTION_FILE"]) && strpos($arPaySystem["ACTION_FILE"], '/cash') !== false)
                                                        );
                                                        if (!$isCashPayment) {
                                                            $orderAccountNumber = urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]));
                                                            $paymentAccountNumber = $payment->getField("ACCOUNT_NUMBER");
                                                            ?>
                                                            <a target="_blank" href="<?= $arParams["PATH_TO_PAYMENT"] ?>?ORDER_ID=<?= $orderAccountNumber ?>&PAYMENT_ID=<?= $paymentAccountNumber ?>" class="btn btn_primary">Выставить счёт</a>
                                                            <?php
                                                        }
                                                    }
                                                }
                                            }
                                            if ($payment && $payment->getField('PAID') == 'Y') {
                                                ?><span class="order-paid">Оплачен</span><?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Покупатель и адрес -->
                <div class="container-grid1__row border-container">
                    <div class="order-list">
                        <h2 class="title3">Покупатель</h2>
                        <table class="info-table">
                            <tbody>
                            <?php if (!empty($properties['FIO'])): ?>
                            <tr>
                                <td class="label">Ф.И.О.</td>
                                <td class="value"><?= htmlspecialcharsbx($properties['FIO']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($properties['EMAIL'])): ?>
                            <tr>
                                <td class="label">E-mail</td>
                                <td class="value"><?= htmlspecialcharsbx($properties['EMAIL']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($properties['PHONE'])): ?>
                            <tr>
                                <td class="label">Телефон</td>
                                <td class="value"><?= htmlspecialcharsbx($properties['PHONE']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($properties['ADDRESS']) || !empty($properties['ENTRANCE']) || !empty($properties['FLOOR']) || !empty($properties['APARTMENT'])): ?>
                            <tr class="section-header">
                                <td colspan="2"><strong>Адрес доставки:</strong></td>
                            </tr>
                            <?php if (!empty($properties['ADDRESS'])): ?>
                            <tr>
                                <td class="label">Адрес</td>
                                <td class="value"><?= htmlspecialcharsbx($properties['ADDRESS']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($properties['ENTRANCE'])): ?>
                            <tr>
                                <td class="label">Подъезд</td>
                                <td class="value"><?= htmlspecialcharsbx($properties['ENTRANCE']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($properties['FLOOR'])): ?>
                            <tr>
                                <td class="label">Этаж</td>
                                <td class="value"><?= htmlspecialcharsbx($properties['FLOOR']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($properties['APARTMENT'])): ?>
                            <tr>
                                <td class="label">Квартира</td>
                                <td class="value"><?= htmlspecialcharsbx($properties['APARTMENT']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php endif; ?>
                            <?php if (!empty($properties['DESCRIPTION'])): ?>
                            <tr class="section-header">
                                <td colspan="2"><strong>Комментарий к заказу:</strong></td>
                            </tr>
                            <tr>
                                <td colspan="2"><?= htmlspecialcharsbx($properties['DESCRIPTION']) ?></td>
                            </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Товары в заказе -->
                <div class="container-grid1__row border-container order-products">
                    <h2 class="title2 title">Товары в заказе</h2>
                    <table class="order-products__table">
                        <tbody class="order-products__body">
                        <?php foreach ($basketItems as $item):
                            $productId = $item->getProductId();
                            $linkedProductId = $productLinks[$productId] ?? $productId;
                            $imagePath = $productImages[$linkedProductId] ?? SITE_TEMPLATE_PATH . '/img/no-image.png';
                            $quantity = $item->getQuantity();
                            $price = $item->getPrice();
                            $basePrice = $item->getBasePrice();
                            $sum = $price * $quantity;
                            $measureName = $item->getField('MEASURE_NAME') ?: 'шт.';
                            $isPriceZero = (float)$price <= 0;
                            $canBuy = $item->getField('CAN_BUY') === 'Y' || $item->getField('CAN_BUY') === true;
                            $itemProps = [];
                            $allowedPropCodes = ['WIDTH', 'HEIGHT'];
                            foreach ($item->getPropertyCollection() as $prop) {
                                $propCode = $prop->getField('CODE');
                                if (!in_array((string)$propCode, $allowedPropCodes, true)) {
                                    continue;
                                }
                                $propName = $prop->getField('NAME');
                                $propValue = $prop->getField('VALUE');
                                if ($propName !== null && $propName !== '' && $propValue !== null && $propValue !== '') {
                                    $itemProps[] = ['NAME' => $propName, 'VALUE' => $propValue];
                                }
                            }
                            ?>
                        <tr class="order-products__item">
                            <td class="order-products__cell order-products__cell--preview">
                                <div class="order-products__preview">
                                    <a href="<?= htmlspecialcharsbx($item->getField('DETAIL_PAGE_URL')) ?>">
                                        <img alt="<?= htmlspecialcharsbx($item->getField('NAME')) ?>" src="<?= htmlspecialcharsbx($imagePath) ?>">
                                    </a>
                                </div>
                            </td>
                            <td class="order-products__cell order-products__cell--info">
                                <div class="order-products__name">
                                    <a href="<?= htmlspecialcharsbx($item->getField('DETAIL_PAGE_URL')) ?>"><?= htmlspecialcharsbx($item->getField('NAME')) ?></a>
                                </div>
                                <?php if (!empty($itemProps)): ?>
                                <div class="order-products__parameters">
                                    <div class="order-products__specs">
                                        <?php foreach ($itemProps as $prop): ?>
                                        <div class="order-products__spec"><?= htmlspecialcharsbx($prop['NAME']) ?><span><?= htmlspecialcharsbx($prop['VALUE']) ?></span></div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="order-products__status <?= $canBuy ? 'instock' : 'outofstock' ?>"><?= $canBuy ? 'В наличии' : 'Нет в наличии' ?></div>
                            </td>
                            <td class="order-products__cell order-products__cell--qty">
                                <div class="order-products__quantity">
                                    <div class="order-products__price-per-unit"><?= $quantity ?> <?= htmlspecialcharsbx($measureName) ?></div>
                                </div>
                            </td>
                            <td class="order-products__cell order-products__cell--price">
                                <?php if ($isPriceZero): ?>
                                <div class="order-products__price2">Цена по запросу</div>
                                <?php else: ?>
                                <?php if ($basePrice > $price): ?>
                                <div class="order-products__price1"><?= number_format($basePrice, 0, '', ' ') ?>&nbsp;₽</div>
                                <?php endif; ?>
                                <div class="order-products__price2"><?= number_format($sum, 0, '', ' ') ?>&nbsp;₽</div>
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
            <div class="order-summary__inner">
                <div class="order-summary__row">
                    <div class="order-summary__info order-summary__row_prise">
                        <span class="order-summary__label">Товары, <?= count($basketItems) ?> шт.</span>
                        <span class="order-summary__value"><?= number_format($basketBasePrice, 0, '', ' ') ?> <span class="rub">₽</span></span>
                    </div>
                    <?php if ($basketDiscountSum > 0): ?>
                    <div class="order-summary__info order-summary__row_old-prise">
                        <span class="order-summary__label">Скидка</span>
                        <span class="order-summary__value order-summary__value_discount">-<?= number_format($basketDiscountSum, 0, '', ' ') ?> ₽</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($deliveryPrice > 0): ?>
                    <div class="order-summary__info order-summary__row_delivery">
                        <span class="order-summary__label">Доставка</span>
                        <span class="order-summary__value"><?= number_format($deliveryPrice, 0, '', ' ') ?> <span class="rub">₽</span></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="order-summary__total">
                <span class="order-summary__total-label">Итого:</span>
                <span class="order-summary__total-value"><?= number_format($totalPrice, 0, '', ' ') ?> <span class="rub">₽</span></span>
            </div>
        </div>
    </div>
</div>

    <?php endif; ?>

<?php else: ?>

<div class="container">
    <div class="heading-cols1">
        <h1 class="title2">Ошибка оформления заказа</h1>
        <p>
            К сожалению, при оформлении заказа произошла ошибка. Пожалуйста, попробуйте еще раз или свяжитесь с нами.
        </p>
    </div>
</div>

<?php endif; ?>