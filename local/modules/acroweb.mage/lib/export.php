<?php

namespace Acroweb\Mage;

use Bitrix\Main\Loader;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Application;

class Export
{
    private string $exportPath;
    private string $xmlPath;
    private string $imagesPath;

    public function __construct()
    {
        Loader::includeModule('iblock');

        $documentRoot = Application::getDocumentRoot();
        $bLocal = $this->isLocalDir();
        $this->exportPath = $documentRoot . '/' . ($bLocal ? 'local' : 'bitrix') . '/modules/acroweb.mage/install/wizards/acroweb/UniBrix/site/services/iblock/';
        $this->xmlPath = $this->exportPath . 'xml/';
        $this->imagesPath = $this->exportPath . 'images/';

        if (!is_dir($this->xmlPath)) {
            mkdir($this->xmlPath, 0755, true);
        }
        if (!is_dir($this->imagesPath)) {
            mkdir($this->imagesPath, 0755, true);
        }
    }

    public function isLocalDir()
    {
        return preg_match("#/local/#", str_replace('\\', '/', __FILE__));
    }

    public function exportAll()
    {
        if (!is_dir($this->xmlPath) && !mkdir($this->xmlPath, 0755, true)) {
            $this->log("Failed to create directory: {$this->xmlPath}");
            return false;
        }
        if (!is_dir($this->imagesPath) && !mkdir($this->imagesPath, 0755, true)) {
            $this->log("Failed to create directory: {$this->imagesPath}");
            return false;
        }

        $this->exportIblockTypes();
        $this->exportIblocks();
        $this->exportIblockSections();
        $this->exportIblockElements();
        $this->exportFormSettings();

        $this->exportWebForms();

        return true;
    }

    private function exportFormSettings()
    {
        $connection = Application::getConnection();
        $sql = "SELECT * FROM b_user_option WHERE CATEGORY = 'form' AND NAME LIKE 'form_element_%'";
        $result = $connection->query($sql);

        $settings = [];
        while ($row = $result->fetch()) {
            $iblockId = str_replace('form_element_', '', $row['NAME']);
            $settings[$iblockId] = unserialize($row['VALUE']);
            $settings[$iblockId]['COMMON'] = $row['COMMON'];
        }

        $iblockNames = [];
        $iblockCodes = [];
        $iblockProperties = [];
        $res = \CIBlock::GetList([], ['ID' => array_keys($settings)]);
        while ($ar_res = $res->Fetch()) {
            $iblockNames[$ar_res['ID']] = $ar_res['NAME'];
            $iblockCodes[$ar_res['ID']] = $ar_res['CODE'];

            // Получаем свойства инфоблока
            $properties = \CIBlockProperty::GetList([], ['IBLOCK_ID' => $ar_res['ID']]);
            while ($prop = $properties->Fetch()) {
                $iblockProperties[$ar_res['ID']][$prop['ID']] = $prop['CODE'];
            }
        }

        // Добавляем названия и коды инфоблоков к настройкам, обрабатываем свойства
        foreach ($settings as $iblockId => &$formSettings) {
            $formSettings['IBLOCK_NAME'] = $iblockNames[$iblockId] ?? 'Unknown';
            $formSettings['IBLOCK_CODE'] = $iblockCodes[$iblockId] ?? 'Unknown';

            // Сохраняем ID свойств с их кодами для последующей замены при импорте
            $formSettings['PROPERTY_IDS'] = [];
            if (isset($formSettings['tabs'])) {
                $tabsArray = explode(',', $formSettings['tabs']);
                foreach ($tabsArray as $item) {
                    if (strpos($item, '--PROPERTY_') === 0) {
                        $parts = explode('--#--', $item);
                        $propId = substr($parts[0], 11); // Remove '--PROPERTY_' prefix

                        if (isset($iblockProperties[$iblockId][$propId])) {
                            $propCode = $iblockProperties[$iblockId][$propId];

                            $formSettings['PROPERTY_IDS'][$propCode] = $propId;
                        }
                    }
                }
            }
        }

        $jsonSettings = json_encode($settings, JSON_PRETTY_PRINT);
        $filePath = $this->xmlPath . 'form_settings.json';
        if (file_put_contents($filePath, $jsonSettings)) {
            $this->log("Form settings exported successfully to {$filePath}");
        } else {
            $this->log("Failed to export form settings to {$filePath}");
        }
    }

    private function exportIblockTypes()
    {
        $types = \CIBlockType::GetList();
        $typesData = [];
        while ($type = $types->Fetch()) {
            if ($arType = \CIBlockType::GetByIDLang($type["ID"], LANGUAGE_ID)) {
                $typesData[] = $arType;
            }
        }
        file_put_contents($this->xmlPath . 'iblock_types.json', json_encode($typesData));
    }

    private function exportIblocks()
    {
        $iblocks = IblockTable::getList([
            'select' => ['ID', 'CODE', 'NAME', 'IBLOCK_TYPE_ID'],
            'filter' => ['ACTIVE' => 'Y'],
        ])->fetchAll();

        foreach ($iblocks as $iblock) {
            if (empty($iblock['CODE'])) {
                $iblockCode = 'iblock_' . $iblock['ID'];
            } else {
                $iblockCode = $iblock['CODE'];
            }

            $iblockData = \CIBlock::GetArrayByID($iblock['ID']);

            // Добавляем информацию о торговом каталоге
            if (Loader::includeModule('catalog')) {
                $catalogInfo = \CCatalog::GetByID($iblock['ID']);
                if ($catalogInfo) {
                    $iblockData['IS_CATALOG'] = 'Y';
                    $iblockData['CATALOG_TYPE'] = $catalogInfo['CATALOG_TYPE'];
                    $iblockData['OFFERS_IBLOCK_ID'] = $catalogInfo['OFFERS_IBLOCK_ID'];
                    $iblockData['OFFERS_PROPERTY_ID'] = $catalogInfo['OFFERS_PROPERTY_ID'];
                } else {
                    $iblockData['IS_CATALOG'] = 'N';
                }
            }

            $iblockData['FIELDS'] = \CIBlock::GetFields($iblock['ID']);

            $ipropValues = new \Bitrix\Iblock\InheritedProperty\IblockTemplates($iblock['ID']);
            $templates = $ipropValues->findTemplates();
            $iblockData['IPROPERTY_TEMPLATES'] = [];
            foreach ($templates as $code => $template) {
                $iblockData['IPROPERTY_TEMPLATES'][$code] = $template['TEMPLATE'];
            }

            // Получаем пользовательские поля инфоблока
            $userFields = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('IBLOCK_' . $iblock['ID'] . '_SECTION');
            if (!empty($userFields)) {
                $iblockData['USER_FIELDS'] = $userFields;
            }

            // Добавляем явное получение настроек доступа
            $iblockData['GROUP_PERMISSIONS'] = \CIBlock::GetGroupPermissions($iblock['ID']);
            $iblockData['RIGHTS_MODE'] = $iblockData['RIGHTS_MODE'];
            $iblockData['ID'] = $iblock['ID'];

            file_put_contents($this->xmlPath . $iblockCode . '.json', json_encode($iblockData, JSON_PRETTY_PRINT));

            $this->exportIblockProperties($iblock['ID'], $iblockCode);

            $this->log("Exporting iblock: ID={$iblock['ID']}, CODE={$iblockCode}");
        }
    }

    private function exportIblockProperties($iblockId, $iblockCode)
    {
        $properties = PropertyTable::getList([
            'filter' => ['IBLOCK_ID' => $iblockId],
        ])->fetchAll();

        // Получаем информацию о каталоге
        $catalogInfo = null;
        if (Loader::includeModule('catalog')) {
            $catalogInfo = \CCatalog::GetByID($iblockId);
        }

        // Если это инфоблок торговых предложений, добавляем свойство связи
        if ($catalogInfo && $catalogInfo['PRODUCT_IBLOCK_ID']) {
            $linkProperty = PropertyTable::getList([
                'filter' => [
                    'IBLOCK_ID' => $iblockId,
                    'ID' => $catalogInfo['OFFERS_PROPERTY_ID']
                ]
            ])->fetch();

            if ($linkProperty) {
                $linkProperty['IS_OFFER_PROPERTY'] = true;
                $linkProperty['PRODUCT_IBLOCK_ID'] = $catalogInfo['PRODUCT_IBLOCK_ID'];
                $properties[] = $linkProperty;
            }
        }

        foreach ($properties as &$prop) {
            // Добавляем описание свойства
            $prop['DESCRIPTION'] = $prop['HINT'];

            // Добавляем специальную обработку для свойства связи с торговыми предложениями
            if ($catalogInfo && $catalogInfo['OFFERS_IBLOCK_ID'] && $prop['ID'] == $catalogInfo['OFFERS_PROPERTY_ID']) {
                $prop['IS_OFFER_PROPERTY'] = true;
                $prop['OFFERS_IBLOCK_ID'] = $catalogInfo['OFFERS_IBLOCK_ID'];
            }

            // Добавляем параметры отображения
            $prop['SHOW_IN_LIST'] = $prop['IS_REQUIRED']; // Показывать на странице списка элементов
            $prop['SHOW_ON_DETAIL_PAGE'] = $prop['MULTIPLE']; // Показывать на детальной странице элемента

            if ($prop['PROPERTY_TYPE'] == 'E') {
                $linkedIblock = \CIBlock::GetByID($prop['LINK_IBLOCK_ID'])->Fetch();
                if ($linkedIblock) {
                    $prop['LINK_IBLOCK_CODE'] = $linkedIblock['CODE'] ?: 'iblock_' . $linkedIblock['ID'];
                    $prop['LINK_IBLOCK_TYPE'] = $linkedIblock['IBLOCK_TYPE_ID'];
                } else {
                    $prop['LINK_IBLOCK_CODE'] = null;
                    $prop['LINK_IBLOCK_TYPE'] = null;
                }
            }

            // Обработка значений списка
            if ($prop['PROPERTY_TYPE'] == 'L') {
                $enumValues = \CIBlockPropertyEnum::GetList([], ["PROPERTY_ID" => $prop['ID']]);
                $prop['VALUES'] = [];
                while ($enumValue = $enumValues->GetNext()) {
                    $prop['VALUES'][] = $enumValue;
                }
            }

            // Обработка значений по умолчанию для text/html
            if ($prop['PROPERTY_TYPE'] == 'S' && $prop['USER_TYPE'] == 'HTML') {
                $prop['DEFAULT_VALUE'] = unserialize($prop['DEFAULT_VALUE']);
            }

            // Добавляем дополнительные параметры для строковых свойств
            if ($prop['PROPERTY_TYPE'] == 'S') {
                $prop['ROW_COUNT'] = $prop['ROW_COUNT'] ?? 1;
                $prop['COL_COUNT'] = $prop['COL_COUNT'] ?? 30;
            }

            // Добавляем параметры для числовых свойств
            if ($prop['PROPERTY_TYPE'] == 'N') {
                $prop['PRECISION'] = $prop['PRECISION'] ?? 0;
            }

            // Обработка свойств типа "Файл"
            if ($prop['PROPERTY_TYPE'] == 'F') {
                if ($prop['DEFAULT_VALUE']) {
                    $prop['DEFAULT_VALUE'] = $this->processAndSaveImage($prop['DEFAULT_VALUE']);
                }
                // Добавляем дополнительные параметры для файловых свойств
                $prop['FILE_TYPE'] = $prop['FILE_TYPE'] ?? '';
                $prop['EXTENSIONS'] = $prop['FILE_TYPE'] ? explode(',', $prop['FILE_TYPE']) : [];
                $prop['MAX_FILE_SIZE'] = $prop['FILE_SIZE'] ?? 0;
            }

            if ($prop['PROPERTY_TYPE'] == 'E') {
                $linkedIblock = \CIBlock::GetByID($prop['LINK_IBLOCK_ID'])->Fetch();
                if ($linkedIblock) {
                    $prop['LINK_IBLOCK_CODE'] = $linkedIblock['CODE'] ?: 'iblock_' . $linkedIblock['ID'];
                    $prop['LINK_IBLOCK_TYPE'] = $linkedIblock['IBLOCK_TYPE_ID'];
                    $prop['LINK_IBLOCK_ID'] = $linkedIblock['ID']; // Добавляем ID связанного инфоблока
                } else {
                    $prop['LINK_IBLOCK_CODE'] = null;
                    $prop['LINK_IBLOCK_TYPE'] = null;
                    $prop['LINK_IBLOCK_ID'] = null;
                }
            }
        }

        file_put_contents(
            $this->xmlPath . $iblockCode . '_properties.json',
            json_encode($properties, JSON_PRETTY_PRINT)
        );
    }

    private function exportIblockElements()
    {
        $iblocks = IblockTable::getList([
            'select' => ['ID', 'CODE'],
            'filter' => ['ACTIVE' => 'Y'],
        ])->fetchAll();

        $hasCatalogModule = Loader::includeModule('catalog');

        foreach ($iblocks as $iblock) {
            if (empty($iblock['CODE'])) {
                $iblockCode = 'iblock_' . $iblock['ID'];
            } else {
                $iblockCode = $iblock['CODE'];
            }

            $elements = \CIBlockElement::GetList(
                [],
                ['IBLOCK_ID' => $iblock['ID']],
                false,
                false,
                [
                    'ID',
                    'IBLOCK_ID',
                    'NAME',
                    'CODE',
                    'SORT',
                    'PREVIEW_TEXT',
                    'PREVIEW_TEXT_TYPE',
                    'DETAIL_TEXT',
                    'DETAIL_TEXT_TYPE',
                    'PREVIEW_PICTURE',
                    'DETAIL_PICTURE',
                    'IBLOCK_SECTION_ID',
                    'PROPERTY_*',
                ]
            );

            $elementsData = [];
            while ($element = $elements->GetNextElement()) {
                $fields = $element->GetFields();
                $properties = $element->GetProperties();

                $elementData = array_merge($fields, ['PROPERTIES' => []]);
                $elementData['IBLOCK_SECTION_ID'] = $fields['IBLOCK_SECTION_ID'];

                // Обработка PREVIEW_PICTURE
                if ($fields['PREVIEW_PICTURE']) {
                    $elementData['PREVIEW_PICTURE'] = $this->processAndSaveImage($fields['PREVIEW_PICTURE']);
                }

                // Обработка DETAIL_PICTURE
                if ($fields['DETAIL_PICTURE']) {
                    $elementData['DETAIL_PICTURE'] = $this->processAndSaveImage($fields['DETAIL_PICTURE']);
                }

                // Сохраняем PREVIEW_TEXT и DETAIL_TEXT
                $elementData['PREVIEW_TEXT'] = $fields['PREVIEW_TEXT'];
                $elementData['PREVIEW_TEXT_TYPE'] = $fields['PREVIEW_TEXT_TYPE'];
                $elementData['DETAIL_TEXT'] = $fields['DETAIL_TEXT'];
                $elementData['DETAIL_TEXT_TYPE'] = $fields['DETAIL_TEXT_TYPE'];

                // Обработка свойств
                foreach ($properties as $code => $prop) {
                    $elementData['PROPERTIES'][$code] = [
                        'VALUE' => $prop['VALUE'],
                        'DESCRIPTION' => $prop['DESCRIPTION'],
                        'PROPERTY_TYPE' => $prop['PROPERTY_TYPE'],
                        'USER_TYPE' => $prop['USER_TYPE'],
                    ];

                    // Обработка свойств типа "Файл"
                    if ($prop['PROPERTY_TYPE'] === 'L') {
                        $elementData['PROPERTIES'][$code]['VALUE'] = [];
                        if (is_array($prop['VALUE'])) {
                            foreach ($prop['VALUE'] as $value) {
                                $enumValue = \CIBlockPropertyEnum::GetList([], [
                                    'PROPERTY_ID' => $prop['ID'],
                                    'VALUE' => $value,
                                ])->Fetch();
                                if ($enumValue) {
                                    $elementData['PROPERTIES'][$code]['VALUE'][] = $enumValue['XML_ID'];
                                }
                            }
                        } else {
                            $enumValue = \CIBlockPropertyEnum::GetList([], [
                                'PROPERTY_ID' => $prop['ID'],
                                'VALUE' => $prop['VALUE'],
                            ])->Fetch();
                            if ($enumValue) {
                                $elementData['PROPERTIES'][$code]['VALUE'] = $enumValue['XML_ID'];
                            }
                        }
                    } elseif ($prop['PROPERTY_TYPE'] === 'F' && !empty($prop['VALUE'])) {
                        if (is_array($prop['VALUE'])) {
                            foreach ($prop['VALUE'] as $key => $fileId) {
                                $elementData['PROPERTIES'][$code]['VALUE'][$key] = $this->processAndSaveImage($fileId);
                            }
                        } else {
                            $elementData['PROPERTIES'][$code]['VALUE'] = $this->processAndSaveImage($prop['VALUE']);
                        }
                    } elseif ($prop['PROPERTY_TYPE'] === 'S') {
                        $elementData['PROPERTIES'][$code]['VALUE'] = $prop['VALUE'];
                        $elementData['PROPERTIES'][$code]['DESCRIPTION'] = $prop['DESCRIPTION'];
                    }
                }

                // Если модуль каталога установлен, добавляем данные каталога
                if ($hasCatalogModule) {
                    $this->addCatalogData($elementData, $fields['ID']);
                }

                $elementsData[] = $elementData;
            }

            file_put_contents(
                $this->xmlPath . $iblockCode . '_elements.json',
                json_encode($elementsData, JSON_PRETTY_PRINT)
            );
            $this->log("Exporting elements for iblock: ID={$iblock['ID']}, CODE={$iblockCode}");
        }
    }

    private function addCatalogData(&$elementData, $elementId)
    {
        $catalogData = \CCatalogProduct::GetByID($elementId);
        if ($catalogData) {
            $elementData['CATALOG'] = [
                'QUANTITY' => $catalogData['QUANTITY'],
                'WEIGHT' => $catalogData['WEIGHT'],
                'WIDTH' => $catalogData['WIDTH'],
                'LENGTH' => $catalogData['LENGTH'],
                'HEIGHT' => $catalogData['HEIGHT'],
                'VAT_ID' => $catalogData['VAT_ID'],
                'VAT_INCLUDED' => $catalogData['VAT_INCLUDED'],
                'MEASURE' => $catalogData['MEASURE'],
                'QUANTITY_TRACE' => $catalogData['QUANTITY_TRACE'],
                'CAN_BUY_ZERO' => $catalogData['CAN_BUY_ZERO'],
                'SUBSCRIBE' => $catalogData['SUBSCRIBE'],
            ];

            // Получение цен
            $prices = \CPrice::GetList(
                [],
                ['PRODUCT_ID' => $elementId],
                false,
                false,
                ['CATALOG_GROUP_ID', 'PRICE', 'CURRENCY']
            );
            $elementData['CATALOG']['PRICES'] = [];
            while ($price = $prices->Fetch()) {
                $elementData['CATALOG']['PRICES'][] = $price;
            }
        }
    }

    private function exportIblockSections()
    {
        $iblocks = IblockTable::getList([
            'select' => ['ID', 'CODE'],
            'filter' => ['ACTIVE' => 'Y'],
        ])->fetchAll();

        foreach ($iblocks as $iblock) {
            $iblockCode = $iblock['CODE'] ?: 'iblock_' . $iblock['ID'];

            $sections = \CIBlockSection::GetList(
                ['LEFT_MARGIN' => 'ASC'],
                ['IBLOCK_ID' => $iblock['ID'], 'ACTIVE' => 'Y'],
                false,
                [
                    'ID',
                    'IBLOCK_ID',
                    'NAME',
                    'CODE',
                    'IBLOCK_SECTION_ID',
                    'DEPTH_LEVEL',
                    'DESCRIPTION',
                    'DESCRIPTION_TYPE',
                    'PICTURE',
                    'DETAIL_PICTURE',
                    'UF_*',
                ]
            );

            $sectionsData = [];
            while ($section = $sections->GetNext()) {
                // Обработка PICTURE и DETAIL_PICTURE
                if ($section['PICTURE']) {
                    $section['PICTURE'] = $this->processAndSaveImage($section['PICTURE']);
                }
                if ($section['DETAIL_PICTURE']) {
                    $section['DETAIL_PICTURE'] = $this->processAndSaveImage($section['DETAIL_PICTURE']);
                }

                $sectionsData[] = $section;
            }

            file_put_contents(
                $this->xmlPath . $iblockCode . '_sections.json',
                json_encode($sectionsData, JSON_PRETTY_PRINT)
            );
            $this->log("Exporting sections for iblock: ID={$iblock['ID']}, CODE={$iblockCode}");
        }
    }

    private function log($message)
    {
        file_put_contents(
            $this->exportPath . 'export_log.txt',
            date('Y-m-d H:i:s') . ': ' . $message . PHP_EOL,
            FILE_APPEND
        );
    }

    private function processAndSaveImage($fileId)
    {
        $file = \CFile::GetFileArray($fileId);
        if ($file) {
            $newFileName = $fileId . '_' . $file['FILE_NAME']; // Добавляем ID файла к имени для уникальности
            $sourcePath = Application::getDocumentRoot() . $file['SRC'];
            if (!is_dir($this->imagesPath . $file['SUBDIR'] . '/')) {
                mkdir($this->imagesPath . $file['SUBDIR'] . '/', 0755, true);
            }

            $destinationPath = $this->imagesPath . $newFileName;
            if ($file['SUBDIR']) {
                $destinationPath = $this->imagesPath . $file['SUBDIR'] . '/' . $newFileName;
            }


            if (copy($sourcePath, $destinationPath)) {
                return [
                    'ID' => $fileId,
                    'FILE_NAME' => $newFileName,
                    'ORIGINAL_NAME' => $file['ORIGINAL_NAME'],
                    'CONTENT_TYPE' => $file['CONTENT_TYPE'],
                    'SUBDIR' => $file['SUBDIR'],
                ];
            }
        }
        return null;
    }

    private function exportWebForms()
    {
        if (!Loader::includeModule('form')) {
            $this->log("Form module is not installed. Web forms export skipped.");
            return;
        }

        $forms = \CForm::GetList($by = "s_id", $order = "desc", [], $filtered = false);
        $formsData = [];

        while ($form = $forms->Fetch()) {
            try {
                $formId = $form['ID'];
                $formData = [
                    'FORM' => $form,
                    'QUESTIONS' => [],
                    'STATUSES' => [],
                    'RESULTS' => [],
                ];

                // Получение информации о меню формы
                $rsMenu = \CForm::GetMenuList(['FORM_ID' => $formId]);
                while ($arMenu = $rsMenu->Fetch()) {
                    $formData['FORM']['MENU'][$arMenu['LID']] = $arMenu['MENU'];
                }

                // Экспорт вопросов формы
                $questions = \CFormField::GetList($formId, "ALL", $by = "s_id", $order = "asc", [], $filtered = false);
                while ($question = $questions->Fetch()) {
                    $questionId = $question['ID'];
                    $formData['QUESTIONS'][$questionId] = $question;

                    // Экспорт ответов для вопроса
                    $answers = \CFormAnswer::GetList($questionId, $by = "s_id", $order = "asc", [], $filtered = false);
                    while ($answer = $answers->Fetch()) {
                        $formData['QUESTIONS'][$questionId]['ANSWERS'][] = $answer;
                    }
                }

                // Экспорт статусов формы
                $statuses = \CFormStatus::GetList($formId, $by = "s_id", $order = "asc", [], $filtered = false);
                while ($status = $statuses->Fetch()) {
                    $formData['STATUSES'][] = $status;
                }

                // Экспорт результатов формы (ограничим до 1000 последних результатов)
                $results = \CFormResult::GetList(
                    $formId,
                    $by = "s_id",
                    $order = "desc",
                    [],
                    $filtered = false,
                    'N',
                    1000
                );
                while ($result = $results->Fetch()) {
                    $resultId = $result['ID'];
                    $formData['RESULTS'][$resultId] = $result;

                    // Экспорт ответов для результата
                    $arrResult = [];
                    $arrAnswers = [];
                    $resultAnswers = \CFormResult::GetDataByID($resultId, [], $arrResult, $arrAnswers, "Y");
                    $formData['RESULTS'][$resultId]['ANSWERS'] = $resultAnswers;
                }

                // Получение шаблона формы
                $formData['TEMPLATE'] = $form['FORM_TEMPLATE'];

                $formsData[$formId] = $formData;
                $this->log("Form with ID {$formId} exported successfully.");
            } catch (\Exception $e) {
                $this->log("Error exporting form with ID {$formId}: " . $e->getMessage());
            }
        }

        // Сохраняем данные форм в файл
        $filePath = $this->xmlPath . 'web_forms.json';
        if (file_put_contents($filePath, json_encode($formsData, JSON_PRETTY_PRINT))) {
            $this->log("Web forms exported successfully to {$filePath}");
        } else {
            $this->log("Failed to export web forms to {$filePath}");
        }
    }
}