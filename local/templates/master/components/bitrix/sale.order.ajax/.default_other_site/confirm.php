<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Order;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Catalog\ProductTable;
use Bitrix\Main\Loader;

/**
 * @var array $arParams
 * @var array $arResult
 * @var $APPLICATION CMain
 */

if ($arParams["SET_TITLE"] == "Y") {
    $APPLICATION->SetTitle(Loc::getMessage("SOA_ORDER_COMPLETE"));
}

Loader::includeModule('sale');
Loader::includeModule('catalog');
Loader::includeModule('iblock');

if (!empty($arResult["ORDER"])):
    $orderId = $arResult["ORDER"]['ID'];
    $order = Order::load($orderId);

    if ($order) {
        $propertyCollection = $order->getPropertyCollection();
        $properties = [];
        foreach ($propertyCollection as $property) {
            $properties[$property->getField('CODE')] = $property->getValue();
        }

        $shipmentCollection = $order->getShipmentCollection();
        $shipment = $shipmentCollection->current();
        $deliveryService = $shipment ? $shipment->getDelivery() : null;

        $paymentCollection = $order->getPaymentCollection();
        $payment = $paymentCollection->current();
        $paySystemName = $payment ? $payment->getPaymentSystemName() : '';

        $basket = $order->getBasket();
        $basketItems = $basket->getBasketItems();

        $basketItemIds = array_map(function ($item) {
            return $item->getProductId();
        }, $basketItems);

        $offerIds = [];
        $offerArticles = [];
        $productIDs = [];
        $productsData = ProductTable::getList([
            'filter' => ['ID' => $basketItemIds],
            'select' => ['ID', 'TYPE'],
        ]);

        while ($type = $productsData->fetch()) {
            if ($type['TYPE'] === '4') {
                $offerIds[] = $type['ID'];
            } else {
                $productIDs[] = $type['ID'];
            }
        }

        $productLinks = [];
        $productImages = [];
        $productArticles = [];
        if ($offerIds) {
            $offer = \CIBlockElement::GetList(
                [],
                ['ID' => $offerIds],
                false,
                false,
                ['ID', 'IBLOCK_ID', 'PROPERTY_CML2_LINK', 'PROPERTY_ARTICLE']
            );
            while ($offerItem = $offer->fetch()) {
                $productLinks[$offerItem['ID']] = $offerItem['PROPERTY_CML2_LINK_VALUE'];
                $productIDs[] = $offerItem['PROPERTY_CML2_LINK_VALUE'];
                $offerArticles[$offerItem['ID']] = $offerItem['PROPERTY_ARTICLE_VALUE'];
            }
        }

        foreach ($productIDs as $id) {
            if (!isset($productLinks[$id])) {
                $productLinks[$id] = $id;
            }
        }

        if ($productIDs) {
            $products = \CIBlockElement::GetList(
                [],
                ['ID' => $productIDs],
                false,
                false,
                ['ID', 'IBLOCK_ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'PROPERTY_ARTICLE']
            );
            while ($productItem = $products->fetch()) {
                $imageId = $productItem['PREVIEW_PICTURE'] ?: $productItem['DETAIL_PICTURE'];
                if ($imageId) {
                    $productImages[$productItem['ID']] = CFile::GetPath($imageId);
                }
                $productArticles[$productItem['ID']] = $productItem['PROPERTY_ARTICLE_VALUE'];
            }
        }

        $isPickup = false;
        if ($deliveryService) {
            $deliveryId = $deliveryService->getId();
            $arDelivery = \Bitrix\Sale\Delivery\Services\Manager::getById($deliveryId);
            $isPickup = $arDelivery['CLASS_NAME'] === '\Bitrix\Sale\Delivery\Services\Configurable' &&
                $arDelivery['NAME'] === 'Самовывоз';
        }

        $pickupPoint = '';
        if ($isPickup && $shipment) {
            $storeId = $shipment->getStoreId();
            if ($storeId) {
                $store = \Bitrix\Catalog\StoreTable::getById($storeId)->fetch();
                if ($store) {
                    $pickupPoint = '<div class="contact">';

                    if (!empty($store['ADDRESS'])) {
                        $pickupPoint .= '<div class="contact__item">
                    <svg aria-hidden="true" width="15" height="18">
                        <use xlink:href="' . SITE_TEMPLATE_PATH . '/img/sprite.svg#location"></use>
                    </svg>
                    <span>' . htmlspecialchars($store['ADDRESS']) . '</span>
                </div>';
                    }

                    if (!empty($store['PHONE'])) {
                        $pickupPoint .= '<a href="tel:' . preg_replace('/[^0-9+]/', '', $store['PHONE']) . '" class="contact__item">
                    <svg aria-hidden="true" width="18" height="18">
                        <use xlink:href="' . SITE_TEMPLATE_PATH . '/img/sprite.svg#phone"></use>
                    </svg>
                    <span>' . htmlspecialchars($store['PHONE']) . '</span>
                </a>';
                    }

                    if (!empty($store['SCHEDULE'])) {
                        $pickupPoint .= '<div class="contact__item">
                    <svg aria-hidden="true" width="18" height="18">
                        <use xlink:href="' . SITE_TEMPLATE_PATH . '/img/sprite.svg#clock"></use>
                    </svg>
                    <span>' . htmlspecialchars($store['SCHEDULE']) . '</span>
                </div>';
                    }

                    $pickupPoint .= '</div>';
                }
            }
        }
        ?>

        <div class="heading-complex">
            <div class="heading-complex__cart">
                <div class="heading-complex__order-info">
                    <svg aria-hidden="true" width="60" height="60">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#check1"></use>
                    </svg>
                    <div class="heading-complex__order-info-titles">
                        <h1 class="title2">Заказ успешно оформлен</h1>
                        <p>
                            Номер Вашего заказа <span><?= $arResult['ORDER']['ACCOUNT_NUMBER'] ?></span>. Вся информация
                            по заказу придет на Ваш почтовый адрес.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="block-order block2">
            <div class="container">
                <form data-validate action="#" class="block-order__content">
                    <div class="block-order__body">
                        <div class="block-order__column">
                            <div class="block-order__title">Покупатель</div>
                            <ul>
                                <li><?= $properties['FIO'] ?? '' ?></li>
                                <?php if (!empty($properties['PHONE'])): ?>
                                    <li>
                                        <svg aria-hidden="true" width="18" height="18">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#phone"></use>
                                        </svg>
                                        <span><?= $properties['PHONE'] ?></span>
                                    </li>
                                <?php endif; ?>
                                <?php if (!empty($properties['EMAIL'])): ?>
                                    <li>
                                        <svg aria-hidden="true" width="18" height="14">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#email"></use>
                                        </svg>
                                        <span><?= $properties['EMAIL'] ?></span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="block-order__column">
                            <div class="block-order__title">Информация о доставке</div>
                            <div class="bottom-block-order info">
                                <div class="bottom-block-order__title"><?= $shipment ? $shipment->getDeliveryName() : '' ?></div>
                                <div class="bottom-block-order__body">
                                    <ul>
                                        <?php if ($isPickup): ?>
                                            <li><?= $pickupPoint ?></li>
                                        <?php else: ?>
                                            <?php if (!empty($properties['ADDRESS'])): ?>
                                                <li>Адрес: <?= $properties['ADDRESS'] ?? '' ?></li>
                                            <?php endif; ?>
                                            <?php if (!empty($properties['ENTRANCE'])): ?>
                                                <li>Подъезд: <?= $properties['ENTRANCE'] ?></li>
                                            <?php endif; ?>
                                            <?php if (!empty($properties['FLOOR'])): ?>
                                                <li>Этаж: <?= $properties['FLOOR'] ?></li>
                                            <?php endif; ?>
                                            <?php if (!empty($properties['APARTMENT'])): ?>
                                                <li>Квартира: <?= $properties['APARTMENT'] ?></li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="block-order__column">
                            <div class="block-order__title">Товары в заказе</div>
                            <div class="cards-product">
                                <?php foreach ($basketItems as $item):
                                    $productId = $item->getProductId();
                                    $linkedProductId = $productLinks[$productId] ?? $productId;
                                    $imagePath = $productImages[$linkedProductId] ?? SITE_TEMPLATE_PATH . '/img/no-image.png';
                                    ?>
                                    <div class="card-product">
                                        <div class="card-product__image">
                                            <img loading="lazy" src="<?= $imagePath ?>"
                                                 alt="<?= $item->getField('NAME') ?>">
                                        </div>
                                        <div class="card-product__item">
                                            <a href="<?= $item->getField('DETAIL_PAGE_URL') ?>"
                                               class="card-product__top">
                                                <?
                                                $articles = $offerArticles[$productId] ?? $productArticles[$linkedProductId] ?? $item->getField('ARTICLE');
                                                ?>
                                                <?
                                                if ($articles) {
                                                ?>
                                                    <div class="card-product__article">
                                                        Артикул: <?= $articles ?>
                                                    </div>
                                                <?
                                                }
                                                ?>
                                                <div class="card-product__title">
                                                    <?= $item->getField('NAME') ?>
                                                </div>
                                                <div class="card-product__stock">
                                                    В наличии
                                                </div>
                                            </a>
                                            <div class="card-product__bottom">
                                                <div class="card-product__stepcounter">
                                                    <span><?= $item->getQuantity() ?> <?= $item->getField('MEASURE_NAME') ?></span>
                                                </div>
                                                <div class="card-product__prices">
                                                    <?php if ($item->getDiscountPrice() > 0): ?>
                                                        <div class="card-product__old-price">
                                                            <s><?= SaleFormatCurrency($item->getBasePrice(), \Bitrix\Currency\CurrencyManager::getBaseCurrency()) ?></s>
                                                            <div class="card-product__sale">
                                                                -<?= round(($item->getBasePrice() - $item->getPrice()) / $item->getBasePrice() * 100) ?>
                                                                %
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="card-product__price">
                                                        <?= SaleFormatCurrency($item->getPrice(), \Bitrix\Currency\CurrencyManager::getBaseCurrency()) ?>/<?= $item->getField('MEASURE_NAME') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="detail-cart">
                        <div class="detail-cart__sticky">
                            <div class="detail-cart__title">
                                Детали заказа
                            </div>
                            <?php
                            $basket = $order->getBasket();
                            $basketPrice = $basket->getPrice();
                            $basketBasePrice = $basket->getBasePrice();
                            $basketDiscountSum = $basket->getBasePrice() - $basket->getPrice(); // Вычисляем сумму скидки
                            $deliveryPrice = $order->getDeliveryPrice();
                            $discountPrice = $order->getDiscountPrice();
                            $taxPrice = $order->getTaxPrice();
                            $totalPrice = $order->getPrice();
                            ?>
                            <ul style="border: 0px;">
                                <li>
                                    <div class="detail-cart__name"><?= $basket->count() ?> товара</div>
                                    <div class="detail-cart__value"><?= SaleFormatCurrency($basketBasePrice, \Bitrix\Currency\CurrencyManager::getBaseCurrency()) ?></div>
                                </li>
                                <li>
                                    <div class="detail-cart__name">Доставка</div>
                                    <div class="detail-cart__value"><?= $deliveryPrice > 0 ? SaleFormatCurrency($deliveryPrice, \Bitrix\Currency\CurrencyManager::getBaseCurrency()) : 'Бесплатно' ?></div>
                                </li>
                                <?php if ($basketDiscountSum > 0): ?>
                                    <li>
                                        <div class="detail-cart__name">Скидка</div>
                                        <div class="detail-cart__value green">-<?= SaleFormatCurrency($basketDiscountSum, \Bitrix\Currency\CurrencyManager::getBaseCurrency()) ?></div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            <div class="detail-cart__totals">
                                <div class="detail-cart__total">Итого:</div>
                                <div class="detail-cart__sum"><?= SaleFormatCurrency($basketPrice, \Bitrix\Currency\CurrencyManager::getBaseCurrency()) ?></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    <?php } ?>

<?php else: ?>
    <b><?= Loc::getMessage("SOA_ERROR_ORDER") ?></b>
    <br/><br/>
    <table class="sale_order_full_table">
        <tr>
            <td>
                <?= Loc::getMessage("SOA_ERROR_ORDER_LOST", ["#ORDER_ID#" => htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"])]) ?>
                <?= Loc::getMessage("SOA_ERROR_ORDER_LOST1") ?>
            </td>
        </tr>
    </table>
<?php endif; ?>