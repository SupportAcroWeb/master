<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */
/** @var array $arParams */

// Структурируем меню по уровням для сложной верстки
$arMenuStructured = [];

if (!empty($arResult)) {
    foreach ($arResult as $arItem) {
        // Первый уровень - основные категории
        if ($arItem['DEPTH_LEVEL'] == 1) {
            $arMenuStructured[$arItem['LINK']] = [
                'ITEM' => $arItem,
                'CHILDREN' => [],
                'PICTURE' => ''
            ];
            
            // Получаем картинку из данных раздела
            $picture = '';
            if (!empty($arItem['PARAMS']['PICTURE'])) {
                $picture = CFile::GetPath($arItem['PARAMS']['PICTURE']);
            }
            
            // Если изображения нет - используем заглушку
            if (empty($picture)) {
                $picture = SITE_TEMPLATE_PATH . '/img/no-image.png';
            }
            
            $arMenuStructured[$arItem['LINK']]['PICTURE'] = $picture;
            
            $currentParent = $arItem['LINK'];
        }
        // Второй уровень - подкатегории
        elseif ($arItem['DEPTH_LEVEL'] == 2 && isset($currentParent)) {
            if (!isset($arMenuStructured[$currentParent]['CHILDREN'][$arItem['LINK']])) {
                $arMenuStructured[$currentParent]['CHILDREN'][$arItem['LINK']] = [
                    'ITEM' => $arItem,
                    'CHILDREN' => []
                ];
            }
            $currentSubParent = $arItem['LINK'];
        }
        // Третий уровень - элементы подкатегорий
        elseif ($arItem['DEPTH_LEVEL'] == 3 && isset($currentParent) && isset($currentSubParent)) {
            $arMenuStructured[$currentParent]['CHILDREN'][$currentSubParent]['CHILDREN'][] = $arItem;
        }
    }
}

$arResult['STRUCTURED_MENU'] = $arMenuStructured;
