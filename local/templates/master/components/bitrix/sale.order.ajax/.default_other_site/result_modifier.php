<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */

$arParams['SERVICES_IMAGES_SCALING'] = (string)($arParams['SERVICES_IMAGES_SCALING'] ?? 'adaptive');

if (!empty($arResult['JS_DATA']['STORE_LIST'])) {
    $stores = $arResult['JS_DATA']['STORE_LIST'];
    
    foreach ($stores as &$store) {
        $storeData = \Bitrix\Catalog\StoreTable::getList([
            'filter' => ['ID' => $store['ID']],
            'select' => ['SORT']
        ])->fetch();
        
        $store['SORT'] = $storeData['SORT'] ?? 500;
    }
    unset($store);

    uasort($stores, function($a, $b) {
        return $a['SORT'] - $b['SORT'];
    });

    $arResult['JS_DATA']['STORE_LIST'] = $stores;
}

foreach ($arResult['DELIVERY'] as $deliveryId => &$delivery) {
    if (!empty($delivery['STORE'])) {
        $delivery['STORE'] = array_keys($arResult['JS_DATA']['STORE_LIST']);
    }
}
unset($delivery);

$component = $this->__component;
$component::scaleImages($arResult['JS_DATA'], $arParams['SERVICES_IMAGES_SCALING']);