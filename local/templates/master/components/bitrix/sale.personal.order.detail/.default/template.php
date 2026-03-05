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

    // Определяем иконку и класс статуса
    $statusClass = 'status_delivery';
    $statusIcon = 'time';
    $statusText = htmlspecialcharsbx($orderStatus['NAME']);

    if ($isCanceled) {
        $statusClass = 'status_cancel';
        $statusIcon = 'cancel1';
        $statusText = Loc::getMessage('SPOD_ORDER_CANCELED') ?: 'Отменен';
    } elseif (!empty($orderStatus['SEMANTICS']) && $orderStatus['SEMANTICS'] === 'F') {
        $statusClass = 'status_paid';
        $statusIcon = 'check1';
    }

    // Статус оплаты для sidebar
    $paymentStatusClass = $isPaid ? 'status_paid' : 'status_delivery';
    $paymentStatusIcon = $isPaid ? 'check1' : 'check1';
    $paymentStatusText = $isPaid ? 'Оплачен' : 'Не оплачен';

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
    $orgProps = [];
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
            if (in_array($code, ['FIO', 'PHONE', 'EMAIL'])) {
                $buyerProps[] = $prop;
            } elseif (in_array($code, ['COMPANY', 'CONTACT_PERSON', 'PHONE_PERSON', 'EMAIL_PERSON'])) {
                $orgProps[] = $prop;
            } elseif (in_array($code, ['ADDRESS', 'ENTRANCE', 'FLOOR', 'APARTMENT'])) {
                $deliveryProps[] = $prop;
            } elseif (in_array($code, ['FIO_MANAGER', 'EMAIL_MANAGER', 'DESCRIPTION'])) {
                $additionalProps[] = $prop;
            }
        }
    }
    ?>


    <div class="container container_bordered1">
        <div class="block-order__content">
            <div class="block-order__body block1">

                <!-- Заголовок и статус -->
                <div class="block-order__column">
                    <h2 class="title2">
                        заказ №<?= htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"]) ?>
                        от <?= $arResult["DATE_INSERT_FORMATED"] ?>
                    </h2>
                    <div class="block-order__status <?= $statusClass ?>">
                        <svg aria-hidden="true" width="30" height="30">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#<?= $statusIcon ?>"></use>
                        </svg>
                        <?= $statusText ?>
                    </div>
                </div>

                <!-- Данные покупателя -->
                <?php if (!empty($buyerProps)): ?>
                    <div class="block-order__column">
                        <div class="block-order__title">
                            данные покупателя
                        </div>
                        <ul class="block-order__details">
                            <?php foreach ($buyerProps as $prop): ?>
                                <?php if (!empty($prop['VALUE'])): ?>
                                    <li>
                                        <span><?= $prop['NAME'] ?>:</span> <?= $prop['VALUE'] ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Данные организации -->
                <?php if (!empty($orgProps)): ?>
                    <div class="block-order__column">
                        <div class="block-order__title">
                            данные организации
                        </div>
                        <ul class="block-order__details">
                            <?php foreach ($orgProps as $prop): ?>
                                <?php if (!empty($prop['VALUE'])): ?>
                                    <li>
                                        <span><?= $prop['NAME'] ?>:</span> <?= $prop['VALUE'] ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Способы доставки и оплаты -->
                <div class="block-order__grid">
                    <div class="block-order__row">
                        <div class="block-order__title">
                            способы доставки
                        </div>
                        <?php
                        $deliveryName = '';
                        
                        // Сначала пробуем получить из DELIVERY напрямую
                        if (!empty($arResult['DELIVERY']['NAME'])) {
                            $deliveryName = htmlspecialcharsbx($arResult['DELIVERY']['NAME']);
                        }
                        // Если не нашли, пробуем из SHIPMENT
                        elseif (!empty($arResult['SHIPMENT']) && is_array($arResult['SHIPMENT'])) {
                            foreach ($arResult['SHIPMENT'] as $shipment) {
                                if (!empty($shipment['DELIVERY_NAME'])) {
                                    $deliveryName = htmlspecialcharsbx($shipment['DELIVERY_NAME']);
                                    break;
                                }
                            }
                        }
                        // Если всё ещё пусто, пробуем старый вариант
                        elseif (!empty($arResult['DELIVERY']) && is_array($arResult['DELIVERY'])) {
                            foreach ($arResult['DELIVERY'] as $delivery) {
                                if (is_array($delivery) && !empty($delivery['NAME'])) {
                                    $deliveryName = htmlspecialcharsbx($delivery['NAME']);
                                    break;
                                }
                            }
                        }
                        ?>
                        <?php if (!empty($deliveryName)): ?>
                            <div class="checkbox-text">
                                <div class="checkbox-text__label">
                                    <div class="checkbox-text__titles">
                                        <div class="checkbox-text__title"><?= $deliveryName ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($deliveryProps)): ?>
                            <div class="addres-block-order _active">
                                <div class="addres-block-order__title">Адрес доставки</div>
                                <ul class="block-order__details">
                                    <?php foreach ($deliveryProps as $prop): ?>
                                        <?php if (!empty($prop['VALUE'])): ?>
                                            <li>
                                                <span><?= $prop['NAME'] ?>:</span> <?= $prop['VALUE'] ?>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
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
                            <?php if ($bShowPayButton): ?>
                                <a href="<?= $payLink ?>" target="_blank" class="btn btn_primary">Выставить счёт</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Дополнительная информация -->
                <?php if (!empty($additionalProps)): ?>
                    <div class="block-order__column">
                        <div class="block-order__title">
                            дополнительная информация
                        </div>
                        <ul class="block-order__details">
                            <?php foreach ($additionalProps as $prop): ?>
                                <?php if (!empty($prop['VALUE'])): ?>
                                    <li>
                                        <span><?= $prop['NAME'] ?>:</span> <?= $prop['VALUE'] ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Товары в заказе -->
                <div class="block-order__column">
                    <div class="block-order__title">
                        товары в заказе
                    </div>
                    <div class="block-order__cards">
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
                            <div class="card-product3 card-product4">
                                <div class="card-product3__col-photo">
                                    <a href="<?= htmlspecialcharsbx($basketItem['DETAIL_PAGE_URL']) ?>">
                                        <img src="<?= htmlspecialcharsbx($imagePath) ?>"
                                             alt="<?= htmlspecialcharsbx($basketItem['NAME']) ?>">
                                    </a>
                                </div>
                                <div class="card-product3__col-data">
                                    <div class="card-product3__name">
                                        <a href="<?= htmlspecialcharsbx($basketItem['DETAIL_PAGE_URL']) ?>"><?= htmlspecialcharsbx($basketItem['NAME']) ?></a>
                                    </div>
                                </div>
                                <div class="card-product3__col1"<?= $isPriceZero ? ' style="display: none;"' : '' ?>>
                                    <div>
                                        <div class="card-product3__label1">С НДС
                                            (1 <?= htmlspecialcharsbx($measureName) ?>)
                                        </div>
                                        <span class="card-product3__price1"><?= number_format($price, 0, '', ' ') ?> ₽</span>
                                        <?php if ($basePrice > $price): ?>
                                            <span class="card-product3__price2"><?= number_format($basePrice, 0, '', ' ') ?> ₽</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if ($isPriceZero): ?>
                                <div class="card-product3__col1">
                                    <div>
                                        <div class="card-product3__label1">С НДС
                                            (1 <?= htmlspecialcharsbx($measureName) ?>)
                                        </div>
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

            <!-- Sidebar с итогами -->
            <div class="details-block-cart">
                <div class="details-block-cart__sticky">
                    <div class="details-block-cart__list">
                        <div class="details-block-cart__title">
                            Ваш заказ
                            <div class="details-block-cart__status <?= $paymentStatusClass ?>">
                                <svg aria-hidden="true" width="16" height="16">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#<?= $paymentStatusIcon ?>"></use>
                                </svg>
                                <?= $paymentStatusText ?>
                            </div>
                        </div>
                        <div class="details-block-cart__item">
                            <div class="details-block-cart__product">
                                Товары, <?= count($arResult['BASKET']) ?> <?= count($arResult['BASKET']) == 1 ? 'товар' : (count($arResult['BASKET']) < 5 ? 'товара' : 'товаров') ?></div>
                            <div class="details-block-cart__cost"><?= number_format($basketBasePrice, 0, '', ' ') ?>₽
                            </div>
                        </div>
                        <?php if ($basketTotalDiscount > 0): ?>
                            <div class="details-block-cart__item sale">
                                <div class="details-block-cart__product">Скидка</div>
                                <div class="details-block-cart__cost">
                                    -<?= number_format($basketTotalDiscount, 0, '', ' ') ?> ₽
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($deliveryPrice > 0): ?>
                            <div class="details-block-cart__item delivery">
                                <div class="details-block-cart__product">Доставка</div>
                                <div class="details-block-cart__cost"><?= number_format($deliveryPrice, 0, '', ' ') ?>
                                    ₽
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="details-block-cart__totals">
                        <div class="details-block-cart__title">Сумма заказа:</div>
                        <div class="details-block-cart__summ"><?= number_format($totalPrice, 0, '', ' ') ?> ₽</div>
                    </div>
                    <a href="<?= htmlspecialcharsbx($arResult["URL_TO_COPY"]) ?>" class="btn btn_primary">
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
