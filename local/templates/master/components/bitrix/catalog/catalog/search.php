<?php
/**
 * Страница поиска - использует section_vertical.php с логикой поиска
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\SectionTable;

// Получаем поисковый запрос
$request = Application::getInstance()->getContext()->getRequest();
$searchQuery = trim((string)$request->get('q'));

// Если есть поисковый запрос и его длина минимум 2 символа, выполняем поиск
if (!empty($searchQuery) && mb_strlen($searchQuery) >= 2) {
    if (Loader::includeModule('search')) {
        $obSearch = new CSearch();
        $obSearch->SetOptions([
            'ERROR_ON_EMPTY_STEM' => false,
            'NO_WORD_LOGIC' => true,
        ]);

        $arFilter = [
            'SITE_ID' => SITE_ID,
            'QUERY' => $searchQuery,
            'MODULE_ID' => 'iblock',
            'PARAM1' => $arParams['IBLOCK_TYPE'],
            'PARAM2' => $arParams['IBLOCK_ID'],
            'CHECK_DATES' => 'Y',
        ];

        $obSearch->Search($arFilter, [], []);

        if ($obSearch->errorno == 0) {
            $foundIds = [];
            while ($arResult = $obSearch->GetNext()) {
                if (!empty($arResult['ITEM_ID'])) {
                    $foundIds[] = (int)str_replace('E', '', $arResult['ITEM_ID']);
                }
            }

            // Устанавливаем фильтр с найденными ID
            if (!empty($foundIds)) {
                $foundIds = array_unique($foundIds);
                $GLOBALS[$arParams['FILTER_NAME']]['ID'] = $foundIds;
                
                // Сохраняем найденные ID для префильтра умного фильтра
                $arResult['SEARCH_FOUND_IDS'] = $foundIds;
                
                // Получаем категории найденных товаров
                if (Loader::includeModule('iblock')) {
                    $arResult['SEARCH_SECTIONS'] = [];
                    $sectionCounts = [];
                    
                    // Получаем все элементы с их секциями за один запрос
                    $rsElements = ElementTable::getList([
                        'filter' => [
                            'ID' => $foundIds,
                            'IBLOCK_ID' => $arParams['IBLOCK_ID']
                        ],
                        'select' => ['ID', 'IBLOCK_SECTION_ID']
                    ]);
                    
                    // Подсчитываем количество товаров в каждой секции
                    while ($element = $rsElements->fetch()) {
                        if (!empty($element['IBLOCK_SECTION_ID'])) {
                            $sectionId = $element['IBLOCK_SECTION_ID'];
                            $sectionCounts[$sectionId] = ($sectionCounts[$sectionId] ?? 0) + 1;
                        }
                    }
                    
                    if (!empty($sectionCounts)) {
                        // Получаем данные секций одним запросом
                        $rsSections = SectionTable::getList([
                            'filter' => ['ID' => array_keys($sectionCounts)],
                            'select' => ['ID', 'NAME', 'CODE'],
                            'order' => ['NAME' => 'ASC']
                        ]);
                        
                        while ($section = $rsSections->fetch()) {
                            $arResult['SEARCH_SECTIONS'][] = [
                                'ID' => $section['ID'],
                                'NAME' => $section['NAME'],
                                'CODE' => $section['CODE'],
                                'COUNT' => $sectionCounts[$section['ID']]
                            ];
                        }
                    }
                }
            } else {
                // Если ничего не найдено, устанавливаем невозможное условие
                // чтобы не показывать товары
                $GLOBALS[$arParams['FILTER_NAME']]['ID'] = [0];
                $arResult['SEARCH_FOUND_IDS'] = [];
                $arResult['SEARCH_SECTIONS'] = [];
            }
        }
    }
} else {
    // Если запрос пустой или меньше 2 символов - не показываем товары
    if (!empty($searchQuery)) {
        $GLOBALS[$arParams['FILTER_NAME']]['ID'] = [0];
        $arResult['SEARCH_FOUND_IDS'] = [];
        $arResult['SEARCH_SECTIONS'] = [];
    }
}

// Устанавливаем признак что это поиск
$arResult['IS_SEARCH'] = !empty($searchQuery);
$arResult['SEARCH_QUERY'] = $searchQuery;

// Получаем выбранную категорию из GET-параметра
$selectedSectionId = (int)$request->get('SECTION_ID');
if ($selectedSectionId > 0 && !empty($arResult['SEARCH_FOUND_IDS']) && Loader::includeModule('iblock')) {
    // Фильтруем найденные ID по выбранной категории
    $rsElements = ElementTable::getList([
        'filter' => [
            'ID' => $arResult['SEARCH_FOUND_IDS'],
            'IBLOCK_SECTION_ID' => $selectedSectionId
        ],
        'select' => ['ID']
    ]);
    
    $filteredIds = [];
    while ($element = $rsElements->fetch()) {
        $filteredIds[] = $element['ID'];
    }
    
    if (!empty($filteredIds)) {
        $GLOBALS[$arParams['FILTER_NAME']]['ID'] = $filteredIds;
        $arResult['SEARCH_FOUND_IDS'] = $filteredIds;
    } else {
        // В выбранной категории нет найденных товаров
        $GLOBALS[$arParams['FILTER_NAME']]['ID'] = [0];
        $arResult['SEARCH_FOUND_IDS'] = [];
    }
}

// Подключаем section_vertical.php который отобразит результаты
include(__DIR__ . '/section_vertical.php');
