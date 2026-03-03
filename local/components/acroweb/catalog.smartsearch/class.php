<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Web\Json;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\SystemException;
use Bitrix\Main\LoaderException;
use Bitrix\Catalog\ProductTable;
use Bitrix\Catalog\PriceTable;
use Bitrix\Catalog\GroupTable;
use Bitrix\Catalog\MeasureRatioTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\PropertyEnumerationTable;
use CCatalogDiscount;
use CCatalogProduct;
use CCatalogMeasure;
use CCurrencyLang;
use CIBlockElement;
use CIBlockSection;
use CFile;

/**
 * Компонент быстрого поиска по каталогу с живыми подсказками
 * 
 * @package Acroweb\Components
 */
class CatalogSmartSearchComponent extends CBitrixComponent implements Controllerable
{
    private const MIN_QUERY_LENGTH = 2;
    private const CACHE_DIR = '/acroweb/catalog.smartsearch/';
    private const ARRIVAL_DATE_PROPERTY_CODE = 'DATA_POSTUPLENIYA';

    /**
     * Конфигурация действий контроллера
     * 
     * @return array<string, array<string, mixed>>
     */
    public function configureActions(): array
    {
        return [
            'search' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_POST]),
                    new ActionFilter\Csrf(),
                ],
                'postfilters' => []
            ]
        ];
    }

    /**
     * Подготовка параметров компонента
     * 
     * @param array<string, mixed> $arParams
     * @return array<string, mixed>
     */
    public function onPrepareComponentParams($arParams): array
    {
        $arParams['IBLOCK_ID'] = (int)($arParams['IBLOCK_ID'] ?? 0);
        $arParams['ITEMS_LIMIT'] = (int)($arParams['ITEMS_LIMIT'] ?? 5);
        $arParams['SECTIONS_LIMIT'] = (int)($arParams['SECTIONS_LIMIT'] ?? 4);
        $arParams['PRICE_CODE'] = (string)($arParams['PRICE_CODE'] ?? 'BASE');
        $arParams['CACHE_TIME'] = (int)($arParams['CACHE_TIME'] ?? 60);
        $arParams['CACHE_TYPE'] = $arParams['CACHE_TYPE'] ?? 'A';
        $arParams['SHOW_SECTIONS'] = $arParams['SHOW_SECTIONS'] ?? 'Y';
        $arParams['SHOW_ITEMS'] = $arParams['SHOW_ITEMS'] ?? 'Y';
        $arParams['SEARCH_BY_ARTICLE'] = $arParams['SEARCH_BY_ARTICLE'] ?? 'N';
        $arParams['ARTICLE_PROPERTY'] = (string)($arParams['ARTICLE_PROPERTY'] ?? '');
        
        // Обработка параметра для лейблов (может быть массивом или строкой)
        if (!empty($arParams['LABEL_PROP'])) {
            if (!is_array($arParams['LABEL_PROP'])) {
                $arParams['LABEL_PROP'] = [$arParams['LABEL_PROP']];
            }
        } else {
            $arParams['LABEL_PROP'] = [];
        }

        return parent::onPrepareComponentParams($arParams);
    }

    /**
     * Основной метод выполнения компонента
     * 
     * @return void
     * @throws LoaderException
     */
    public function executeComponent(): void
    {
        try {
            if (!Loader::includeModule('iblock')) {
                throw new SystemException('Модуль iblock не установлен');
            }

            if (!Loader::includeModule('catalog')) {
                throw new SystemException('Модуль catalog не установлен');
            }

            if ($this->arParams['IBLOCK_ID'] <= 0) {
                throw new ArgumentException('Не указан ID инфоблока');
            }

            // Генерируем уникальный ID компонента для JS
            $this->arResult['COMPONENT_ID'] = 'smartsearch_' . $this->randString();

            $this->includeComponentTemplate();
        } catch (\Exception $e) {
            ShowError($e->getMessage());
        }
    }

    /**
     * Список ключей подписанных параметров
     * 
     * @return array<string>
     */
    public function listKeysSignedParameters(): array
    {
        return [
            'IBLOCK_ID',
            'ITEMS_LIMIT',
            'SECTIONS_LIMIT',
            'PRICE_CODE',
            'CACHE_TIME',
            'CACHE_TYPE',
            'SHOW_SECTIONS',
            'SHOW_ITEMS',
            'SEARCH_BY_ARTICLE',
            'ARTICLE_PROPERTY',
            'LABEL_PROP',
        ];
    }

    /**
     * Ajax-метод поиска товаров и разделов
     * 
     * @param string $query Поисковый запрос
     * @param int $offset Смещение для пагинации
     * @return array<string, mixed>
     * @throws LoaderException
     */
    public function searchAction(string $query, int $offset = 0): array
    {
        try {
            if (!Loader::includeModule('iblock')) {
                return ['error' => 'Модуль iblock не установлен'];
            }

            if (!Loader::includeModule('catalog')) {
                return ['error' => 'Модуль catalog не установлен'];
            }

            $query = trim($query);

            // Валидация минимальной длины запроса
            if (mb_strlen($query) < self::MIN_QUERY_LENGTH) {
                return [
                    'html' => '<div class="header-search-dd__empty">Введите минимум 2 символа</div>'
                ];
            }

            // Проверка кеша (только для первой страницы)
            $cacheId = $this->generateCacheId($query, $offset);
            $cache = Cache::createInstance();

            if ($offset === 0 && $this->arParams['CACHE_TYPE'] !== 'N' 
                && $cache->initCache($this->arParams['CACHE_TIME'], $cacheId, self::CACHE_DIR)) {
                $result = $cache->getVars();
            } else {
                $result = [
                    'SECTIONS' => [],
                    'ITEMS' => [],
                    'TOTAL' => 0,
                    'HAS_MORE' => false
                ];

                // Поиск разделов (только на первой странице)
                if ($offset === 0 && $this->arParams['SHOW_SECTIONS'] === 'Y') {
                    $result['SECTIONS'] = $this->searchSections($query);
                }

                // Поиск товаров с пагинацией
                if ($this->arParams['SHOW_ITEMS'] === 'Y') {
                    $itemsData = $this->searchItems($query, $offset);
                    $result['ITEMS'] = $itemsData['items'];
                    $result['HAS_MORE'] = $itemsData['hasMore'];
                }

                $result['TOTAL'] = count($result['SECTIONS']) + count($result['ITEMS']);

                // Сохранение в кеш (только первая страница)
                if ($offset === 0 && $this->arParams['CACHE_TYPE'] !== 'N') {
                    $cache->startDataCache();
                    $cache->endDataCache($result);
                }
            }

            // Генерируем HTML
            $html = $this->renderHtml($result);
            
            return [
                'html' => $html,
                'hasMore' => $result['HAS_MORE']
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Рендеринг HTML для результатов поиска
     * 
     * @param array $data Данные результатов (SECTIONS, ITEMS, HAS_MORE)
     * @return string
     */
    private function renderHtml(array $data): string
    {
        try {
            // Если нет результатов
            if (empty($data['SECTIONS']) && empty($data['ITEMS'])) {
                return '<div class="header-search-dd__empty">Ничего не найдено</div>';
            }
            
            // Получаем имя текущего шаблона
            $templateName = $this->getTemplateName();
            if (empty($templateName)) {
                $templateName = '.default';
            }
            
            // Формируем путь к ajax-шаблонам
            $componentPath = str_replace(
                Application::getDocumentRoot(),
                '',
                $this->getPath()
            );
            $templatePath = Application::getDocumentRoot() . $componentPath . '/templates/' . $templateName . '/ajax_templates/';
            
            // Проверяем существование директории
            if (!is_dir($templatePath)) {
                return '<div class="header-search-dd__error">Ошибка: шаблоны не найдены</div>';
            }
            
            // Формируем HTML через буферизацию вывода
            ob_start();
            
            // Подготавливаем $arResult для шаблонов
            $arResult = $data;
            
            // Рендерим разделы (только если есть)
            if (!empty($arResult['SECTIONS'])) {
                echo '<div class="header-search-dd__categories">';
                include($templatePath . 'sections.php');
                echo '</div>';
            }
            
            // Рендерим товары
            if (!empty($arResult['ITEMS'])) {
                echo '<div class="header-search-dd__products">';
                include($templatePath . 'items.php');
                echo '</div>';
            }
            
            // Рендерим кнопку "Показать ещё"
            if (!empty($arResult['HAS_MORE'])) {
                include($templatePath . 'show_more.php');
            }
            
            return ob_get_clean();
        } catch (\Exception $e) {
            return '<div class="header-search-dd__error">Ошибка: ' . htmlspecialcharsbx($e->getMessage()) . '</div>';
        }
    }

    /**
     * Поиск разделов каталога
     * 
     * @param string $query Поисковый запрос
     * @return array<int, array<string, mixed>>
     */
    private function searchSections(string $query): array
    {
        $sections = [];

        try {
            $filter = [
                'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                'ACTIVE' => 'Y',
                'GLOBAL_ACTIVE' => 'Y',
                '%NAME' => $query
            ];

            $result = CIBlockSection::GetList(
                ['NAME' => 'ASC'],
                $filter,
                false,
                ['ID', 'NAME', 'SECTION_PAGE_URL', 'PICTURE', 'DESCRIPTION'],
                ['nTopCount' => $this->arParams['SECTIONS_LIMIT']]
            );

            while ($section = $result->GetNext()) {
                $pictureUrl = '';
                if ($section['PICTURE']) {
                    $pictureUrl = CFile::GetPath($section['PICTURE']);
                }
                
                // Если нет картинки, используем заглушку
                if (empty($pictureUrl)) {
                    $pictureUrl = SITE_TEMPLATE_PATH . '/img/no-photo.svg';
                }

                $sections[] = [
                    'ID' => $section['ID'],
                    'NAME' => $section['NAME'],
                    'URL' => $section['SECTION_PAGE_URL'],
                    'PICTURE' => $pictureUrl,
                    'DESCRIPTION' => $section['DESCRIPTION'] ?? ''
                ];
            }
        } catch (\Exception $e) {
            // Логирование ошибки
            AddMessage2Log('Ошибка поиска разделов: ' . $e->getMessage());
        }

        return $sections;
    }

    /**
     * Поиск товаров в каталоге
     * 
     * @param string $query Поисковый запрос
     * @param int $offset Смещение для пагинации
     * @return array<string, mixed>
     */
    private function searchItems(string $query, int $offset = 0): array
    {
        $items = [];

        try {
            $filter = [
                'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                'ACTIVE' => 'Y',
                [
                    'LOGIC' => 'OR',
                    ['%NAME' => $query]
                ]
            ];

            // Добавляем поиск по артикулу если включено
            if ($this->arParams['SEARCH_BY_ARTICLE'] === 'Y' && !empty($this->arParams['ARTICLE_PROPERTY'])) {
                $filter[0]['%PROPERTY_' . $this->arParams['ARTICLE_PROPERTY']] = $query;
            }

            // Сортировка: сначала по количеству (DESC), потом по имени (ASC)
            // CIBlockElement::GetList автоматически делает JOIN с b_catalog_product
            $order = [
                'CATALOG_QUANTITY' => 'DESC', // Товары с большим количеством первыми
                'NAME' => 'ASC'
            ];
            
            // Запрашиваем на 1 больше для проверки hasMore
            $pageSize = $this->arParams['ITEMS_LIMIT'] + 1;
            $pageNumber = floor($offset / $this->arParams['ITEMS_LIMIT']) + 1;
            
            $result = CIBlockElement::GetList(
                $order,
                $filter,
                false,
                [
                    'nPageSize' => $pageSize,
                    'iNumPage' => $pageNumber
                ],
                ['ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'PREVIEW_TEXT', 'IBLOCK_SECTION_ID', 'CATALOG_QUANTITY']
            );

            $elementIds = [];
            $elementsData = [];
            $counter = 0;

            while ($element = $result->GetNext()) {
                $counter++;
                // Берем только нужное количество элементов
                if ($counter <= $this->arParams['ITEMS_LIMIT']) {
                    $elementIds[] = $element['ID'];
                    $elementsData[$element['ID']] = $element;
                }
            }
            
            // Если получили больше элементов чем лимит, значит есть еще страницы
            $hasMore = $counter > $this->arParams['ITEMS_LIMIT'];

            // Получаем цены одним запросом
            $prices = $this->getPrices($elementIds);

            // Получаем остатки товаров
            $availability = $this->getAvailability($elementIds);
            
            // Получаем лейблы товаров
            $labels = $this->getLabels($elementIds);

            // Получаем дату поступления товаров
            $arrivalDates = $this->getArrivalDates($elementIds);

            // Формируем результат
            foreach ($elementsData as $elementId => $element) {
                $pictureUrl = '';
                if ($element['PREVIEW_PICTURE']) {
                    $pictureUrl = CFile::GetPath($element['PREVIEW_PICTURE']);
                }
                
                // Если нет картинки, используем заглушку
                if (empty($pictureUrl)) {
                    $pictureUrl = SITE_TEMPLATE_PATH . '/img/no-photo.svg';
                }

                $price = $prices[$elementId] ?? null;
                $inStock = $availability[$elementId] ?? false;
                $itemLabels = $labels[$elementId] ?? [];
                $arrivalDate = $arrivalDates[$elementId] ?? '';

                $items[] = [
                    'ID' => $element['ID'],
                    'NAME' => $element['NAME'],
                    'URL' => $element['DETAIL_PAGE_URL'],
                    'PICTURE' => $pictureUrl,
                    'DESCRIPTION' => strip_tags($element['PREVIEW_TEXT'] ?? ''),
                    'PRICE' => $price ? $price['PRICE_FORMATTED'] : '',
                    'BASE_PRICE' => $price && $price['HAS_DISCOUNT'] ? $price['BASE_PRICE_FORMATTED'] : '',
                    'HAS_DISCOUNT' => $price ? $price['HAS_DISCOUNT'] : false,
                    'DISCOUNT_PERCENT' => $price ? $price['DISCOUNT_PERCENT'] : 0,
                    'MEASURE_RATIO' => $price ? $price['MEASURE_RATIO'] : 1,
                    'MEASURE_NAME' => $price ? $price['MEASURE_NAME'] : 'шт.',
                    'IN_STOCK' => $inStock,
                    'LABELS' => $itemLabels,
                    'ARRIVAL_DATE' => $arrivalDate,
                ];
            }
            
        } catch (\Exception $e) {
            // Логирование ошибки
            AddMessage2Log('Ошибка поиска товаров: ' . $e->getMessage());
        }

        return [
            'items' => $items,
            'hasMore' => $hasMore ?? false
        ];
    }

    /**
     * Получение информации о доступности товаров
     * 
     * @param array<int> $elementIds Массив ID товаров
     * @return array<int, bool>
     */
    private function getAvailability(array $elementIds): array
    {
        if (empty($elementIds)) {
            return [];
        }

        $availability = [];

        try {
            // Массовая выборка через D7 API - один запрос вместо N
            $result = ProductTable::getList([
                'filter' => ['ID' => $elementIds],
                'select' => ['ID', 'QUANTITY', 'CAN_BUY_ZERO']
            ]);
            
            while ($arProduct = $result->fetch()) {
                $elementId = (int)$arProduct['ID'];
                $quantity = (float)($arProduct['QUANTITY'] ?? 0);
                $canBuyZero = $arProduct['CAN_BUY_ZERO'] === 'Y';
                
                // Товар в наличии если:
                // - количество > 0 ИЛИ
                // - разрешена покупка при нулевом остатке
                $availability[$elementId] = ($quantity > 0) || $canBuyZero;
            }
            
            // Для товаров без данных в каталоге
            foreach ($elementIds as $elementId) {
                if (!isset($availability[$elementId])) {
                    $availability[$elementId] = false;
                }
            }
        } catch (\Exception $e) {
            AddMessage2Log('Ошибка получения остатков: ' . $e->getMessage());
        }

        return $availability;
    }

    /**
     * Получение цен для товаров с учетом скидок и коэффициента
     * 
     * @param array<int> $elementIds Массив ID товаров
     * @return array<int, array<string, mixed>>
     */
    private function getPrices(array $elementIds): array
    {
        if (empty($elementIds)) {
            return [];
        }

        $prices = [];

        try {
            // Получаем данные о товарах и единицах измерения массово
            $productsData = [];
            $measureIds = [];
            
            $result = ProductTable::getList([
                'filter' => ['ID' => $elementIds],
                'select' => ['ID', 'MEASURE']
            ]);
            
            while ($product = $result->fetch()) {
                $productsData[$product['ID']] = $product;
                if (!empty($product['MEASURE'])) {
                    $measureIds[$product['MEASURE']] = $product['MEASURE'];
                }
            }
            
            // Получаем коэффициенты единиц измерения массово
            $measureRatios = [];
            $result = MeasureRatioTable::getList([
                'filter' => ['PRODUCT_ID' => $elementIds],
                'select' => ['PRODUCT_ID', 'RATIO'],
                'order' => ['IS_DEFAULT' => 'DESC']
            ]);
            
            while ($ratio = $result->fetch()) {
                $productId = (int)$ratio['PRODUCT_ID'];
                if (!isset($measureRatios[$productId])) {
                    $measureRatios[$productId] = (float)$ratio['RATIO'];
                }
            }
            
            // Получаем единицы измерения массово
            $measures = [];
            if (!empty($measureIds)) {
                $result = CCatalogMeasure::getList(
                    [],
                    ['ID' => array_values($measureIds)],
                    false,
                    false,
                    ['ID', 'SYMBOL']
                );
                
                while ($measure = $result->Fetch()) {
                    $measures[$measure['ID']] = $measure['SYMBOL'];
                }
            }
            
            // Получаем ID группы цен
            $priceGroup = GroupTable::getList([
                'filter' => ['NAME' => $this->arParams['PRICE_CODE']],
                'select' => ['ID'],
                'limit' => 1
            ])->fetch();
            
            if (!$priceGroup) {
                return [];
            }
            
            $catalogGroupId = (int)$priceGroup['ID'];
            
            // Получаем базовые цены массово
            $basePrices = [];
            $result = PriceTable::getList([
                'filter' => [
                    'PRODUCT_ID' => $elementIds,
                    'CATALOG_GROUP_ID' => $catalogGroupId
                ],
                'select' => ['PRODUCT_ID', 'PRICE', 'CURRENCY'],
                'order' => ['PRODUCT_ID' => 'ASC', 'QUANTITY_FROM' => 'ASC']
            ]);
            
            while ($price = $result->fetch()) {
                $productId = (int)$price['PRODUCT_ID'];
                if (!isset($basePrices[$productId])) {
                    $basePrices[$productId] = $price;
                }
            }
            
            // Если не нашли цены - возвращаем пустой массив
            if (empty($basePrices)) {
                return [];
            }
            
            // Получаем скидки для всех товаров
            $userGroups = CurrentUser::get()->getUserGroups();
            
            // Формируем результат с применением скидок
            foreach ($elementIds as $productId) {
                if (!isset($basePrices[$productId])) {
                    continue;
                }
                
                $basePrice = (float)$basePrices[$productId]['PRICE'];
                $currency = $basePrices[$productId]['CURRENCY'];
                $discountPrice = $basePrice;
                $hasDiscount = false;
                $discountPercent = 0;
                
                // Получаем скидку для товара
                $discount = CCatalogDiscount::GetDiscount(
                    $productId,
                    $catalogGroupId,
                    $userGroups,
                    'N',
                    SITE_ID
                );

                if (!empty($discount)) {
                    $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                        $basePrice,
                        $currency,
                        $discount
                    );
                    
                    if ($discountPrice < $basePrice) {
                        $hasDiscount = true;
                        $discountPercent = round((($basePrice - $discountPrice) / $basePrice) * 100);
                    }
                }
                
                // Получаем коэффициент и единицу измерения
                $measureRatio = $measureRatios[$productId] ?? 1;
                $measureName = 'шт.';
                
                if (isset($productsData[$productId]) && !empty($productsData[$productId]['MEASURE'])) {
                    if (isset($measures[$productsData[$productId]['MEASURE']])) {
                        $measureName = $measures[$productsData[$productId]['MEASURE']];
                    }
                }
                
                // Форматируем цены БЕЗ учета кратности (цена за 1 единицу)
                $priceFormatted = CCurrencyLang::CurrencyFormat($discountPrice, $currency);
                $basePriceFormatted = $hasDiscount ? CCurrencyLang::CurrencyFormat($basePrice, $currency) : null;
                
                $prices[$productId] = [
                    'PRICE' => $discountPrice,
                    'PRICE_FORMATTED' => $priceFormatted,
                    'BASE_PRICE' => $basePrice,
                    'BASE_PRICE_FORMATTED' => $basePriceFormatted,
                    'CURRENCY' => $currency,
                    'DISCOUNT_PERCENT' => $discountPercent,
                    'HAS_DISCOUNT' => $hasDiscount,
                    'MEASURE_RATIO' => $measureRatio,
                    'MEASURE_NAME' => $measureName
                ];
            }
        } catch (\Exception $e) {
            AddMessage2Log('Ошибка получения цен: ' . $e->getMessage());
        }

        return $prices;
    }

    /**
     * Получение лейблов для товаров
     * 
     * @param array<int> $elementIds Массив ID товаров
     * @return array<int, array<string, string>>
     */
    private function getLabels(array $elementIds): array
    {
        if (empty($elementIds) || empty($this->arParams['LABEL_PROP'])) {
            return [];
        }

        $labels = [];

        try {
            // Получаем ID свойств используя D7 API
            $propertyIds = [];
            $propertyMap = []; // Маппинг ID свойства -> CODE свойства
            
            $propertyResult = PropertyTable::getList([
                'filter' => [
                    'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                    'CODE' => $this->arParams['LABEL_PROP'],
                    'PROPERTY_TYPE' => PropertyTable::TYPE_LIST,
                    'ACTIVE' => 'Y'
                ],
                'select' => ['ID', 'CODE', 'NAME']
            ]);
            
            $propertyNames = []; // Маппинг ID свойства -> NAME свойства (русское название)
            
            while ($property = $propertyResult->fetch()) {
                $propertyIds[] = (int)$property['ID'];
                $propertyMap[(int)$property['ID']] = $property['CODE'];
                $propertyNames[(int)$property['ID']] = $property['NAME'];
            }
            
            if (empty($propertyIds)) {
                return [];
            }

            // Получаем все enum значения для этих свойств одним запросом (D7 API)
            $enumValues = [];
            $enumResult = PropertyEnumerationTable::getList([
                'filter' => [
                    'PROPERTY_ID' => $propertyIds
                ],
                'select' => ['ID', 'VALUE', 'XML_ID', 'PROPERTY_ID']
            ]);
            
            while ($enum = $enumResult->fetch()) {
                $enumValues[(int)$enum['ID']] = [
                    'VALUE' => $enum['VALUE'],
                    'XML_ID' => $enum['XML_ID'],
                    'PROPERTY_ID' => (int)$enum['PROPERTY_ID']
                ];
            }

            // Массовая выборка значений свойств для всех элементов одним запросом
            $propertyValuesResult = CIBlockElement::GetPropertyValues(
                $this->arParams['IBLOCK_ID'],
                ['ID' => $elementIds],
                false,
                ['ID' => $propertyIds]
            );
            
            // GetPropertyValues возвращает объект CIBlockPropertyResult, получаем данные через Fetch
            $propertyValues = [];
            while ($row = $propertyValuesResult->Fetch()) {
                $propertyValues[$row['IBLOCK_ELEMENT_ID']] = $row;
            }

            // Обрабатываем полученные значения
            foreach ($propertyValues as $elementId => $properties) {
                $elementLabels = [];
                
                foreach ($propertyIds as $propertyId) {
                    // Ключ в результате - это ID свойства
                    if (!isset($properties[$propertyId])) {
                        continue;
                    }
                    
                    $value = $properties[$propertyId];
                    if (empty($value)) {
                        continue;
                    }
                    
                    // Обрабатываем множественные значения
                    $values = is_array($value) ? $value : [$value];
                    
                    foreach ($values as $enumId) {
                        $enumIdInt = (int)$enumId;
                        if (isset($enumValues[$enumIdInt])) {
                            // Используем CODE свойства как ключ (NEWPRODUCT, SALELEADER, SPECIALOFFER)
                            // А значением - русское название свойства (Новинка, Хит, Акция)
                            $propCode = $propertyMap[$propertyId];
                            $elementLabels[$propCode] = $propertyNames[$propertyId];
                        }
                    }
                }
                
                if (!empty($elementLabels)) {
                    $labels[(int)$elementId] = $elementLabels;
                }
            }
        } catch (\Exception $e) {
            AddMessage2Log('Ошибка получения лейблов: ' . $e->getMessage());
        }

        return $labels;
    }

    /**
     * Получение даты поступления для товаров (свойство DATA_POSTUPLENIYA)
     *
     * @param array<int> $elementIds Массив ID товаров
     * @return array<int, string> [ID товара => значение свойства]
     */
    private function getArrivalDates(array $elementIds): array
    {
        if (empty($elementIds)) {
            return [];
        }

        try {
            $property = PropertyTable::getList([
                'filter' => [
                    'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                    'CODE' => self::ARRIVAL_DATE_PROPERTY_CODE,
                    'ACTIVE' => 'Y',
                ],
                'select' => ['ID'],
                'limit' => 1,
            ])->fetch();

            $propertyId = (int)($property['ID'] ?? 0);
            if ($propertyId <= 0) {
                return [];
            }

            $result = CIBlockElement::GetPropertyValues(
                $this->arParams['IBLOCK_ID'],
                ['ID' => $elementIds],
                false,
                ['ID' => [$propertyId]]
            );

            $arrivalDates = [];
            while ($row = $result->Fetch()) {
                $elementId = (int)($row['IBLOCK_ELEMENT_ID'] ?? 0);
                if ($elementId <= 0) {
                    continue;
                }

                $value = $row[$propertyId] ?? '';
                if (empty($value)) {
                    continue;
                }

                $valueStr = is_array($value) ? implode(' / ', array_filter($value)) : (string)$value;
                $valueStr = trim($valueStr);
                if ($valueStr === '') {
                    continue;
                }

                $arrivalDates[$elementId] = $valueStr;
            }

            return $arrivalDates;
        } catch (\Exception $e) {
            AddMessage2Log('Ошибка получения даты поступления: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Генерация ID кеша для поискового запроса
     * 
     * @param string $query Поисковый запрос
     * @param int $offset Смещение
     * @return string
     */
    private function generateCacheId(string $query, int $offset = 0): string
    {
        return md5(Json::encode([
            'query' => $query,
            'offset' => $offset,
            'iblock_id' => $this->arParams['IBLOCK_ID'],
            'items_limit' => $this->arParams['ITEMS_LIMIT'],
            'sections_limit' => $this->arParams['SECTIONS_LIMIT'],
            'price_code' => $this->arParams['PRICE_CODE']
        ]));
    }
}

