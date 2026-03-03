<?php

/**
 * Шаблон компонента переключения кнопок корзины
 *
 * @var array $arResult
 * @var array $arParams
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 *
 * @global CMain $APPLICATION
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Web\Json;

$basketIds = $arResult['BASKET_PRODUCT_IDS'] ?? [];
$basketIdsJson = Json::encode($basketIds);
?>
<div 
    id="cart-button-switcher" 
    data-role="cart-button-switcher" 
    data-basket-ids='<?= htmlspecialcharsbx($basketIdsJson) ?>'
    style="display: none;"
></div>

