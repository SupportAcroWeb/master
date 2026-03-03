<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Модификатор результата для виджета блока новостей
 * 
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

// Формируем фильтр для news.list
$arFilter = [];

// Фильтр по отображению на главной
if ($arParams['SHOW_ON_MAIN'] === 'Y') {
    $arFilter['!=PROPERTY_SHOW_MAIN'] = false;
}

// Исключаем элементы по ID
if (!empty($arParams['EXCLUDE_IDS'])) {
    $excludeIds = array_map('trim', explode(',', $arParams['EXCLUDE_IDS']));
    $excludeIds = array_filter($excludeIds, function ($id) {
        return is_numeric($id) && (int)$id > 0;
    });
    
    if (!empty($excludeIds)) {
        $arFilter['!ID'] = $excludeIds;
    }
}

// Передаем фильтр через глобальную переменную
if (!empty($arFilter)) {
    global $arrNewsFilter;
    $arrNewsFilter = $arFilter;
    $arResult['FILTER_NAME'] = 'arrNewsFilter';
} else {
    $arResult['FILTER_NAME'] = '';
}

