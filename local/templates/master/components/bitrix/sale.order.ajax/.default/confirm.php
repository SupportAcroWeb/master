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
        ?>

<div class="block-order block-order-info">
    <div class="container">
        <div class="heading-cols1">
            <h1 class="title2">заказ успешно оформлен</h1>
            <p>
                Номер Вашего заказа <span><?= htmlspecialcharsbx($arResult["ORDER"]["ACCOUNT_NUMBER"]) ?></span>. За ходом выполнения заказа можно следить в
                разделе личного кабинета <a href="/personal/moi-zakazy/">«Мои заказы»</a>
            </p>
        </div>
    </div>
    <div class="container container_bordered1">
        <div class="block-order__content">
            <div class="block-order__body block1">
                
                <!-- Данные покупателя -->
                <div class="block-order__column">
                    <div class="block-order__title">
                        данные покупателя
                    </div>
                    <ul class="block-order__details">
                        <?php if (!empty($properties['FIO'])): ?>
                        <li>
                            <span>ФИО:</span> <?= htmlspecialcharsbx($properties['FIO']) ?>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($properties['PHONE'])): ?>
                        <li>
                            <span>Телефон:</span> <?= htmlspecialcharsbx($properties['PHONE']) ?>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($properties['EMAIL'])): ?>
                        <li>
                            <span>E-mail:</span> <?= htmlspecialcharsbx($properties['EMAIL']) ?>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Данные организации -->
                <?php if (!empty($properties['COMPANY']) || !empty($properties['CONTACT_PERSON']) || !empty($properties['PHONE_PERSON'])): ?>
                <div class="block-order__column">
                    <div class="block-order__title">
                        данные организации
                    </div>
                    <ul class="block-order__details">
                        <?php if (!empty($properties['COMPANY'])): ?>
                        <li>
                            <span>Название организации:</span> <?= htmlspecialcharsbx($properties['COMPANY']) ?>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($properties['CONTACT_PERSON'])): ?>
                        <li>
                            <span>ФИО контактного лица:</span> <?= htmlspecialcharsbx($properties['CONTACT_PERSON']) ?>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($properties['PHONE_PERSON'])): ?>
                        <li>
                            <span>Телефон контактного лица:</span> <?= htmlspecialcharsbx($properties['PHONE_PERSON']) ?>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Способы доставки и адрес -->
                <div class="block-order__grid">
                    <div class="block-order__row">
                        <div class="block-order__title">
                            способы доставки
                        </div>
                        <div class="checkbox-text">
                            <div class="checkbox-text__label">
                                <div class="checkbox-text__titles">
                                    <div class="checkbox-text__title"><?= $shipment ? htmlspecialcharsbx($shipment->getDeliveryName()) : '' ?></div>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($properties['ADDRESS']) || !empty($properties['ENTRANCE']) || !empty($properties['FLOOR']) || !empty($properties['APARTMENT'])): ?>
                        <div class="addres-block-order _active">
                            <div class="addres-block-order__title">Адрес доставки</div>
                            <ul class="block-order__details">
                                <?php if (!empty($properties['ADDRESS'])): ?>
                                <li>
                                    <span>Адрес доставки:</span> <?= htmlspecialcharsbx($properties['ADDRESS']) ?>
                                </li>
                                <?php endif; ?>
                                <?php if (!empty($properties['ENTRANCE'])): ?>
                                <li>
                                    <span>Подъезд:</span> <?= htmlspecialcharsbx($properties['ENTRANCE']) ?>
                                </li>
                                <?php endif; ?>
                                <?php if (!empty($properties['FLOOR'])): ?>
                                <li>
                                    <span>Этаж:</span> <?= htmlspecialcharsbx($properties['FLOOR']) ?>
                                </li>
                                <?php endif; ?>
                                <?php if (!empty($properties['APARTMENT'])): ?>
                                <li>
                                    <span>Квартира:</span> <?= htmlspecialcharsbx($properties['APARTMENT']) ?>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Способы оплаты -->
                    <div class="block-order__row">
                        <div class="block-order__title">
                            способы оплаты
                        </div>
                        <div class="checkbox-text payment">
                            <div class="checkbox-text__label">
                                <div class="checkbox-text__titles">
                                    <div class="checkbox-text__title"><?= htmlspecialcharsbx($paySystemName) ?></div>
                                </div>
                            </div>
                            <?php
                            // Кнопка оплаты (только для безналичных платежей)
                            if ($arResult["ORDER"]["IS_ALLOW_PAY"] === 'Y' && $payment && $payment->getField('PAID') != 'Y') {
                                if (!empty($arResult['PAY_SYSTEM_LIST']) && array_key_exists($payment->getPaymentSystemId(), $arResult['PAY_SYSTEM_LIST'])) {
                                    $arPaySystem = $arResult['PAY_SYSTEM_LIST_BY_PAYMENT_ID'][$payment->getId()];
                                    
                                    if (empty($arPaySystem["ERROR"])) {
                                        // Проверяем, является ли платеж наличным
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
                            if ($payment->getField('PAID') == 'Y') { ?>
                                Оплачен
                            <?
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Дополнительная информация -->
                <?php if (!empty($properties['FIO_MANAGER']) || !empty($properties['DESCRIPTION'])): ?>
                <div class="block-order__column">
                    <div class="block-order__title">
                        дополнительная информация
                    </div>
                    <ul class="block-order__details">
                        <?php if (!empty($properties['FIO_MANAGER'])): ?>
                        <li>
                            <span>Личный менеджер:</span> <?= htmlspecialcharsbx($properties['FIO_MANAGER']) ?>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($properties['DESCRIPTION'])): ?>
                        <li>
                            <span>Комментарий к заказу:</span> <?= htmlspecialcharsbx($properties['DESCRIPTION']) ?>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Товары в заказе -->
                <div class="block-order__column">
                    <div class="block-order__title">
                        товары в заказе
                    </div>
                    <div class="block-order__cards">
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
                            ?>
                        <div class="card-product3 card-product4">
                            <div class="card-product3__col-photo">
                                <a href="<?= htmlspecialcharsbx($item->getField('DETAIL_PAGE_URL')) ?>">
                                    <img src="<?= htmlspecialcharsbx($imagePath) ?>" alt="<?= htmlspecialcharsbx($item->getField('NAME')) ?>">
                                </a>
                            </div>
                            <div class="card-product3__col-data">
                                <div class="card-product3__name">
                                    <a href="<?= htmlspecialcharsbx($item->getField('DETAIL_PAGE_URL')) ?>"><?= htmlspecialcharsbx($item->getField('NAME')) ?></a>
                                </div>
                            </div>
                            <div class="card-product3__col1"<?= $isPriceZero ? ' style="display: none;"' : '' ?>>
                                <div>
                                    <div class="card-product3__label1">С НДС (1 <?= htmlspecialcharsbx($measureName) ?>)</div>
                                    <span class="card-product3__price1"><?= number_format($price, 0, '', ' ') ?> ₽</span>
                                    <?php if ($basePrice > $price): ?>
                                    <span class="card-product3__price2"><?= number_format($basePrice, 0, '', ' ') ?> ₽</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($isPriceZero): ?>
                            <div class="card-product3__col1">
                                <div>
                                    <div class="card-product3__label1">С НДС (1 <?= htmlspecialcharsbx($measureName) ?>)</div>
                                    <span class="card-product3__price1 no_price">Цена по запросу</span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="card-product3__col3">
                                <span><?= $quantity ?> <?= htmlspecialcharsbx($measureName) ?></span>
                            </div>
                            <div class="card-product3__col2"<?= $isPriceZero ? ' style="display: none;"' : '' ?>>
                                <div>
                                    <span>На сумму</span>
                                    <span class="card-product3__price3"><?= number_format($sum, 0, '', ' ') ?> ₽</span>
                                </div>
                            </div>
                            <?php if ($isPriceZero): ?>
                            <div class="card-product3__col2">
                                <div>
                                    <span class="card-product3__price3 no_price">Цена по запросу</span>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Детали заказа -->
            <div class="details-block-cart">
                <div class="details-block-cart__sticky">
                    <div class="details-block-cart__list">
                        <div class="details-block-cart__title">
                            Ваш заказ
                        </div>
                        <div class="details-block-cart__item">
                            <div class="details-block-cart__product">Товары, <?= count($basketItems) ?> шт.</div>
                            <div class="details-block-cart__cost"><?= number_format($basketBasePrice, 0, '', ' ') ?> ₽</div>
                        </div>
                        <?php if ($basketDiscountSum > 0): ?>
                        <div class="details-block-cart__item sale">
                            <div class="details-block-cart__product">Скидка</div>
                            <div class="details-block-cart__cost">-<?= number_format($basketDiscountSum, 0, '', ' ') ?> ₽</div>
                        </div>
                        <?php endif; ?>
                        <?php if ($deliveryPrice > 0): ?>
                        <div class="details-block-cart__item delivery">
                            <div class="details-block-cart__product">Доставка</div>
                            <div class="details-block-cart__cost"><?= number_format($deliveryPrice, 0, '', ' ') ?> ₽</div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="details-block-cart__totals">
                        <div class="details-block-cart__title">Сумма заказа:</div>
                        <div class="details-block-cart__summ"><?= number_format($totalPrice, 0, '', ' ') ?> ₽</div>
                    </div>
                </div>
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