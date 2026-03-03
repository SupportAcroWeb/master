<?php

namespace Acroweb\Mage;

use Bitrix\Main\Loader;
use Bitrix\Main\Application;

class Import
{
    private string $importPath;
    private string $xmlPath;
    private string $imagesPath;
    private string $siteId;
    private $deferredLinks = [];
    private $sectionMaps = [];
    private $idMap = [];
    private array $iblockIdMap = [];

    private array $propertyIdMap = [];

    public function __construct($siteId)
    {
        Loader::includeModule('iblock');

        $this->siteId = $siteId;
        $documentRoot = Application::getDocumentRoot();
        $bLocal = $this->isLocalDir();
        $this->importPath = $documentRoot . '/' . ($bLocal ? 'local' : 'bitrix') . '/modules/acroweb.mage/install/wizards/acroweb/UniBrix/site/services/iblock/';
        $this->xmlPath = $this->importPath . 'xml/';
        $this->imagesPath = $this->importPath . 'images/';

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

    public function importAll()
    {
        if (!is_dir($this->xmlPath)) {
            $this->log("Failed to find directory: {$this->xmlPath}");
            return false;
        }
        if (!is_dir($this->imagesPath)) {
            $this->log("Failed to find directory: {$this->imagesPath}");
            return false;
        }

        $this->importIblockTypes();
        $this->importIblocks();
        $this->importIblockSections();
        $this->setDeferredLinks();
        $this->importFormSettings();
        $this->importIblockElements();

        if (Loader::includeModule('form')) {
            $this->importWebForms();
        }

        return true;
    }

    public function importWebForms()
    {
        Loader::includeModule('form');

        // Отключаем упрощенный режим редактирования форм
        \COption::SetOptionString('form', 'SIMPLE', 'N');
        $this->log("Disabled simplified form editing mode");

        $formsFile = $this->xmlPath . 'web_forms.json';
        if (!file_exists($formsFile)) {
            $this->log("Web forms file not found: {$formsFile}");
            return;
        }

        $formsData = json_decode(file_get_contents($formsFile), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->log("Error decoding web forms JSON: " . json_last_error_msg());
            return;
        }

        $newAliases = [];

        foreach ($formsData as $formData) {
            $formFields = $formData['FORM'];
            $newFormAliasesNoSiteId = 'acroweb_' . $formFields['SID'];
            $formFields['SID'] = 'acroweb_' . $formFields['SID'] . '_' . $this->siteId;

            $formFields['SITE'] = [$this->siteId => $formFields['SID']]; // Устанавливаем сайт формы

            $formId = \CForm::Set($formFields);

            if ($formId > 0) {
                $this->log("Created form: " . $formFields['NAME'] . " (ID: $formId)");
                $newAliases[$formData['FORM']['SID']] = $newFormAliasesNoSiteId;

                // Обновляем меню результатов
                if (isset($formFields['MENU'])) {
                    $this->updateFormMenu($formId, $formFields['MENU']);
                }

                // Импорт вопросов и ответов
                if (isset($formData['QUESTIONS'])) {
                    $this->importFormQuestions($formId, $formData['QUESTIONS']);
                }

                // Импорт статусов
                if (isset($formData['STATUSES'])) {
                    $this->importFormStatuses($formId, $formData['STATUSES']);
                }

                // Убедимся, что форма привязана к текущему сайту
                $this->ensureFormSiteBinding($formId, $this->siteId);
            } else {
                $error = $GLOBALS["APPLICATION"]->GetException();
                $this->log("Error creating form: " . ($error ? $error->GetString() : "Unknown error"));
            }
        }

        // Сохраняем новые алиасы форм
        if (!empty($newAliases)) {
            if (!$this->updateFormAliases($newAliases)) {
                $this->log("Warning: Failed to update form aliases file. Please update it manually.");
            }
        } else {
            $this->log("No new forms were created. Form aliases file was not updated.");
        }
    }

    private function ensureFormSiteBinding($formId, $siteId)
    {
        $formSites = \CForm::GetSiteArray($formId);
        if (!in_array($siteId, $formSites)) {
            $formSites[] = $siteId;

            // Обновляем привязку формы к сайтам
            $form = new \CForm;
            $form->Set(
                [
                    'arSITE' => $formSites,
                ],
                $formId
            );

            $this->log("Added site binding for form ID: {$formId}, Site ID: {$siteId}");
        }
    }

    private function updateFormMenu($formId, $menuData)
    {
        if (!is_array($menuData)) {
            return;
        }

        $connection = Application::getConnection();
        foreach ($menuData as $lid => $menuText) {
            // Проверяем, существует ли уже запись для этой формы и языка
            $existingMenu = $connection->query(
                "SELECT ID FROM b_form_menu WHERE FORM_ID = {$formId} AND LID = '{$lid}'"
            )->fetch();

            if ($existingMenu) {
                // Обновляем существующую запись
                $connection->query(
                    "UPDATE b_form_menu SET MENU = '" . $connection->getSqlHelper()->forSql(
                        $menuText
                    ) . "' WHERE ID = " . $existingMenu['ID']
                );
            } else {
                // Добавляем новую запись
                $connection->query(
                    "INSERT INTO b_form_menu (FORM_ID, LID, MENU) VALUES ({$formId}, '{$lid}', '" . $connection->getSqlHelper(
                    )->forSql($menuText) . "')"
                );
            }
        }
        $this->log("Updated menu for form ID: {$formId}");
    }

    private function importFormQuestions($formId, $questions)
    {
        foreach ($questions as $questionData) {
            $questionFields = [
                'FORM_ID' => $formId,
                'ACTIVE' => $questionData['ACTIVE'],
                'TITLE' => $questionData['TITLE'],
                'TITLE_TYPE' => $questionData['TITLE_TYPE'],
                'SID' => $questionData['SID'],
                'C_SORT' => $questionData['C_SORT'],
                'ADDITIONAL' => $questionData['ADDITIONAL'],
                'REQUIRED' => $questionData['REQUIRED'],
                'IN_FILTER' => $questionData['IN_FILTER'],
                'IN_RESULTS_TABLE' => $questionData['IN_RESULTS_TABLE'],
                'IN_EXCEL_TABLE' => $questionData['IN_EXCEL_TABLE'],
                'FIELD_TYPE' => $questionData['FIELD_TYPE'],
                'COMMENTS' => $questionData['COMMENTS'],
                'FILTER_TITLE' => $questionData['FILTER_TITLE'],
                'RESULTS_TABLE_TITLE' => $questionData['RESULTS_TABLE_TITLE'],
            ];

            $questionId = \CFormField::Set($questionFields);

            if ($questionId > 0) {
                $this->log("Created question: " . $questionData['TITLE'] . " (ID: $questionId)");

                if (isset($questionData['ANSWERS'])) {
                    $this->importFormAnswers($questionId, $questionData['ANSWERS']);
                }
            } else {
                $error = $GLOBALS["APPLICATION"]->GetException();
                $this->log("Error creating question: " . ($error ? $error->GetString() : "Unknown error"));
            }
        }
    }

    private function importFormAnswers($questionId, $answers)
    {
        foreach ($answers as $answerData) {
            $answerFields = [
                'FIELD_ID' => $questionId,
                'MESSAGE' => $answerData['MESSAGE'],
                'VALUE' => $answerData['VALUE'],
                'SORT' => $answerData['C_SORT'],
                'ACTIVE' => $answerData['ACTIVE'],
                'FIELD_TYPE' => $answerData['FIELD_TYPE'],
                'FIELD_WIDTH' => $answerData['FIELD_WIDTH'],
                'FIELD_HEIGHT' => $answerData['FIELD_HEIGHT'],
                'FIELD_PARAM' => $answerData['FIELD_PARAM'],
            ];

            $answerId = \CFormAnswer::Set($answerFields);

            if ($answerId > 0) {
                $this->log("Created answer for question ID $questionId (Answer ID: $answerId)");
            } else {
                $error = $GLOBALS["APPLICATION"]->GetException();
                $this->log("Error creating answer: " . ($error ? $error->GetString() : "Unknown error"));
            }
        }
    }

    private function importFormStatuses($formId, $statuses)
    {
        foreach ($statuses as $statusData) {
            $statusData['FORM_ID'] = $formId;

            // Добавляем права доступа для всех пользователей
            // TODO: реализовать права доступа для конкретных пользователей
            $statusData['arPERMISSION_VIEW'] = [2];  // 2 - это ID группы "Все пользователи"
            $statusData['arPERMISSION_MOVE'] = [2];  // Право на перевод (изменение) статуса
            $statusData['arPERMISSION_EDIT'] = [2];  // Право на редактирование

            $statusId = \CFormStatus::Set($statusData);

            if ($statusId > 0) {
                $this->log("Created status: " . $statusData['TITLE'] . " (ID: $statusId)");
            } else {
                $error = $GLOBALS["APPLICATION"]->GetException();
                $this->log("Error creating status: " . ($error ? $error->GetString() : "Unknown error"));
            }
        }
    }

    private function generateUniqueFormSID($baseSID)
    {
        $i = 0;
        $newSID = $baseSID;
        while (\CForm::GetBySID($newSID)->Fetch()) {
            $i++;
            $newSID = $baseSID . '_' . $i;
        }
        return $newSID;
    }

    private function setDeferredLinks()
    {
        foreach ($this->deferredLinks as $link) {
            if ($link['TYPE'] !== 'CATALOG_OFFERS') {
                $linkedIblockId = $this->getIblockIdByCodeAndType(
                    'acroweb_' . $link['LINK_IBLOCK_CODE'] . '_' . $this->siteId,
                    'acroweb_' . $link['LINK_IBLOCK_TYPE'] . '_' . $this->siteId
                );

                if ($linkedIblockId) {
                    $ibp = new \CIBlockProperty;
                    $ibp->Update($link['PROPERTY_ID'], ['LINK_IBLOCK_ID' => $linkedIblockId]);
                    $this->log("Updated link for property ID {$link['PROPERTY_ID']} to iblock ID {$linkedIblockId}");
                } else {
                    $this->log("Failed to find linked iblock for property ID {$link['PROPERTY_ID']}");
                }
            }
        }
    }

    private function getIblockIdByCodeAndType($code, $type)
    {
        $iblock = \CIBlock::GetList([], ['CODE' => $code, 'TYPE' => $type])->Fetch();
        return $iblock ? $iblock['ID'] : false;
    }

    private function importIblockTypes()
    {
        $typesFile = $this->xmlPath . 'iblock_types.json';
        if (!file_exists($typesFile)) {
            $this->log("Iblock types file not found: {$typesFile}");
            return;
        }

        $typesData = json_decode(file_get_contents($typesFile), true);
        foreach ($typesData as $typeData) {
            if (Loader::includeModule('form') && $typeData['ID'] === 'feedback') {
                continue;
            }

            $newTypeId = 'acroweb_' . $typeData['ID'] . '_' . $this->siteId;
            $fields = [
                'ID' => $newTypeId,
                'SECTIONS' => $typeData['SECTIONS'],
                'EDIT_FILE_BEFORE' => $typeData['EDIT_FILE_BEFORE'],
                'EDIT_FILE_AFTER' => $typeData['EDIT_FILE_AFTER'],
                'IN_RSS' => $typeData['IN_RSS'],
                'SORT' => $typeData['SORT'],
                'LANG' => [
                    LANGUAGE_ID => [
                        'NAME' => $typeData['NAME'],
                        'SECTION_NAME' => $typeData['SECTION_NAME'],
                        'ELEMENT_NAME' => $typeData['ELEMENT_NAME'],
                    ],
                ],
            ];

            $obBlocktype = new \CIBlockType;
            $res = $obBlocktype->Add($fields);
            if (!$res) {
                $this->log("Error creating iblock type: " . $obBlocktype->LAST_ERROR);
            } else {
                $this->log("Successfully created iblock type: " . $newTypeId);
            }
        }
    }

    private function importIblocks()
    {
        $newAliases = [];
        $catalogIblocks = [];
        $files = glob($this->xmlPath . '*.json');
        foreach ($files as $file) {
            if (strpos($file, '_properties.json') !== false ||
                strpos($file, '_elements.json') !== false ||
                strpos($file, '_sections.json') !== false ||
                basename($file) === 'iblock_types.json' ||
                basename($file) === 'web_forms.json' ||
                basename($file) === 'form_settings.json') {
                continue;
            }

            if (Loader::includeModule('form')
                && in_array(basename($file), [
                    'corp1callback.json',
                    'corp1contactus.json',
                    'corp1services.json',
                    'corp1vacancy.json',
                ])) {
                continue;
            }

            if (!Loader::includeModule('catalog')
                &&  (strpos($file, 'catalogShop') !== false
                    || strpos($file, 'clothesOffers') !== false )
            ) {
                continue;
            }

            if (Loader::includeModule('catalog')
                && strpos($file, 'catalogStart') !== false
            ) {
                continue;
            }

            if (!file_exists($file)) {
                $this->log("Iblock file not found: {$file}");
                continue;
            }

            $iblockData = json_decode(file_get_contents($file), true);
            if (empty($iblockData['NAME']) || empty($iblockData['IBLOCK_TYPE_ID'])) {
                $this->log("Error: Missing required fields for iblock in file: {$file}");
                continue;
            }

            $newIblockCode = 'acroweb_' . $iblockData['CODE'] . '_' . $this->siteId;
            $fields = [
                'SITE_ID' => $this->siteId,
                'CODE' => $newIblockCode,
                'API_CODE' => 'acroweb' . $iblockData['API_CODE'] . $this->siteId,
                'IBLOCK_TYPE_ID' => 'acroweb_' . $iblockData['IBLOCK_TYPE_ID'] . '_' . $this->siteId,
                'NAME' => $iblockData['NAME'],
                'ACTIVE' => $iblockData['ACTIVE'],
                'SORT' => $iblockData['SORT'],
                'LIST_PAGE_URL' => $iblockData['LIST_PAGE_URL'],
                'DETAIL_PAGE_URL' => $iblockData['DETAIL_PAGE_URL'],
                'SECTION_PAGE_URL' => $iblockData['SECTION_PAGE_URL'],
                'CANONICAL_PAGE_URL' => $iblockData['CANONICAL_PAGE_URL'],
                'PICTURE' => $this->importFile($iblockData['PICTURE']),
                'DESCRIPTION' => $iblockData['DESCRIPTION'],
                'DESCRIPTION_TYPE' => $iblockData['DESCRIPTION_TYPE'],
                'RSS_TTL' => $iblockData['RSS_TTL'],
                'RSS_ACTIVE' => $iblockData['RSS_ACTIVE'],
                'RSS_FILE_ACTIVE' => $iblockData['RSS_FILE_ACTIVE'],
                'RSS_FILE_LIMIT' => $iblockData['RSS_FILE_LIMIT'],
                'RSS_FILE_DAYS' => $iblockData['RSS_FILE_DAYS'],
                'RSS_YANDEX_ACTIVE' => $iblockData['RSS_YANDEX_ACTIVE'],
                'XML_ID' => $iblockData['XML_ID'],
                'INDEX_ELEMENT' => $iblockData['INDEX_ELEMENT'],
                'INDEX_SECTION' => $iblockData['INDEX_SECTION'],
                'WORKFLOW' => $iblockData['WORKFLOW'],
                'BIZPROC' => $iblockData['BIZPROC'],
                'SECTION_CHOOSER' => $iblockData['SECTION_CHOOSER'],
                'LIST_MODE' => $iblockData['LIST_MODE'],
                'RIGHTS_MODE' => $iblockData['RIGHTS_MODE'],
                'SECTION_PROPERTY' => $iblockData['SECTION_PROPERTY'],
                'PROPERTY_INDEX' => $iblockData['PROPERTY_INDEX'],
                'VERSION' => $iblockData['VERSION'],
                'LAST_CONV_ELEMENT' => $iblockData['LAST_CONV_ELEMENT'],
                'SOCNET_GROUP_ID' => $iblockData['SOCNET_GROUP_ID'],
                'EDIT_FILE_BEFORE' => $iblockData['EDIT_FILE_BEFORE'],
                'EDIT_FILE_AFTER' => $iblockData['EDIT_FILE_AFTER'],
                'SECTIONS_NAME' => $iblockData['SECTIONS_NAME'],
                'SECTION_NAME' => $iblockData['SECTION_NAME'],
                'ELEMENTS_NAME' => $iblockData['ELEMENTS_NAME'],
                'ELEMENT_NAME' => $iblockData['ELEMENT_NAME'],
                'ELEMENT_ADD' => $iblockData['ELEMENT_ADD'],
                'ELEMENT_EDIT' => $iblockData['ELEMENT_EDIT'],
                'ELEMENT_DELETE' => $iblockData['ELEMENT_DELETE'],
                'SECTION_ADD' => $iblockData['SECTION_ADD'],
                'SECTION_EDIT' => $iblockData['SECTION_EDIT'],
                'SECTION_DELETE' => $iblockData['SECTION_DELETE'],
                'GROUP_PERMISSIONS' => $iblockData['GROUP_PERMISSIONS'],
            ];

            // Добавляем IPROPERTY_TEMPLATES в поля инфоблока
            if (!empty($iblockData['IPROPERTY_TEMPLATES'])) {
                $fields['IPROPERTY_TEMPLATES'] = $iblockData['IPROPERTY_TEMPLATES'];
            }

            $iblock = new \CIBlock;
            $newIblockId = $iblock->Add($fields);

            if ($newIblockId) {
                $this->iblockIdMap[$iblockData['ID']] = $newIblockId;
                if (!empty($iblockData['FIELDS'])) {
                    \CIBlock::SetFields($newIblockId, $iblockData['FIELDS']);
                    $this->log("Successfully imported form settings for iblock ID: {$newIblockId}");
                }

                if (isset($iblockData['IS_CATALOG']) && $iblockData['IS_CATALOG'] === 'Y') {
                    $catalogIblocks[$newIblockId] = [
                        'OFFERS_IBLOCK_ID' => $iblockData['OFFERS_IBLOCK_ID'] ?? null,
                        'OFFERS_PROPERTY_ID' => $iblockData['OFFERS_PROPERTY_ID'] ?? null,
                        'IBLOCK_CODE' => $iblockData['CODE'],
                        'IBLOCK_TYPE' => $iblockData['IBLOCK_TYPE_ID'],
                    ];
                }

                // Применяем настройки SEO (IPROPERTY_TEMPLATES)
                if (!empty($iblockData['IPROPERTY_TEMPLATES'])) {
                    $ipropTemplates = $iblockData['IPROPERTY_TEMPLATES'];
                    $ibFields = ['IPROPERTY_TEMPLATES' => $ipropTemplates];
                    $iblock = new \CIBlock;
                    if ($iblock->Update($newIblockId, $ibFields)) {
                        $this->log("Successfully imported SEO templates for iblock ID: {$newIblockId}");
                    } else {
                        $this->log(
                            "Error importing SEO templates for iblock ID: {$newIblockId}: " . $iblock->LAST_ERROR
                        );
                    }
                }

                // Импорт пользовательских полей
                if (!empty($iblockData['USER_FIELDS'])) {
                    $this->importUserFields($newIblockId, $iblockData['USER_FIELDS']);
                }

                // Импорт свойств инфоблока
                $this->importIblockProperties($newIblockId, $iblockData['CODE']);

                // Установка прав доступа
                if ($iblockData['RIGHTS_MODE'] === 'E') {
                    // Расширенный режим прав
                    $this->setExtendedRights($newIblockId, $iblockData['RIGHTS']);
                } else {
                    // Простой режим прав
                    \CIBlock::SetPermission($newIblockId, $iblockData['GROUP_PERMISSIONS']);
                }

                $oldCode = $iblockData['CODE'];
//                $newAliases[$oldCode] = 'acroweb' . $iblockData['API_CODE'] . $this->siteId;
                $newAliases[$oldCode] = 'acroweb' . $iblockData['API_CODE'];

                \Bitrix\Main\Application::getInstance()->getTaggedCache()->clearByTag("iblock_id_new");
                $this->log("Successfully created iblock: " . $newIblockCode);
            } else {
                $this->log("Error creating iblock: " . $iblock->LAST_ERROR);
            }
        }

        if (Loader::includeModule('catalog')) {
            // Второй проход: настраиваем связи каталогов и торговых предложений
            foreach ($catalogIblocks as $iblockId => $catalogData) {
                $this->setupCatalogLinks($iblockId, $catalogData);
            }
        }

        if (!empty($newAliases)) {
            if (!$this->updateIblockAliases($newAliases)) {
                $this->log("Warning: Failed to update iblock aliases file. Please update it manually.");
            }
        } else {
            $this->log("No new iblocks were created. Iblock aliases file was not updated.");
        }
    }

    /**
     * Получает новый ID инфоблока по старому
     *
     * @param int $oldIblockId Старый ID инфоблока
     * @return int|null Новый ID инфоблока или null, если соответствие не найдено
     */
    private function getNewIblockId(int $oldIblockId): ?int
    {
        return $this->iblockIdMap[$oldIblockId] ?? null;
    }

    /**
     * Получает новый ID свойства инфоблока по старому
     *
     * @param int $oldPropertyId Старый ID свойства
     * @return int|null Новый ID свойства или null, если соответствие не найдено
     */
    private function getNewPropertyId(int $oldPropertyId): ?int
    {
        return $this->propertyIdMap[$oldPropertyId] ?? null;
    }

    private function setupCatalogLinks($iblockId, $catalogData)
    {
        $this->log("Setting up catalog links for iblock ID: {$iblockId}");
        $this->log("Catalog data: " . print_r($catalogData, true));

        $newOffersIblockId = null;
        $newOffersPropertyId = null;

        if ($catalogData['OFFERS_IBLOCK_ID']) {
            $newOffersIblockId = $this->getNewIblockId($catalogData['OFFERS_IBLOCK_ID']);
            $this->log("Found offers iblock ID: {$newOffersIblockId}");
        }

        if ($newOffersIblockId && $catalogData['OFFERS_PROPERTY_ID']) {
            $newOffersPropertyId = $this->getNewPropertyId($catalogData['OFFERS_PROPERTY_ID']);
            $this->log("Found offers property ID: {$newOffersPropertyId}");
        }

        if (!$newOffersIblockId || !$newOffersPropertyId) {
            $this->log("Error: Unable to find offers iblock or property for catalog iblock ID {$iblockId}");
            return;
        }

        // Устанавливаем инфоблок как каталог
        $result = \Bitrix\Catalog\CatalogIblockTable::add([
            'IBLOCK_ID' => $iblockId,
            'PRODUCT_IBLOCK_ID' => 0,
            'SKU_PROPERTY_ID' => 0,
        ]);

        if ($result->isSuccess()) {
            $this->log("Successfully set iblock ID {$iblockId} as catalog");
        } else {
            $this->log("Error setting iblock ID {$iblockId} as catalog: " . implode(', ', $result->getErrorMessages()));
        }

        // Устанавливаем инфоблок предложений
        $result = \Bitrix\Catalog\CatalogIblockTable::add([
            'IBLOCK_ID' => $newOffersIblockId,
            'PRODUCT_IBLOCK_ID' => $iblockId,
            'SKU_PROPERTY_ID' => $newOffersPropertyId,
        ]);

        if ($result->isSuccess()) {
            $this->log(
                "Successfully set iblock ID {$newOffersIblockId} as offers for catalog ID {$iblockId} with property ID {$newOffersPropertyId}"
            );
        } else {
            $this->log(
                "Error setting iblock ID {$newOffersIblockId} as offers: " . implode(', ', $result->getErrorMessages())
            );
        }

        // Проверяем, что связи установлены корректно
        $catalogIblock = \Bitrix\Catalog\CatalogIblockTable::getList([
            'filter' => ['IBLOCK_ID' => $iblockId],
        ])->fetch();
        $offersIblock = \Bitrix\Catalog\CatalogIblockTable::getList([
            'filter' => ['IBLOCK_ID' => $newOffersIblockId],
        ])->fetch();

        $this->log("Catalog iblock after setup: " . print_r($catalogIblock, true));
        $this->log("Offers iblock after setup: " . print_r($offersIblock, true));
    }

    private function updateIblockAliases($newAliases)
    {
        $aliasesFile = __DIR__ . '/../config/iblock_aliases.php';
        $content = "<?php\nreturn [\n";
        foreach ($newAliases as $oldCode => $newCode) {
            $content .= "    '$oldCode' => '$newCode',\n";
        }
        $content .= "    // Add more aliases here as needed\n];\n";

        if (!is_writable($aliasesFile)) {
            $this->log("Error: Unable to write to iblock aliases file. Check permissions.");
            return false;
        }

        if (file_put_contents($aliasesFile, $content) === false) {
            $this->log("Error: Failed to update iblock aliases file.");
            return false;
        }

        $this->log("Successfully updated iblock aliases file");
        return true;
    }

    private function updateFormAliases($newAliases)
    {
        $aliasesFile = __DIR__ . '/../config/form_aliases.php';
        $content = "<?php\nreturn [\n";
        foreach ($newAliases as $oldCode => $newCode) {
            $content .= "    '$oldCode' => '$newCode',\n";
        }
        $content .= "    // Add more aliases here as needed\n];\n";

        if (!is_writable($aliasesFile)) {
            $this->log("Error: Unable to write to form aliases file. Check permissions.");
            return false;
        }

        if (file_put_contents($aliasesFile, $content) === false) {
            $this->log("Error: Failed to update form aliases file.");
            return false;
        }

        $this->log("Successfully updated form aliases file");
        return true;
    }

    private function importFormSettings()
    {
        $formSettingsFile = $this->xmlPath . 'form_settings.json';
        if (!file_exists($formSettingsFile)) {
            $this->log("Form settings file not found: {$formSettingsFile}");
            return;
        }

        $formSettings = json_decode(file_get_contents($formSettingsFile), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->log("Error decoding form settings JSON: " . json_last_error_msg());
            return;
        }

        $connection = Application::getConnection();

        foreach ($formSettings as $oldIblockId => $settings) {
            // Найдем новый ID инфоблока по коду
            $newIblockCode = 'acroweb_' . $settings['IBLOCK_CODE'] . '_' . $this->siteId;
            $newIblock = \CIBlock::GetList([], ['CODE' => $newIblockCode])->Fetch();
            if (!$newIblock) {
                $this->log("New iblock not found for code: {$newIblockCode}");
                continue;
            }

            $newIblockId = $newIblock['ID'];

            $newPropertyIds = [];
            $properties = \CIBlockProperty::GetList([], ['IBLOCK_ID' => $newIblockId]);
            while ($prop = $properties->Fetch()) {
                $newPropertyIds[$prop['CODE']] = $prop['ID'];
            }

            if (isset($settings['tabs'])) {
                $tabsString = $settings['tabs'];
                $tabsArray = explode(',', $tabsString);

                foreach ($tabsArray as &$item) {
                    if (preg_match('/^--PROPERTY_(\d+)--#--(.*)$/', $item, $matches)) {
                        $oldPropId = $matches[1];
                        $propName = $matches[2];

                        // Найдем код свойства по его старому ID
                        $propCode = array_search($oldPropId, $settings['PROPERTY_IDS']);

                        if ($propCode !== false && isset($newPropertyIds[$propCode])) {
                            $newPropId = $newPropertyIds[$propCode];
                            $item = "--PROPERTY_{$newPropId}--#--{$propName}";
                        }
                    }
                }
                $settings['tabs'] = implode(',', $tabsArray);
            }

            unset($settings['IBLOCK_NAME'], $settings['IBLOCK_CODE'], $settings['COMMON'], $settings['PROPERTY_IDS']);

            $serializedSettings = serialize($settings);

            $existingSettings = $connection->query(
                "SELECT ID FROM b_user_option WHERE CATEGORY = 'form' AND NAME = 'form_element_{$newIblockId}'"
            )->fetch();

            if ($existingSettings) {
                $connection->query(
                    "UPDATE b_user_option SET VALUE = '" . $connection->getSqlHelper()->forSql(
                        $serializedSettings
                    ) . "', COMMON = 'Y' WHERE ID = " . $existingSettings['ID']
                );
            } else {
                $sql = "INSERT INTO b_user_option (USER_ID, CATEGORY, NAME, VALUE, COMMON) VALUES (0, 'form', 'form_element_{$newIblockId}', '" . $connection->getSqlHelper(
                    )->forSql($serializedSettings) . "', 'Y')";
                $connection->query($sql);
            }

            $GLOBALS["CACHE_MANAGER"]->cleanDir("user_option");
        }
    }

    private function importIblockSections()
    {
        $files = glob($this->xmlPath . '*_sections.json');
        foreach ($files as $file) {
            if (!Loader::includeModule('catalog')
                &&  (strpos($file, 'catalogShop') !== false
                    || strpos($file, 'clothesOffers') !== false )
            ) {
                continue;
            }

            if (Loader::includeModule('catalog')
                && strpos($file, 'catalogStart') !== false
            ) {
                continue;
            }

            $sectionsData = json_decode(file_get_contents($file), true);
            $iblockCode = str_replace('_sections.json', '', basename($file));
            $newIblockCode = 'acroweb_' . $iblockCode . '_' . $this->siteId;
            $iblockId = \CIBlock::GetList([], ['CODE' => $newIblockCode])->Fetch()['ID'];

            if (!$iblockId) {
                $this->log("Iblock not found for code: {$newIblockCode}");
                continue;
            }

            $sectionMap = [];
            $bs = new \CIBlockSection;

            // Получаем список пользовательских полей для разделов инфоблока
            $userFields = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('IBLOCK_' . $iblockId . '_SECTION');


            foreach ($sectionsData as $sectionData) {
                $fields = [
                    'IBLOCK_ID' => $iblockId,
                    'NAME' => $sectionData['NAME'],
                    'CODE' => $sectionData['CODE'],
                    'SORT' => $sectionData['SORT'],
                    'DESCRIPTION' => $sectionData['DESCRIPTION'],
                    'DESCRIPTION_TYPE' => $sectionData['DESCRIPTION_TYPE'],
                    'PICTURE' => $this->importFile($sectionData['PICTURE']),
                    'DETAIL_PICTURE' => $this->importFile($sectionData['DETAIL_PICTURE']),
                    'XML_ID' => $sectionData['XML_ID'],
                ];

                if ($sectionData['IBLOCK_SECTION_ID'] && isset($sectionMap[$sectionData['IBLOCK_SECTION_ID']])) {
                    $fields['IBLOCK_SECTION_ID'] = $sectionMap[$sectionData['IBLOCK_SECTION_ID']];
                }

                // Обработка пользовательских полей
                foreach ($userFields as $ufCode => $ufData) {
                    if (isset($sectionData[$ufCode])) {
                        $value = $sectionData[$ufCode];

                        // Обработка поля типа "Да/Нет"
                        if ($ufData['USER_TYPE_ID'] === 'boolean') {
                            $value = ($value == '1' || $value === true) ? 1 : 0;
                        }

                        $fields[$ufCode] = $value;
                    }
                }

                $newSectionId = $bs->Add($fields);
                if ($newSectionId) {
                    $sectionMap[$sectionData['ID']] = $newSectionId;
                    $this->log("Successfully created section: ID={$newSectionId}, NAME={$sectionData['NAME']}");
                } else {
                    $this->log("Error creating section: " . $bs->LAST_ERROR);
                }
            }

            // Сохраняем карту соответствия старых и новых ID разделов
            $this->sectionMaps[$iblockId] = $sectionMap;
        }
    }

    private function setExtendedRights($iblockId, $rights)
    {
        if (empty($rights)) {
            return;
        }

        $obRights = new \CIBlockRights($iblockId);
        $obRights->SetRights($rights);
    }

    private function setFormSettings($iblockId, $formSettings)
    {
        if (!empty($formSettings)) {
            \CIBlock::SetFields($iblockId, $formSettings);
            $this->log("Successfully imported form settings for iblock ID: {$iblockId}");
        } else {
            $this->log("No form settings to import for iblock ID: {$iblockId}");
        }
    }

    private function importIblockElements()
    {
        $files = glob($this->xmlPath . '*_elements.json');
        $hasCatalogModule = Loader::includeModule('catalog');

        foreach ($files as $file) {
            $elementsData = json_decode(file_get_contents($file), true);
            $iblockCode = str_replace('_elements.json', '', basename($file));
            $newIblockCode = 'acroweb_' . $iblockCode . '_' . $this->siteId;
            $iblockId = \CIBlock::GetList([], ['CODE' => $newIblockCode])->Fetch()['ID'];

            $properties = \CIBlockProperty::GetList([], ["IBLOCK_ID" => $iblockId]);
            $propEnums = [];
            while ($prop_fields = $properties->GetNext()) {
                if ($prop_fields["PROPERTY_TYPE"] == "L") {
                    $property_enums = \CIBlockPropertyEnum::GetList([],
                        ["IBLOCK_ID" => $iblockId, "CODE" => $prop_fields["CODE"]]);
                    while ($enum_fields = $property_enums->GetNext()) {
                        $propEnums[$prop_fields["CODE"]][$enum_fields["XML_ID"]] = $enum_fields["ID"];
                    }
                }
            }

            foreach ($elementsData as $elementData) {
                $fields = [
                    'IBLOCK_ID' => $iblockId,
                    'NAME' => html_entity_decode($elementData['NAME']),
                    'CODE' => $elementData['CODE'],
                    'ACTIVE' => $elementData['ACTIVE'] ?? 'Y',
                    'SORT' => $elementData['SORT'] ?? 500,
                    'PREVIEW_TEXT' => $elementData['PREVIEW_TEXT'] ?? '',
                    'PREVIEW_TEXT_TYPE' => $elementData['PREVIEW_TEXT_TYPE'] ?? 'text',
                    'DETAIL_TEXT' => $elementData['DETAIL_TEXT'] ?? '',
                    'DETAIL_TEXT_TYPE' => $elementData['DETAIL_TEXT_TYPE'] ?? 'html',
                    'XML_ID' => $elementData['XML_ID'] ?? '',
                ];

                // Добавляем привязку к разделу
                if (isset($elementData['IBLOCK_SECTION_ID']) && isset($this->sectionMaps[$iblockId][$elementData['IBLOCK_SECTION_ID']])) {
                    $fields['IBLOCK_SECTION_ID'] = $this->sectionMaps[$iblockId][$elementData['IBLOCK_SECTION_ID']];
                }

                // Обработка PREVIEW_PICTURE
                if (isset($elementData['PREVIEW_PICTURE'])) {
                    $fields['PREVIEW_PICTURE'] = $this->importFile($elementData['PREVIEW_PICTURE']);
                }

                // Обработка DETAIL_PICTURE
                if (isset($elementData['DETAIL_PICTURE'])) {
                    $fields['DETAIL_PICTURE'] = $this->importFile($elementData['DETAIL_PICTURE']);
                }

                $properties = [];
                if (isset($elementData['PROPERTIES']) && is_array($elementData['PROPERTIES'])) {
                    foreach ($elementData['PROPERTIES'] as $code => $prop) {
                        if ($prop['PROPERTY_TYPE'] === 'L') {
                            if (isset($propEnums[$code])) {
                                if (is_array($prop['VALUE'])) {
                                    // Для множественных значений
                                    $properties[$code] = [];
                                    foreach ($prop['VALUE'] as $value) {
                                        $enumId = $propEnums[$code][$value] ?? null;

                                        if ($enumId !== null) {
                                            $properties[$code]['VALUE'][] = $enumId;
                                        }
                                    }
                                } else {
                                    $enumId = $propEnums[$code][$prop['VALUE']] ?? null;
                                    $properties[$code] = $enumId;
                                }
                            } else {
                                $properties[$code] = $prop['VALUE'];
                            }
                        } elseif ($prop['PROPERTY_TYPE'] === 'F') {
                            if ($prop['VALUE']) {
                                $properties[$code] = $this->importFile($prop['VALUE']);
                            }
                        } elseif ($prop['PROPERTY_TYPE'] === 'S' && $prop['USER_TYPE'] === 'HTML') {
                            $properties[$code] = [
                                'VALUE' => [
                                    'TEXT' => html_entity_decode($prop['VALUE']['TEXT']),
                                    'TYPE' => $prop['VALUE']['TYPE'],
                                ],
                            ];
                        } else {
                            if (is_array($prop['VALUE'])) {
                                $properties[$code] = [];
                                foreach ($prop['VALUE'] as $key => $value) {
                                    $properties[$code][] = [
                                        'VALUE' => $value,
                                        'DESCRIPTION' => $prop['DESCRIPTION'][$key] ?? '',
                                    ];
                                }
                            } else {
                                $properties[$code] = [
                                    'VALUE' => $prop['VALUE'],
                                    'DESCRIPTION' => $prop['DESCRIPTION'] ?? '',
                                ];
                            }
                        }
                    }
                }

                $element = new \CIBlockElement;
                $newElementId = $element->Add($fields);

                if ($newElementId) {
                    \CIBlockElement::SetPropertyValuesEx($newElementId, $iblockId, $properties);
                    $this->idMap[$iblockId][$elementData['ID']] = $newElementId;
                    $this->log("Successfully created element: ID={$newElementId}, NAME={$elementData['NAME']}");

                    // Импорт данных каталога, если они есть
                    if ($hasCatalogModule && isset($elementData['CATALOG'])) {
                        $this->importCatalogData($newElementId, $elementData['CATALOG']);
                    }
                } else {
                    $this->log("Error creating element: " . $element->LAST_ERROR);
                }
            }
        }

        $this->updateElementLinks();
    }

    private function importCatalogData($elementId, $catalogData)
    {
        $fields = [
            'ID' => $elementId,
            'QUANTITY' => $catalogData['QUANTITY'] ?? 0,
            'WEIGHT' => $catalogData['WEIGHT'] ?? 0,
            'WIDTH' => $catalogData['WIDTH'] ?? 0,
            'LENGTH' => $catalogData['LENGTH'] ?? 0,
            'HEIGHT' => $catalogData['HEIGHT'] ?? 0,
            'VAT_ID' => $catalogData['VAT_ID'] ?? null,
            'VAT_INCLUDED' => $catalogData['VAT_INCLUDED'] ?? 'N',
            'PURCHASING_PRICE' => $catalogData['PURCHASING_PRICE'] ?? null,
            'PURCHASING_CURRENCY' => $catalogData['PURCHASING_CURRENCY'] ?? null,
        ];

        $result = \CCatalogProduct::Add($fields);

        if ($result) {
            $this->log("Successfully added catalog data for element ID: {$elementId}");

            // Импорт цен
            if (isset($catalogData['PRICES']) && is_array($catalogData['PRICES'])) {
                foreach ($catalogData['PRICES'] as $price) {
                    $priceFields = [
                        'PRODUCT_ID' => $elementId,
                        'CATALOG_GROUP_ID' => $price['CATALOG_GROUP_ID'],
                        'PRICE' => $price['PRICE'],
                        'CURRENCY' => $price['CURRENCY'],
                    ];
                    $priceResult = \CPrice::Add($priceFields);
                    if ($priceResult) {
                        $this->log("Added price for element ID: {$elementId}, Price type: {$price['CATALOG_GROUP_ID']}");
                    } else {
                        $this->log("Error adding price for element ID: {$elementId}, Price type: {$price['CATALOG_GROUP_ID']}");
                    }
                }
            }
        } else {
            $this->log("Error adding catalog data for element ID: {$elementId}");
        }
    }

    private function updateElementLinks()
    {
        foreach ($this->idMap as $iblockId => $idMap) {
            $properties = \CIBlockProperty::GetList([], ["IBLOCK_ID" => $iblockId, "PROPERTY_TYPE" => "E"]);
            $elementProperties = [];
            while ($prop = $properties->Fetch()) {
                $elementProperties[$prop['ID']] = $prop['CODE'];
            }

            foreach ($idMap as $oldId => $newId) {
                $props = [];
                $dbProps = \CIBlockElement::GetProperty($iblockId, $newId, "sort", "asc", ["PROPERTY_TYPE" => "E"]);
                while ($prop = $dbProps->Fetch()) {
                    if (isset($elementProperties[$prop['ID']])) {
                        $propCode = $elementProperties[$prop['ID']];
                        if ($prop['MULTIPLE'] == 'Y') {
                            if (!isset($props[$propCode])) {
                                $props[$propCode] = [];
                            }
                            if (!empty($prop['VALUE'])) {
                                $newValue = $this->getNewElementId($prop['VALUE'], $prop['LINK_IBLOCK_ID']);
                                if ($newValue) {
                                    $props[$propCode][] = $newValue;
                                }
                            }
                        } else {
                            if (!empty($prop['VALUE'])) {
                                $newValue = $this->getNewElementId($prop['VALUE'], $prop['LINK_IBLOCK_ID']);
                                if ($newValue) {
                                    $props[$propCode] = $newValue;
                                }
                            }
                        }
                    }
                }

                if (!empty($props)) {
                    \CIBlockElement::SetPropertyValuesEx($newId, $iblockId, $props);
                    $this->log("Updated links for element ID: {$newId} in iblock ID: {$iblockId}");
                }
            }
        }
    }

    private function getNewElementId($oldId, $linkedIblockId)
    {
        if (isset($this->idMap[$linkedIblockId][$oldId])) {
            return $this->idMap[$linkedIblockId][$oldId];
        }
        return false;
    }

    private function importFile($fileData)
    {
        if (empty($fileData)) {
            return false;
        }

        if (is_array($fileData)) {
            if (isset($fileData['ID'])) {
                // Это одиночный файл
                return $this->processSingleFile($fileData);
            } elseif (isset($fileData[0]) && is_array($fileData[0])) {
                // Это массив файлов (множественное свойство)
                return array_map([$this, 'processSingleFile'], $fileData);
            } elseif (isset($fileData[0]) && is_string($fileData[0])) {
                // Это массив строк (путей к файлам)
                return array_map([$this, 'processFilePath'], $fileData);
            } else {
                $this->log("Неподдерживаемый формат массива файла: " . print_r($fileData, true));
                return false;
            }
        } elseif (is_string($fileData)) {
            // Это строка (путь к файлу)
            return $this->processFilePath($fileData);
        } else {
            $this->log("Неподдерживаемый тип данных файла: " . gettype($fileData));
            return false;
        }
    }

    private function processSingleFile($fileData)
    {
        $sourcePath = $this->imagesPath . $fileData['SUBDIR'] . '/' . $fileData['FILE_NAME'];
        if (file_exists($sourcePath)) {
            return \CFile::MakeFileArray($sourcePath);
        } else {
            $this->log("Файл не найден: {$sourcePath}");
            return false;
        }
    }

    private function processFilePath($filePath)
    {
        $sourcePath = $this->imagesPath . $filePath;
        if (file_exists($sourcePath)) {
            return \CFile::MakeFileArray($sourcePath);
        } else {
            $this->log("Файл не найден: {$sourcePath}");
            return false;
        }
    }

    private function importUserFields($iblockId, $userFields)
    {
        $entity_id = 'IBLOCK_' . $iblockId . '_SECTION';

        foreach ($userFields as $ufName => $ufData) {
            $arFields = [
                'ENTITY_ID' => $entity_id,
                'FIELD_NAME' => $ufName,
                'USER_TYPE_ID' => $ufData['USER_TYPE_ID'],
                'XML_ID' => $ufData['XML_ID'],
                'SORT' => $ufData['SORT'],
                'MULTIPLE' => $ufData['MULTIPLE'],
                'MANDATORY' => $ufData['MANDATORY'],
                'SHOW_FILTER' => $ufData['SHOW_FILTER'],
                'SHOW_IN_LIST' => $ufData['SHOW_IN_LIST'],
                'EDIT_IN_LIST' => $ufData['EDIT_IN_LIST'],
                'IS_SEARCHABLE' => $ufData['IS_SEARCHABLE'],
                'SETTINGS' => $ufData['SETTINGS'],
                'EDIT_FORM_LABEL' => $ufData['EDIT_FORM_LABEL'],
                'LIST_COLUMN_LABEL' => $ufData['LIST_COLUMN_LABEL'],
                'LIST_FILTER_LABEL' => $ufData['LIST_FILTER_LABEL'],
                'ERROR_MESSAGE' => $ufData['ERROR_MESSAGE'],
                'HELP_MESSAGE' => $ufData['HELP_MESSAGE'],
            ];

            $obUserField = new \CUserTypeEntity;
            $id = $obUserField->Add($arFields);

            if (!$id) {
                $this->log("Error creating user field '{$ufName}': " . $obUserField->LAST_ERROR);
            } else {
                $this->log("Successfully created user field '{$ufName}' with ID: {$id}");
            }
        }
    }

    private function importIblockProperties($iblockId, $oldIblockCode)
    {
        $propertiesFile = $this->xmlPath . $oldIblockCode . '_properties.json';
        if (!file_exists($propertiesFile)) {
            $this->log("Properties file not found: {$propertiesFile}");
            return;
        }

        $propertiesData = json_decode(file_get_contents($propertiesFile), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->log("Error decoding properties JSON: " . json_last_error_msg());
            return;
        }

        foreach ($propertiesData as $propertyData) {
            $propertyData['IBLOCK_ID'] = $iblockId;

            // Обработка описания свойства
            $propertyData['HINT'] = $propertyData['DESCRIPTION'];

            // Обработка параметров отображения
            $propertyData['IS_REQUIRED'] = $propertyData['SHOW_IN_LIST'];
            $propertyData['MULTIPLE'] = $propertyData['SHOW_ON_DETAIL_PAGE'];

            if ($propertyData['PROPERTY_TYPE'] == 'E') {
                // Получаем новый ID связанного инфоблока
                $linkedIblockCode = 'acroweb_' . $propertyData['LINK_IBLOCK_CODE'] . '_' . $this->siteId;
                $linkedIblockTypeId = 'acroweb_' . $propertyData['LINK_IBLOCK_TYPE'] . '_' . $this->siteId;

                $linkedIblock = \CIBlock::GetList([], [
                    'CODE' => $linkedIblockCode,
                    'TYPE' => $linkedIblockTypeId,
                ])->Fetch();

                if ($linkedIblock) {
                    $propertyData['LINK_IBLOCK_ID'] = $linkedIblock['ID'];
                } else {
                    $this->log("Linked iblock not found for property: {$propertyData['CODE']}");
                    // Можно решить пропускать это свойство или создавать его без привязки
                    // continue; // Раскомментируйте, если хотите пропустить
                }
            }

            // Корректная обработка значения по умолчанию для text/html
            if ($propertyData['PROPERTY_TYPE'] == 'S' && $propertyData['USER_TYPE'] == 'HTML') {
                if (is_string($propertyData['DEFAULT_VALUE'])) {
                    $propertyData['DEFAULT_VALUE'] = unserialize($propertyData['DEFAULT_VALUE']);
                }
            }

            // Обработка значений для списков
            if ($propertyData['PROPERTY_TYPE'] == 'L' && isset($propertyData['VALUES'])) {
                $propertyData['VALUES'] = array_map(function ($value) {
                    if (empty($value['XML_ID'])) {
                        $value['XML_ID'] = $value['ID'];
                    }
                    unset($value['ID']); // Удаляем старый ID
                    return $value;
                }, $propertyData['VALUES']);
            }

            // Обработка дополнительных параметров для строковых свойств
            if ($propertyData['PROPERTY_TYPE'] == 'S') {
                $propertyData['ROW_COUNT'] = $propertyData['ROW_COUNT'] ?? 1;
                $propertyData['COL_COUNT'] = $propertyData['COL_COUNT'] ?? 30;
            }

            // Обработка параметров для числовых свойств
            if ($propertyData['PROPERTY_TYPE'] == 'N') {
                $propertyData['PRECISION'] = $propertyData['PRECISION'] ?? 0;
            }

            // Обработка свойств типа "Файл"
            if ($propertyData['PROPERTY_TYPE'] == 'F') {
                if (isset($propertyData['DEFAULT_VALUE'])) {
                    $propertyData['DEFAULT_VALUE'] = $this->importFile($propertyData['DEFAULT_VALUE']);
                }
                $propertyData['FILE_TYPE'] = $propertyData['FILE_TYPE'] ?? '';
                $propertyData['FILE_SIZE'] = $propertyData['MAX_FILE_SIZE'] ?? 0;
            }

            $ibp = new \CIBlockProperty;
            $propId = $ibp->Add($propertyData);
            if (!$propId) {
                $this->log("Error creating property '{$propertyData['CODE']}': " . $ibp->LAST_ERROR);
            } else {
                $this->propertyIdMap[$propertyData['ID']] = $propId;
                $this->log("Successfully created property '{$propertyData['CODE']}' with ID: {$propId}");
            }
        }
    }

    private function importElementProperties($elementId, $properties)
    {
        $propertiesToSet = [];
        foreach ($properties as $code => $property) {
            if ($property['PROPERTY_TYPE'] === 'F' && !empty($property['VALUE'])) {
                if (is_array($property['VALUE'])) {
                    $propertiesToSet[$code] = array_map([$this, 'importFile'], $property['VALUE']);
                } else {
                    $propertiesToSet[$code] = $this->importFile($property['VALUE']);
                }
            } else {
                $propertiesToSet[$code] = $property['VALUE'];
            }
        }
        \CIBlockElement::SetPropertyValuesEx($elementId, false, $propertiesToSet);
    }

    private function log($message)
    {
        file_put_contents(
            $this->importPath . 'import_log.txt',
            date('Y-m-d H:i:s') . ': ' . $message . PHP_EOL,
            FILE_APPEND
        );
    }
}