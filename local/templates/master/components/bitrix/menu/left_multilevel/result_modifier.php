<?php
/**
 * Модификатор результата для меню каталога
 * Определяет текущий элемент, его родителя и детей для отображения
 * 
 * @var array $arResult Результат работы компонента меню
 * @var array $arParams Параметры компонента
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$currentItem = null;
$parentItem = null;
$children = [];

if (empty($arResult)) {
    return;
}

// Находим текущий выбранный элемент (берем последний SELECTED - самый глубокий)
$currentKey = null;
foreach ($arResult as $key => $arItem) {
    if ($arItem['SELECTED']) {
        $currentItem = $arItem;
        $currentKey = $key;
    }
}

if (!$currentItem) {
    return;
}

$currentDepth = $currentItem['DEPTH_LEVEL'];

// Ищем родителя текущего элемента
if ($currentDepth > 1) {
    for ($i = $currentKey - 1; $i >= 0; $i--) {
        if ($arResult[$i]['DEPTH_LEVEL'] == ($currentDepth - 1) && $arResult[$i]['IS_PARENT']) {
            $parentItem = $arResult[$i];
            break;
        }
    }
}

// Собираем прямых детей текущего элемента
$collectChildren = false;
foreach ($arResult as $arItem) {
    if ($arItem === $currentItem) {
        $collectChildren = true;
        continue;
    }
    
    if ($collectChildren) {
        if ($arItem['DEPTH_LEVEL'] == ($currentDepth + 1)) {
            $children[] = $arItem;
        } elseif ($arItem['DEPTH_LEVEL'] <= $currentDepth) {
            break;
        }
    }
}

$arResult['CURRENT_ITEM'] = $currentItem;
$arResult['PARENT_ITEM'] = $parentItem;
$arResult['CHILDREN'] = $children;
