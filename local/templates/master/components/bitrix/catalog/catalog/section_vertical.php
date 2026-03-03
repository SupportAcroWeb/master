<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


use Acroweb\Mage\Helpers\TemplateHelper;
use Bitrix\Main\Application;

/**
 * @global CMain $APPLICATION
 * @var CBitrixComponent $component
 * @var array $arParams
 * @var array $arResult
 * @var array $arCurSection
 */

if (isset($arParams['USE_COMMON_SETTINGS_BASKET_POPUP']) && $arParams['USE_COMMON_SETTINGS_BASKET_POPUP'] == 'Y') {
    $basketAction = $arParams['COMMON_ADD_TO_BASKET_ACTION'] ?? '';
} else {
    $basketAction = $arParams['SECTION_ADD_TO_BASKET_ACTION'] ?? '';
}

// Обработка сортировки
$sortOptions = [
    'default' => [
        'field' => $arParams["ELEMENT_SORT_FIELD"],
        'order' => $arParams["ELEMENT_SORT_ORDER"],
        'name' => GetMessage('SORT_DEFAULT') ?: 'По умолчанию'
    ],
    'popularity_asc' => [
        'field' => 'shows',
        'order' => 'asc',
        'name' => GetMessage('SORT_POPULARITY_ASC') ?: 'По популярности (возрастание)'
    ],
    'popularity_desc' => [
        'field' => 'shows',
        'order' => 'desc',
        'name' => GetMessage('SORT_POPULARITY_DESC') ?: 'По популярности (убывание)'
    ],
//    'name_asc' => [
//        'field' => 'name',
//        'order' => 'asc',
//        'name' => GetMessage('SORT_NAME_ASC') ?: 'По названию (А-Я)'
//    ],
//    'name_desc' => [
//        'field' => 'name',
//        'order' => 'desc',
//        'name' => GetMessage('SORT_NAME_DESC') ?: 'По названию (Я-А)'
//    ],
//    'price_asc' => [
//        'field' => 'catalog_PRICE_1',
//        'order' => 'asc',
//        'name' => GetMessage('SORT_PRICE_ASC') ?: 'По цене (сначала дешевые)'
//    ],
//    'price_desc' => [
//        'field' => 'catalog_PRICE_1',
//        'order' => 'desc',
//        'name' => GetMessage('SORT_PRICE_DESC') ?: 'По цене (сначала дорогие)'
//    ]
];

$request = Application::getInstance()->getContext()->getRequest();
$selectedSort = $request->get('sort') ?: 'default';

// Валидация выбранного значения сортировки
if (!isset($sortOptions[$selectedSort])) {
    $selectedSort = 'default';
}

$currentSort = $sortOptions[$selectedSort];

?>

<div class="block-breadcrumbs">
    <div class="container"> <?
        TemplateHelper::includePartial('breadcrumbs'); ?>
        <h1 class="title2"><? $APPLICATION->ShowTitle(false, false); ?></h1>
    </div>
</div>
<div class="container">
    <div class="grid1">
        <div class="grid1__aside">
            <?php if (!empty($arResult['IS_SEARCH'])): ?>
                <!-- Форма поиска -->
                <div class="search-form-block" style="background:#fff;padding:20px;border-radius:8px;margin-bottom:20px;">
                    <form action="<?= $arResult["FOLDER"] ?>" method="get">
                        <div style="margin-bottom:15px;">
                            <label style="display:block;margin-bottom:5px;font-weight:500;">Поиск:</label>
                            <input type="text" name="q" value="<?= htmlspecialcharsbx($arResult['SEARCH_QUERY']) ?>" 
                                   placeholder="Введите запрос" required
                                   style="width:100%;padding:10px;border:1px solid #ddd;border-radius:4px;">
                        </div>
                        <button type="submit" class="btn btn_black" style="width: 100%;">
                            Найти
                        </button>
                    </form>
                </div>
                
                <?php if (!empty($arResult['SEARCH_SECTIONS'])): 
                    $currentSectionId = (int)$request->get('SECTION_ID');
                    $searchQuery = htmlspecialcharsbx($arResult['SEARCH_QUERY']);
                ?>
                    <!-- Категории найденных товаров -->
                    <div class="btn-toggle">
                        <span>Категории</span>
                    </div>
                    <div class="grid1__inner1">
                        <ul class="menu1 menu1_expandable">
                            <?php foreach ($arResult['SEARCH_SECTIONS'] as $section):
                                $isActive = ($currentSectionId == $section['ID']);
                                $url = $arResult["FOLDER"] . '?q=' . urlencode($searchQuery) . '&SECTION_ID=' . $section['ID'];
                            ?>
                                <li<?= $isActive ? ' class="active"' : '' ?>>
                                    <a href="<?= $url ?>">
                                        <?= htmlspecialcharsbx($section['NAME']) ?> (<?= $section['COUNT'] ?>)
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php
                $APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "left_multilevel",
                    Array(
                        "ROOT_MENU_TYPE" => "topcatalog",
                        "MAX_LEVEL" => "5",
                        "CHILD_MENU_TYPE" => "topcatalog",
                        "USE_EXT" => "Y",
                        "ALLOW_MULTI_SELECT" => "Y"
                    )
                );
                ?>
            <?php endif; ?>
            <?
            // Не показываем умный фильтр если это режим поиска без результатов
            if (empty($arResult['IS_SEARCH']) || !empty($arResult['SEARCH_FOUND_IDS'])) {
                // Определяем SECTION_ID для умного фильтра
                $filterSectionId = $arCurSection['ID'];
                if (!empty($arResult['IS_SEARCH'])) {
                    $selectedSectionId = (int)$request->get('SECTION_ID');
                    if ($selectedSectionId > 0) {
                        $filterSectionId = $selectedSectionId;
                    }
                }
                
                // Подготовка параметров для умного фильтра
                $smartFilterParams = array(
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "SECTION_ID" => $filterSectionId,
                    "FILTER_NAME" => $arParams["FILTER_NAME"],
                    "PRICE_CODE" => $arParams["~PRICE_CODE"],
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "SAVE_IN_SESSION" => "N",
                    "FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],
                    "XML_EXPORT" => "N",
                    "SECTION_TITLE" => "NAME",
                    "SECTION_DESCRIPTION" => "DESCRIPTION",
                    'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                    "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
                    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                    "SEF_MODE" => $arParams["SEF_MODE"],
                    "SEF_RULE" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["smart_filter"],
                    "SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
                    "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                    "INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"],
                );
                
                // Если это режим поиска с результатами, устанавливаем префильтр с найденными ID
                if (!empty($arResult['IS_SEARCH']) && !empty($arResult['SEARCH_FOUND_IDS'])) {
                    $prefilterName = 'searchPreFilter';
                    $GLOBALS[$prefilterName]['ID'] = $arResult['SEARCH_FOUND_IDS'];
                    $smartFilterParams['PREFILTER_NAME'] = $prefilterName;
                }
                
                $APPLICATION->IncludeComponent(
                    "bitrix:catalog.smart.filter",
                    "visual_vertical",
                    $smartFilterParams,
                    $component,
                    array('HIDE_ICONS' => 'Y')
                );
            }

            ?>
        </div>
        <div class="grid1__content">
            <div class="catalog-topbar">
                <div class="catalog-topbar__left">
                    <button data-action="showFilter" class="btn-filter" type="button">
                        <svg aria-hidden="true" width="18" height="18">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#filter1"></use>
                        </svg>
                        Фильтры
                    </button>
                </div>
                <div class="catalog-topbar__right">
                    <select data-select class="ts-wrapper_text" name="sort" id="catalog-sort">
                        <?php foreach ($sortOptions as $key => $option): ?>
                            <option value="<?= $key ?>"<?= $selectedSort === $key ? ' selected' : '' ?>>
                                <?= htmlspecialcharsbx($option['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?
            $intSectionID = $APPLICATION->IncludeComponent(
                "bitrix:catalog.section",
                "catalog",
                array(
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "ELEMENT_SORT_FIELD" => $currentSort['field'],
                    "ELEMENT_SORT_ORDER" => $currentSort['order'],
                    "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                    "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                    "PROPERTY_CODE" => (isset($arParams["LIST_PROPERTY_CODE"]) ? $arParams["LIST_PROPERTY_CODE"] : []),
                    "PROPERTY_CODE_MOBILE" => $arParams["LIST_PROPERTY_CODE_MOBILE"],
                    "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
                    "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
                    "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
                    "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
                    "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
                    "BASKET_URL" => $arParams["BASKET_URL"],
                    "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                    "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                    "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                    "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                    "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                    "FILTER_NAME" => $arParams["FILTER_NAME"],
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "SET_TITLE" => $arParams["SET_TITLE"],
                    "MESSAGE_404" => $arParams["~MESSAGE_404"],
                    "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                    "SHOW_404" => $arParams["SHOW_404"],
                    "FILE_404" => $arParams["FILE_404"],
                    "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                    "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
                    "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
                    "PRICE_CODE" => $arParams["~PRICE_CODE"],
                    "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                    "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

                    "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                    "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                    "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                    "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                    "PRODUCT_PROPERTIES" => (isset($arParams["PRODUCT_PROPERTIES"]) ? $arParams["PRODUCT_PROPERTIES"] : []),

                    "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
                    "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
                    "PAGER_TITLE" => $arParams["PAGER_TITLE"],
                    "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
                    "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
                    "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
                    "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
                    "PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
                    "PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
                    "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                    "LAZY_LOAD" => $arParams["LAZY_LOAD"],
                    "MESS_BTN_LAZY_LOAD" => $arParams["~MESS_BTN_LAZY_LOAD"],
                    "LOAD_ON_SCROLL" => $arParams["LOAD_ON_SCROLL"],

                    "OFFERS_CART_PROPERTIES" => (isset($arParams["OFFERS_CART_PROPERTIES"]) ? $arParams["OFFERS_CART_PROPERTIES"] : []),
                    "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
                    "OFFERS_PROPERTY_CODE" => (isset($arParams["LIST_OFFERS_PROPERTY_CODE"]) ? $arParams["LIST_OFFERS_PROPERTY_CODE"] : []),
                    "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                    "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                    "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                    "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                    "OFFERS_LIMIT" => (isset($arParams["LIST_OFFERS_LIMIT"]) ? $arParams["LIST_OFFERS_LIMIT"] : 0),

                    "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                    "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                    "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                    "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                    "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
                    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                    'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                    'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],

                    'LABEL_PROP' => $arParams['LABEL_PROP'],
                    'LABEL_PROP_MOBILE' => $arParams['LABEL_PROP_MOBILE'],
                    'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'] ?? '',
                    'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
                    'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
                    'PRODUCT_BLOCKS_ORDER' => $arParams['LIST_PRODUCT_BLOCKS_ORDER'],
                    'PRODUCT_ROW_VARIANTS' => $arParams['LIST_PRODUCT_ROW_VARIANTS'],
                    'ENLARGE_PRODUCT' => $arParams['LIST_ENLARGE_PRODUCT'],
                    'ENLARGE_PROP' => isset($arParams['LIST_ENLARGE_PROP']) ? $arParams['LIST_ENLARGE_PROP'] : '',
                    'SHOW_SLIDER' => $arParams['LIST_SHOW_SLIDER'],
                    'SLIDER_INTERVAL' => isset($arParams['LIST_SLIDER_INTERVAL']) ? $arParams['LIST_SLIDER_INTERVAL'] : '',
                    'SLIDER_PROGRESS' => isset($arParams['LIST_SLIDER_PROGRESS']) ? $arParams['LIST_SLIDER_PROGRESS'] : '',

                    'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
                    'OFFER_TREE_PROPS' => (isset($arParams['OFFER_TREE_PROPS']) ? $arParams['OFFER_TREE_PROPS'] : []),
                    'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                    'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
                    'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
                    'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
                    'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
                    'MESS_SHOW_MAX_QUANTITY' => (isset($arParams['~MESS_SHOW_MAX_QUANTITY']) ? $arParams['~MESS_SHOW_MAX_QUANTITY'] : ''),
                    'RELATIVE_QUANTITY_FACTOR' => (isset($arParams['RELATIVE_QUANTITY_FACTOR']) ? $arParams['RELATIVE_QUANTITY_FACTOR'] : ''),
                    'MESS_RELATIVE_QUANTITY_MANY' => (isset($arParams['~MESS_RELATIVE_QUANTITY_MANY']) ? $arParams['~MESS_RELATIVE_QUANTITY_MANY'] : ''),
                    'MESS_RELATIVE_QUANTITY_FEW' => (isset($arParams['~MESS_RELATIVE_QUANTITY_FEW']) ? $arParams['~MESS_RELATIVE_QUANTITY_FEW'] : ''),
                    'MESS_BTN_BUY' => (isset($arParams['~MESS_BTN_BUY']) ? $arParams['~MESS_BTN_BUY'] : ''),
                    'MESS_BTN_ADD_TO_BASKET' => (isset($arParams['~MESS_BTN_ADD_TO_BASKET']) ? $arParams['~MESS_BTN_ADD_TO_BASKET'] : ''),
                    'MESS_BTN_SUBSCRIBE' => (isset($arParams['~MESS_BTN_SUBSCRIBE']) ? $arParams['~MESS_BTN_SUBSCRIBE'] : ''),
                    'MESS_BTN_DETAIL' => (isset($arParams['~MESS_BTN_DETAIL']) ? $arParams['~MESS_BTN_DETAIL'] : ''),
                    'MESS_NOT_AVAILABLE' => $arParams['~MESS_NOT_AVAILABLE'] ?? '',
                    'MESS_NOT_AVAILABLE_SERVICE' => $arParams['~MESS_NOT_AVAILABLE_SERVICE'] ?? '',
                    'MESS_BTN_COMPARE' => (isset($arParams['~MESS_BTN_COMPARE']) ? $arParams['~MESS_BTN_COMPARE'] : ''),

                    'USE_ENHANCED_ECOMMERCE' => (isset($arParams['USE_ENHANCED_ECOMMERCE']) ? $arParams['USE_ENHANCED_ECOMMERCE'] : ''),
                    'DATA_LAYER_NAME' => (isset($arParams['DATA_LAYER_NAME']) ? $arParams['DATA_LAYER_NAME'] : ''),
                    'BRAND_PROPERTY' => (isset($arParams['BRAND_PROPERTY']) ? $arParams['BRAND_PROPERTY'] : ''),

                    'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                    "ADD_SECTIONS_CHAIN" => "Y",
                    'ADD_TO_BASKET_ACTION' => $basketAction,
                    'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
                    'COMPARE_PATH' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['compare'],
                    'COMPARE_NAME' => $arParams['COMPARE_NAME'],
                    'USE_COMPARE_LIST' => 'Y',
                    'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
                    'COMPATIBLE_MODE' => (isset($arParams['COMPATIBLE_MODE']) ? $arParams['COMPATIBLE_MODE'] : ''),
                    'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : '')
                ),
                $component
            );
            $GLOBALS['CATALOG_CURRENT_SECTION_ID'] = $intSectionID;
            ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortSelect = document.getElementById('catalog-sort');

    if (sortSelect) {
        // Восстановление сохраненного значения при загрузке
        const savedSort = localStorage.getItem('catalog_sort');
        if (savedSort && savedSort !== sortSelect.value) {
            sortSelect.value = savedSort;
        }

        // Обработчик изменения сортировки
        sortSelect.addEventListener('change', function() {
            const selectedValue = this.value;

            // Сохраняем выбор в localStorage
            localStorage.setItem('catalog_sort', selectedValue);

            // Получаем текущий URL
            const url = new URL(window.location);

            // Обновляем или добавляем параметр sort
            url.searchParams.set('sort', selectedValue);

            // Очищаем параметры пагинации при изменении сортировки
            url.searchParams.delete('PAGEN_1');
            url.searchParams.delete('SIZEN_1');

            // Переходим на новую страницу
            window.location.href = url.toString();
        });
    }
});
</script>
