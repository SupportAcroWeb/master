<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Data\Cache;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 */

$tabsConfig = [
    'new' => [
        'title' => 'Новинки',
        'property' => 'NEWPRODUCT',
        'filter_name' => 'arrFilterNew'
    ],
    'hit' => [
        'title' => 'Хиты', 
        'property' => 'SALELEADER',
        'filter_name' => 'arrFilterHit'
    ],
    'special' => [
        'title' => 'Акции',
        'property' => 'SPECIALOFFER', 
        'filter_name' => 'arrFilterSpecial'
    ]
];

$elementIds = [];
$propertyFilter = [];
$filterConditions = ['LOGIC' => 'OR'];

foreach ($tabsConfig as $tabConfig) {
    $property = $tabConfig['property'];
    $elementIds[$property] = [];
    $propertyFilter[$property] = 'Да';
    $filterConditions[] = ['PROPERTY_' . $property . '_VALUE' => 'Да'];
}

$cacheTime = 3600;
$cacheId = 'recommendations_' . $arParams['IBLOCK_ID'];
$cacheDir = '/recommendations/';

$cache = Cache::createInstance();

if ($cache->initCache($cacheTime, $cacheId, $cacheDir)) {
    $elementIds = $cache->getVars();
} elseif ($cache->startDataCache()) {
    global $CACHE_MANAGER;
    
    if (defined("BX_COMP_MANAGED_CACHE")) {
        $CACHE_MANAGER->StartTagCache($cacheDir);
        $CACHE_MANAGER->RegisterTag("iblock_id_" . $arParams['IBLOCK_ID']);
        $CACHE_MANAGER->EndTagCache();
    }
    
    $filter = [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'ACTIVE' => 'Y',
        $filterConditions
    ];
    
    $selectFields = ['ID'];
    foreach ($tabsConfig as $tabConfig) {
        $selectFields[] = 'PROPERTY_' . $tabConfig['property'];
    }
    
    $elements = CIBlockElement::GetList(
        ['SORT' => 'ASC'],
        $filter,
        false,
        false,
        $selectFields
    );
    
    while ($element = $elements->Fetch()) {
        foreach ($propertyFilter as $prop => $value) {
            if ($element['PROPERTY_' . $prop . '_VALUE'] === $value) {
                $elementIds[$prop][] = $element['ID'];
            }
        }
    }

    $cache->endDataCache($elementIds);
}

$activeTabs = [];
$firstActiveTab = '';
/** @var array<int, array<string>> Карта: ID элемента => массив ключей вкладок (new, hit, special) */
$itemTabs = [];
$combinedIds = [];

foreach ($tabsConfig as $tabKey => $tabConfig) {
    $property = $tabConfig['property'];

    if (!empty($elementIds[$property])) {
        $activeTabs[$tabKey] = $tabConfig;

        foreach ($elementIds[$property] as $id) {
            $itemTabs[$id] = ($itemTabs[$id] ?? []);
            $itemTabs[$id][] = $tabKey;
        }
        $combinedIds = array_merge($combinedIds, $elementIds[$property]);

        if (empty($firstActiveTab)) {
            $firstActiveTab = $tabKey;
        }
    }
}

$combinedIds = array_values(array_unique($combinedIds));
sort($combinedIds, SORT_NUMERIC); // стабильный порядок для ключа кеша catalog.section

// Один общий фильтр для одного вызова catalog.section вместо трёх
if (!empty($combinedIds)) {
    $GLOBALS['arrFilterRecommendationsMain'] = [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'ACTIVE' => 'Y',
        'ID' => $combinedIds
    ];
}

$hasElements = !empty($activeTabs);
$arResult['HAS_ELEMENTS'] = $hasElements;
$arResult['ELEMENTS_ID'] = $elementIds;
$arResult['ACTIVE_TABS'] = $activeTabs;
$arResult['FIRST_ACTIVE_TAB'] = $firstActiveTab;
$arResult['ITEM_TABS'] = $itemTabs;
$arResult['COMBINED_IDS'] = $combinedIds;

