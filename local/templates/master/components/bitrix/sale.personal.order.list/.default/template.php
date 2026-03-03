<?php
/**
 * Список заказов пользователя
 * 
 * @var CBitrixPersonalOrderListComponent $component
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

\Bitrix\Main\UI\Extension::load([
    'ui.design-tokens',
    'ui.fonts.opensans',
    'clipboard',
    'fx',
]);

Asset::getInstance()->addJs("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/script.js");
Asset::getInstance()->addCss("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/style.css");

Loc::loadMessages(__FILE__);

if (!empty($arResult['ERRORS']['FATAL'])) {
    foreach ($arResult['ERRORS']['FATAL'] as $error) {
        ShowError($error);
    }
    $component = $this->__component;
    if ($arParams['AUTH_FORM_IN_TEMPLATE'] && isset($arResult['ERRORS']['FATAL'][$component::E_NOT_AUTHORIZED])) {
        $APPLICATION->AuthForm('', false, false, 'N', false);
    }
} else {
    $filterHistory = ($_REQUEST['filter_history'] ?? '');
    $filterShowCanceled = ($_REQUEST["show_canceled"] ?? '');

    if (!empty($arResult['ERRORS']['NONFATAL'])) {
        foreach ($arResult['ERRORS']['NONFATAL'] as $error) {
            ShowError($error);
        }
    }
    ?>

<div class="block-user-cabinet__top">
    <div class="block-user-cabinet__title">мои заказы</div>
</div>

<div class="filter-user-cabinet">
    <?php
    $nothing = !isset($_REQUEST["filter_history"]) && !isset($_REQUEST["show_all"]);
    $clearFromLink = array("filter_history", "filter_status", "show_all", "show_canceled");

    // Текущие заказы
    if ($nothing || $filterHistory === 'N') {
        ?>
        <span class="filter-user-cabinet__title active">Текущие</span>
        <?php
    } else {
        ?>
        <a href="<?= $APPLICATION->GetCurPageParam("", $clearFromLink, false) ?>" class="filter-user-cabinet__title">Текущие</a>
        <?php
    }

    // История заказов
    if ($filterHistory === 'Y' && $filterShowCanceled !== 'Y') {
        ?>
        <span class="filter-user-cabinet__title active">История заказов</span>
        <?php
    } else {
        ?>
        <a href="<?= $APPLICATION->GetCurPageParam("filter_history=Y", $clearFromLink, false) ?>" class="filter-user-cabinet__title">История заказов</a>
        <?php
    }

    // Отмененные
    if ($filterShowCanceled === 'Y') {
        ?>
        <span class="filter-user-cabinet__title active">Отмененные</span>
        <?php
    } else {
        ?>
        <a href="<?= $APPLICATION->GetCurPageParam("filter_history=Y&show_canceled=Y", $clearFromLink, false) ?>" class="filter-user-cabinet__title">Отмененные</a>
        <?php
    }
    ?>
</div>
<?php if (!empty($arResult['ORDERS'])): ?>
<div class="tables-user-cabinet">
    
    <div class="tables-user-cabinet__content" data-entity="items-row">
        <!-- Заголовок таблицы -->
        <div class="tables-user-cabinet__main-table">
            <div class="tables-user-cabinet__value">№</div>
            <div class="tables-user-cabinet__value">Создан</div>
            <div class="tables-user-cabinet__value">Сумма</div>
            <div class="tables-user-cabinet__value">Статус оплаты</div>
            <div class="tables-user-cabinet__value">Статус заказа</div>
            <div class="tables-user-cabinet__value"></div>
            <div class="tables-user-cabinet__value"></div>
        </div>

        <?php
        $paymentChangeData = [];
        $orderHeaderStatus = null;

        foreach ($arResult['ORDERS'] as $key => $order):
            if ($orderHeaderStatus !== $order['ORDER']['STATUS_ID'] && $arResult['SORT_TYPE'] == 'STATUS') {
                $orderHeaderStatus = $order['ORDER']['STATUS_ID'];
                ?>
                <h2 class="sale-order-title">
                    <?= Loc::getMessage('SPOL_TPL_ORDER_IN_STATUSES') ?>
                    &laquo;<?= htmlspecialcharsbx($arResult['INFO']['STATUS'][$orderHeaderStatus]['NAME']) ?>&raquo;
                </h2>
                <?php
            }

            // Определяем статус оплаты
            $paymentStatusClass = '';
            $paymentStatusText = '';
            $paymentStatusIcon = 'time';
            
            foreach ($order['PAYMENT'] as $payment) {
                if ($order['ORDER']['LOCK_CHANGE_PAYSYSTEM'] !== 'Y') {
                    $paymentChangeData[$payment['ACCOUNT_NUMBER']] = [
                        "order" => htmlspecialcharsbx($order['ORDER']['ACCOUNT_NUMBER']),
                        "payment" => htmlspecialcharsbx($payment['ACCOUNT_NUMBER']),
                        "allow_inner" => $arParams['ALLOW_INNER'],
                        "refresh_prices" => $arParams['REFRESH_PRICES'],
                        "path_to_payment" => $arParams['PATH_TO_PAYMENT'],
                        "only_inner_full" => $arParams['ONLY_INNER_FULL'],
                        "return_url" => $arResult['RETURN_URL'],
                    ];
                }
                
                if ($payment['PAID'] === 'Y') {
                    $paymentStatusClass = 'status_paid';
                    $paymentStatusText = Loc::getMessage('SPOL_TPL_PAID') ?: 'Оплачен';
                    $paymentStatusIcon = 'check1';
                } elseif ($order['ORDER']['IS_ALLOW_PAY'] == 'N') {
                    $paymentStatusClass = 'status_delivery';
                    $paymentStatusText = Loc::getMessage('SPOL_TPL_RESTRICTED_PAID') ?: 'Ограничена';
                    $paymentStatusIcon = 'time';
                } else {
                    $paymentStatusClass = 'status_delivery';
                    $paymentStatusText = Loc::getMessage('SPOL_TPL_NOTPAID') ?: 'Не оплачен';
                    $paymentStatusIcon = 'time';
                }
            }

            // Определяем статус заказа
            $orderStatusClass = 'status_delivery';
            $orderStatusIcon = 'time';
            $orderStatus = $arResult['INFO']['STATUS'][$order['ORDER']['STATUS_ID']];
            
            if ($order['ORDER']['CANCELED'] === 'Y') {
                $orderStatusClass = 'status_cancel';
                $orderStatusIcon = 'cross';
            } elseif (!empty($orderStatus['SEMANTICS']) && $orderStatus['SEMANTICS'] === 'F') {
                $orderStatusClass = 'status_paid';
                $orderStatusIcon = 'check1';
            }
            ?>

        <div class="tables-user-cabinet__table" data-entity="item">
            <div class="tables-user-cabinet__value">
                <span>Заказ</span>
                № <?= htmlspecialcharsbx($order['ORDER']['ACCOUNT_NUMBER']) ?>
            </div>
            <div class="tables-user-cabinet__value">
                <span>Создан</span>
                <?= htmlspecialcharsbx($order['ORDER']['DATE_INSERT_FORMATED']) ?>
            </div>
            <div class="tables-user-cabinet__value">
                <span>Сумма</span>
                <?= html_entity_decode($order['ORDER']['FORMATED_PRICE'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div class="tables-user-cabinet__value <?= $paymentStatusClass ?>">
                <span>Статус оплаты</span>
                <svg aria-hidden="true" width="16" height="16">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#<?= $paymentStatusIcon ?>"></use>
                </svg>
                <?= htmlspecialcharsbx($paymentStatusText) ?>
            </div>
            <div class="tables-user-cabinet__value <?= $orderStatusClass ?>">
                <span>Статус заказа</span>
                <svg aria-hidden="true" width="16" height="16">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#<?= $orderStatusIcon ?>"></use>
                </svg>
                <?= htmlspecialcharsbx($orderStatus['NAME']) ?>
            </div>
            <a href="<?= htmlspecialcharsbx($order["ORDER"]["URL_TO_COPY"]) ?>" class="tables-user-cabinet__value link">
                Заказать повторно
                <svg aria-hidden="true" width="13" height="13">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                </svg>
            </a>
            <a href="<?= htmlspecialcharsbx($order["ORDER"]["URL_TO_DETAIL"]) ?>" class="tables-user-cabinet__value link">
                Подробнее
                <svg aria-hidden="true" width="13" height="13">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                </svg>
            </a>
        </div>

        <?php endforeach; ?>
    </div>
    

    <?php
    // Выводим пагинацию
    if (!empty($arResult["NAV_STRING"])) {
        echo $arResult["NAV_STRING"];
    }
    ?>
</div>
<?php endif; ?>
<?php if (empty($arResult['ORDERS'])): ?>
    <div class="panel-notification">
        <?php
        if ($filterHistory === 'Y') {
            if ($filterShowCanceled === 'Y') {
                echo Loc::getMessage('SPOL_TPL_EMPTY_CANCELED_ORDER') ?: 'Нет отмененных заказов';
            } else {
                echo Loc::getMessage('SPOL_TPL_EMPTY_HISTORY_ORDER_LIST') ?: 'История заказов пуста';
            }
        } else {
            echo Loc::getMessage('SPOL_TPL_EMPTY_ORDER_LIST') ?: 'У вас пока нет заказов';
        }
        ?>
    </div>
<?php endif; ?>

<?php
    // JavaScript для работы со сменой платежной системы
    if ($filterHistory !== 'Y') {
        $javascriptParams = [
            "url" => CUtil::JSEscape($this->__component->GetPath() . '/ajax.php'),
            "templateFolder" => CUtil::JSEscape($templateFolder),
            "templateName" => $this->__component->GetTemplateName(),
            "paymentList" => $paymentChangeData,
            "returnUrl" => CUtil::JSEscape($arResult["RETURN_URL"]),
        ];
        $javascriptParams = CUtil::PhpToJSObject($javascriptParams);
        ?>
        <script>
            BX.Sale.PersonalOrderComponent.PersonalOrderList.init(<?= $javascriptParams ?>);
        </script>
        <?php
    }
}
