<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('iblock')) {
    return;
}

// Получаем список инфоблоков
$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = [];
$iblockFilter = ['ACTIVE' => 'Y'];
$rsIBlock = CIBlock::GetList(['SORT' => 'ASC'], $iblockFilter);
while ($arr = $rsIBlock->Fetch()) {
    $arIBlock[$arr['ID']] = '[' . $arr['ID'] . '] ' . $arr['NAME'];
}

// Получаем типы цен
$arPrice = [];
if (Loader::includeModule('catalog')) {
    $rsPrice = CCatalogGroup::GetList(['SORT' => 'ASC'], ['ACTIVE' => 'Y']);
    while ($arr = $rsPrice->Fetch()) {
        $arPrice[$arr['NAME']] = '[' . $arr['NAME'] . '] ' . $arr['NAME_LANG'];
    }
}

$arComponentParameters = [
    'GROUPS' => [
        'SEARCH_SETTINGS' => [
            'NAME' => Loc::getMessage('ACROWEB_CATALOG_SMARTSEARCH_GROUP_SEARCH'),
            'SORT' => 100
        ],
        'DISPLAY_SETTINGS' => [
            'NAME' => Loc::getMessage('ACROWEB_CATALOG_SMARTSEARCH_GROUP_DISPLAY'),
            'SORT' => 200
        ],
        'ADDITIONAL' => [
            'NAME' => Loc::getMessage('ACROWEB_CATALOG_SMARTSEARCH_GROUP_ADDITIONAL'),
            'SORT' => 300
        ]
    ],
    'PARAMETERS' => [
        'IBLOCK_ID' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('ACROWEB_CATALOG_SMARTSEARCH_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlock,
            'REFRESH' => 'Y',
            'ADDITIONAL_VALUES' => 'N'
        ],
        'ITEMS_LIMIT' => [
            'PARENT' => 'SEARCH_SETTINGS',
            'NAME' => Loc::getMessage('ACROWEB_CATALOG_SMARTSEARCH_ITEMS_LIMIT'),
            'TYPE' => 'STRING',
            'DEFAULT' => '5'
        ],
        'SECTIONS_LIMIT' => [
            'PARENT' => 'SEARCH_SETTINGS',
            'NAME' => Loc::getMessage('ACROWEB_CATALOG_SMARTSEARCH_SECTIONS_LIMIT'),
            'TYPE' => 'STRING',
            'DEFAULT' => '4'
        ],
        'SHOW_SECTIONS' => [
            'PARENT' => 'DISPLAY_SETTINGS',
            'NAME' => Loc::getMessage('ACROWEB_CATALOG_SMARTSEARCH_SHOW_SECTIONS'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y'
        ],
        'SHOW_ITEMS' => [
            'PARENT' => 'DISPLAY_SETTINGS',
            'NAME' => Loc::getMessage('ACROWEB_CATALOG_SMARTSEARCH_SHOW_ITEMS'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y'
        ],
        'PRICE_CODE' => [
            'PARENT' => 'DISPLAY_SETTINGS',
            'NAME' => Loc::getMessage('ACROWEB_CATALOG_SMARTSEARCH_PRICE_CODE'),
            'TYPE' => 'LIST',
            'VALUES' => $arPrice,
            'DEFAULT' => 'BASE'
        ],
        'SEARCH_BY_ARTICLE' => [
            'PARENT' => 'SEARCH_SETTINGS',
            'NAME' => Loc::getMessage('ACROWEB_CATALOG_SMARTSEARCH_SEARCH_BY_ARTICLE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ],
        'CACHE_TIME' => [
            'DEFAULT' => 60
        ]
    ]
];

// Если выбран инфоблок, получаем его свойства
if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperty = [];
    $arPropertyList = []; // Свойства типа "Список"
    
    $rsProperty = CIBlockProperty::GetList(
        ['SORT' => 'ASC'],
        ['IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], 'ACTIVE' => 'Y']
    );
    while ($arr = $rsProperty->Fetch()) {
        $arProperty[$arr['CODE']] = '[' . $arr['CODE'] . '] ' . $arr['NAME'];
        
        // Собираем только свойства типа "Список" для лейблов
        if ($arr['PROPERTY_TYPE'] === 'L') {
            $arPropertyList[$arr['CODE']] = '[' . $arr['CODE'] . '] ' . $arr['NAME'];
        }
    }

    if ($arCurrentValues['SEARCH_BY_ARTICLE'] === 'Y') {
        $arComponentParameters['PARAMETERS']['ARTICLE_PROPERTY'] = [
            'PARENT' => 'SEARCH_SETTINGS',
            'NAME' => Loc::getMessage('ACROWEB_CATALOG_SMARTSEARCH_ARTICLE_PROPERTY'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperty,
            'ADDITIONAL_VALUES' => 'N'
        ];
    }
    
    // Добавляем параметр для выбора свойства с лейблами
    $arComponentParameters['PARAMETERS']['LABEL_PROP'] = [
        'PARENT' => 'DISPLAY_SETTINGS',
        'NAME' => Loc::getMessage('ACROWEB_CATALOG_SMARTSEARCH_LABEL_PROP'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'VALUES' => $arPropertyList,
        'ADDITIONAL_VALUES' => 'N',
        'SIZE' => 5
    ];
}

