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
<?php
    $nothing = !isset($_REQUEST["filter_history"]) && !isset($_REQUEST["show_all"]);
    $clearFromLink = ["filter_history", "filter_status", "show_all", "show_canceled"];

    if ($nothing || $filterHistory === 'N') {
        $currentTab = 'current';
    } elseif ($filterShowCanceled === 'Y') {
        $currentTab = 'cancelled';
    } else {
        $currentTab = 'completed';
    }
?>

<div class="order-tabs">
    <?php if ($currentTab === 'current'): ?>
        <button class="order-tabs__item is-active" type="button">Текущие</button>
    <?php else: ?>
        <a
            href="<?= $APPLICATION->GetCurPageParam("", $clearFromLink, false) ?>"
            class="order-tabs__item"
        >
            Текущие
        </a>
    <?php endif ?>

    <?php if ($currentTab === 'completed'): ?>
        <button class="order-tabs__item is-active" type="button">Завершённые</button>
    <?php else: ?>
        <a
            href="<?= $APPLICATION->GetCurPageParam("filter_history=Y", $clearFromLink, false) ?>"
            class="order-tabs__item"
        >
            Завершённые
        </a>
    <?php endif ?>

    <?php if ($currentTab === 'cancelled'): ?>
        <button class="order-tabs__item is-active" type="button">Отменённые</button>
    <?php else: ?>
        <a
            href="<?= $APPLICATION->GetCurPageParam("filter_history=Y&show_canceled=Y", $clearFromLink, false) ?>"
            class="order-tabs__item"
        >
            Отменённые
        </a>
    <?php endif ?>
</div>

<?php if (!empty($arResult['ORDERS'])): ?>
<div class="order-tabs__content is-active" data-tab="<?= htmlspecialcharsbx($currentTab) ?>">
    <div class="orders-table-wrapper">
        <table class="orders-table" data-entity="items-row">
            <thead class="orders-table__header">
                <tr>
                    <th>№</th>
                    <th>Создан</th>
                    <th>Сумма</th>
                    <th>Статус оплаты</th>
                    <th>Статус заказа</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
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
            $statusId = $order['ORDER']['STATUS_ID'] ?? '';
            $isCompletedStatus = ($statusId === 'F') || (!empty($orderStatus['SEMANTICS']) && $orderStatus['SEMANTICS'] === 'F');

            if ($order['ORDER']['CANCELED'] === 'Y') {
                $orderStatusClass = 'status_cancel';
                $orderStatusIcon = 'cross';
            } elseif ($isCompletedStatus) {
                $orderStatusClass = 'status_paid';
                $orderStatusIcon = 'check1';
            }
            ?>

            <tr data-entity="item">
                <td data-label="№">
                    <div>№ <?= htmlspecialcharsbx($order['ORDER']['ACCOUNT_NUMBER']) ?></div>
                </td>
                <td data-label="Создан">
                    <div><?= htmlspecialcharsbx($order['ORDER']['DATE_INSERT_FORMATED']) ?></div>
                </td>
                <td data-label="Сумма">
                    <div><?= html_entity_decode($order['ORDER']['FORMATED_PRICE'], ENT_QUOTES, 'UTF-8') ?></div>
                </td>
                <td data-label="Статус оплаты">
                    <div>
                        <?php
                        $payClass = 'order-status';
                        $payIcon = 'status-pending';
                        if ($paymentStatusClass === 'status_paid') {
                            $payClass .= ' order-status--success';
                            $payIcon = 'status-success';
                        } else {
                            $payClass .= ' order-status--pending';
                            $payIcon = 'status-pending';
                        }
                        ?>
                        <span class="<?= $payClass ?>">
                            <svg aria-hidden="true" width="18" height="18">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#<?= $payIcon ?>"></use>
                            </svg>
                            <?= htmlspecialcharsbx($paymentStatusText) ?>
                        </span>
                    </div>
                </td>
                <td data-label="Статус заказа">
                    <div>
                        <?php
                        $orderClass = 'order-status';
                        $orderIconSprite = 'status-pending';
                        if ($orderStatusClass === 'status_cancel') {
                            $orderClass .= ' order-status--cancel';
                            $orderIconSprite = 'status-cancel';
                        } elseif ($orderStatusClass === 'status_paid') {
                            $orderClass .= ' order-status--success';
                            $orderIconSprite = 'status-success';
                        } else {
                            $orderClass .= ' order-status--pending';
                            $orderIconSprite = 'status-pending';
                        }
                        ?>
                        <span class="<?= $orderClass ?>">
                            <svg aria-hidden="true" width="18" height="18">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#<?= $orderIconSprite ?>"></use>
                            </svg>
                            <?= htmlspecialcharsbx($orderStatus['NAME']) ?>
                        </span>
                    </div>
                </td>
                <td class="cell--details">
                    <div>
                        <a
                            class="btn-text btn-text_primary"
                            href="<?= htmlspecialcharsbx($order["ORDER"]["URL_TO_DETAIL"]) ?>"
                        >
                            <span>подробнее</span>
                            <svg class="btn-text__icon" width="14" height="14" aria-hidden="true">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                            </svg>
                        </a>
                    </div>
                </td>
                <td class="cell--details">
                    <div>
                        <a
                            class="btn-text btn-text_primary"
                            href="<?= htmlspecialcharsbx($order["ORDER"]["URL_TO_COPY"]) ?>"
                        >
                            <span>повторить заказ</span>
                            <svg class="btn-text__icon" width="14" height="14" aria-hidden="true">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                            </svg>
                        </a>
                    </div>
                </td>
            </tr>

        <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php
    // Выводим пагинацию (data-атрибуты остаются из шаблона system.pagenavigation)
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
