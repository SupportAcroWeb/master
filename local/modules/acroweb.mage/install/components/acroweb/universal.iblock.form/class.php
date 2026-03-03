<?php

namespace Acroweb\Components;

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use CBitrixComponent;
use CIBlockElement;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\Page\Asset;
use CFile;
use Bitrix\Main\Data\Cache;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

Loader::includeModule('iblock');

/**
 * Universal Iblock Form Component
 *
 * This component handles form display and submission for Bitrix iblocks.
 */
class UniversalIblockFormComponent extends CBitrixComponent implements Controllerable
{
    /** @var ErrorCollection */
    protected $errorCollection;

    /**
     * Constructor.
     *
     * @param CBitrixComponent|null $component
     */
    public function __construct(?CBitrixComponent $component = null)
    {
        parent::__construct($component);
        $this->errorCollection = new ErrorCollection();
    }

    /**
     * Prepare component parameters.
     *
     * @param array $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams = parent::onPrepareComponentParams($arParams);
        $arParams['IBLOCK_ID'] = isset($arParams['IBLOCK_ID']) ? intval($arParams['IBLOCK_ID']) : 0;
        $arParams['AJAX'] = isset($arParams['AJAX']) && $arParams['AJAX'] === 'Y';
        $arParams['SUCCESS_URL'] = isset($arParams['SUCCESS_URL']) ? $arParams['SUCCESS_URL'] : '';
        $arParams['USE_CAPTCHA'] = isset($arParams['USE_CAPTCHA']) && $arParams['USE_CAPTCHA'] === 'Y';
        $arParams['EMAIL_TO'] = isset($arParams['EMAIL_TO']) ? $arParams['EMAIL_TO'] : '';
        $arParams['ELEMENT_NAME'] = isset($arParams['ELEMENT_NAME']) ? $arParams['ELEMENT_NAME'] : Loc::getMessage(
            'ACROWEB_UIBF_ELEMENT_NAME_DEFAULT'
        );
        $arParams['CHECK_EMAIL_TEMPLATE'] = isset($arParams['CHECK_EMAIL_TEMPLATE']) && $arParams['CHECK_EMAIL_TEMPLATE'] === 'Y' ? 'Y' : 'N';
        $arParams['FORM_NAME'] = isset($arParams['FORM_NAME']) ? $arParams['FORM_NAME'] : Loc::getMessage(
            'ACROWEB_UIBF_DEFAULT_FORM_NAME'
        );
        $arParams['REQUIRED_FIELDS'] = isset($arParams['REQUIRED_FIELDS']) && is_array(
            $arParams['REQUIRED_FIELDS']
        ) ? $arParams['REQUIRED_FIELDS'] : [];
        $arParams['DEFAULT_VALUES'] = isset($arParams['DEFAULT_VALUES']) && is_array(
            $arParams['DEFAULT_VALUES']
        ) ? $arParams['DEFAULT_VALUES'] : [];

        return $arParams;
    }

    /**
     * Execute component.
     *
     * @return array
     */
    public function executeComponent(): array
    {
        $this->registerScript();
        if ($this->arParams['CHECK_EMAIL_TEMPLATE'] === 'Y') {
            $this->importEmailTemplate();
        }

        try {
            if ($this->startResultCache()) {
                $this->arResult['FIELDS'] = $this->getIblockProperties();

                if ($this->arParams['USE_CAPTCHA']) {
                    $this->arResult['CAPTCHA_CODE'] = $GLOBALS['APPLICATION']->CaptchaGetCode();
                }

                $this->includeComponentTemplate();
            }
        } catch (SystemException $e) {
            $this->abortResultCache();
            ShowError($e->getMessage());
        }

        return $this->arResult;
    }

    protected function registerScript()
    {
        if (!Loader::includeModule('main')) {
            return;
        }

        Asset::getInstance()->addJs($this->getPath() . '/script.js');
    }

    /**
     * Get iblock properties.
     *
     * @return array
     * @throws SystemException
     */
    protected function getIblockProperties()
    {
        if (!$this->arParams['IBLOCK_ID']) {
            throw new SystemException(Loc::getMessage('ACROWEB_UIBF_IBLOCK_ID_NOT_SET'));
        }

        $properties = [];
        $dbProperties = \CIBlockProperty::GetList(
            ['SORT' => 'ASC'],
            ['IBLOCK_ID' => $this->arParams['IBLOCK_ID'], 'ACTIVE' => 'Y']
        );
        while ($property = $dbProperties->GetNext()) {
            $properties[] = $property;
        }

        foreach ($properties as &$property) {
            $property['REQUIRED'] = in_array($property['CODE'], $this->arParams['REQUIRED_FIELDS']);
            $property['DEFAULT_VALUE'] = $this->arParams['DEFAULT_VALUES'][$property['CODE']] ?? '';
        }

        return $properties;
    }

    protected function validateFormData(array $formData): array
    {
        $errors = [];
        $properties = $this->getIblockProperties();

        foreach ($properties as $property) {
            if ($property['REQUIRED'] && empty($formData[$property['CODE']])) {
                $errors[] = Loc::getMessage('ACROWEB_UIBF_REQUIRED_FIELD_ERROR', ['#FIELD#' => $property['NAME']]);
            }
        }

        return $errors;
    }

    /**
     * Configure actions.
     *
     * @return array
     */
    public function configureActions(): array
    {
        return [
            'submitForm' => [
                'prefilters' => [],
                'postfilters' => [],
            ],
            'refreshCaptcha' => [
                'prefilters' => [],
                'postfilters' => [],
            ],
        ];
    }

    public function refreshCaptchaAction()
    {
        $captchaCode = $GLOBALS['APPLICATION']->CaptchaGetCode();
        return [
            'captchaCode' => $captchaCode,
        ];
    }

    /**
     * Submit form action.
     *
     * @return array
     */
    public function submitFormAction(): array
    {
        $request = Application::getInstance()->getContext()->getRequest();
        $formData = $request->getPostList()->toArray();
        $fileData = $request->getFileList()->toArray();

        if (!check_bitrix_sessid()) {
            return $this->getErrorResponse('ACROWEB_UIBF_SECURITY_ERROR');
        }

        if ($formData['ELEMENT_NAME']) {
            $this->arParams['ELEMENT_NAME'] = $formData['ELEMENT_NAME'];
        }

        $formData = array_merge($formData, $fileData);
        $preparedData = $this->prepareFormData($formData);

        $errors = $this->validateFormData($preparedData);
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
            ];
        }

        if ($request->getPost('captcha_sid') && !$this->checkCaptcha(
                $request->getPost('captcha_word'),
                $request->getPost('captcha_sid')
            )) {
            return $this->getErrorResponse('ACROWEB_UIBF_CAPTCHA_ERROR');
        }

        $elementId = $this->addIblockElement($preparedData);
        if (!$elementId) {
            return $this->getErrorResponse('ACROWEB_UIBF_SUBMIT_ERROR');
        }

        $preparedData['FORM_NAME'] = $formData['FORM_NAME'];
        $this->sendNotificationEmail($preparedData);

        return [
            'success' => true,
            'message' => Loc::getMessage('ACROWEB_UIBF_SUBMIT_SUCCESS'),
            'redirectUrl' => $this->arParams['SUCCESS_URL'],
        ];
    }

    protected function prepareFormData(array $formData): array
    {
        $preparedData = [];
        if (!$this->arParams['IBLOCK_ID']) {
            $this->arParams['IBLOCK_ID'] = $formData['IBLOCK_ID'];
        }

        $properties = $this->getIblockProperties();

        foreach ($properties as $property) {
            $code = $property['CODE'];
            if (isset($formData['PROPERTY'][$code]) || isset($formData['PROPERTY_FILE']['name'][$code])) {
                $value = $formData['PROPERTY'][$code];
                if (is_array($value)) {
                    $value = array_map('trim', $value);
                } else {
                    $value = trim($value);
                }

                switch ($property['PROPERTY_TYPE']) {
                    case 'L': // Список
                        $preparedData[$code] = $this->processListProperty($value, $property);
                        break;
                    case 'F': // Файл
                        if (isset($formData['PROPERTY_FILE']['name'][$code])) {
                            $fileInfo = [
                                'name' => $formData['PROPERTY_FILE']['name'][$code],
                                'type' => $formData['PROPERTY_FILE']['type'][$code],
                                'tmp_name' => $formData['PROPERTY_FILE']['tmp_name'][$code],
                                'error' => $formData['PROPERTY_FILE']['error'][$code],
                                'size' => $formData['PROPERTY_FILE']['size'][$code],
                            ];
                            $preparedData[$code] = $this->processFileProperty($fileInfo, $code);
                        }
                        break;
                    case 'S':
                        if ($property['USER_TYPE'] === 'HTML') {
                            $preparedData[$code]['VALUE']['TEXT'] = $value;
                        } else {
                            $preparedData[$code] = $value;
                        }
                        break;
                    default:
                        $preparedData[$code] = $value;
                }
            }
        }

        return $preparedData;
    }

    protected function processListProperty($value, $property)
    {
        if ($property['MULTIPLE'] === 'Y') {
            return $value;
        }
        return is_array($value) ? reset($value) : $value;
    }

    protected function processFileProperty($value, $code)
    {
        if (!is_array($value) || empty($value['name'])) {
            return null;
        }

        $fileArray = [
            "name" => $value['name'],
            "type" => $value['type'],
            "tmp_name" => $value['tmp_name'],
            "error" => $value['error'],
            "size" => $value['size']
        ];

        $fileId = CFile::SaveFile($fileArray, 'iblock');
        return $fileId ?: null;
    }

    private function processFileUploads(array $formData, array $fileData): array
    {
        foreach ($fileData as $fieldName => $fileInfo) {
            if (!empty($fileInfo['name'])) {
                $formData[$fieldName] = $fileInfo;
            }
        }
        return $formData;
    }

    private function addIblockElement(array $formData): int
    {
        $elementName = str_replace('#DATE#', date('d.m.Y H:i:s'), $this->arParams['ELEMENT_NAME']);

        $element = new CIBlockElement;
        $elementFields = [
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            'NAME' => $elementName,
            'ACTIVE' => 'Y',
            'PROPERTY_VALUES' => $formData,
        ];

        $elementId = $element->Add($elementFields);
        if (!$elementId) {
            $this->errorCollection->setError(new Error($element->LAST_ERROR));
        }

        return $elementId;
    }

    private function sendNotificationEmail(array $formData): void
    {
        $eventFields = [];
        $files = [];
        $formDataHtml = '<table style="border-collapse: collapse; width: 100%;">';
        $formDataHtml .= '<tr><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Поле</th><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Значение</th></tr>';

        $properties = $this->getIblockProperties();
        $propertyNames = array_column($properties, 'NAME', 'CODE');
        $propertyType = array_column($properties, 'PROPERTY_TYPE', 'CODE');
        $propertyUserType = array_column($properties, 'USER_TYPE', 'CODE');

        if ($formData['FORM_NAME']) {
            $eventFields['FORM_SUBJECT'] = Loc::getMessage('ACROWEB_UIBF_NEW_FORM_SUBMISSION', [
                '#FORM_NAME#' => $formData['FORM_NAME'],
            ]);
            unset($formData['FORM_NAME']);
        }


        foreach ($formData as $key => $value) {
            $fieldName = $propertyNames[$key] ?? $key;
            if (is_numeric($value) && CFile::GetFileArray($value) && $propertyType[$key] === 'F') {
                $fileArray = CFile::GetFileArray($value);
                if ($fileArray) {
                    $files[] = $fileArray;
                    $fieldValue = Loc::getMessage(
                        'ACROWEB_UIBF_FILE_ATTACHMENT',
                        ['#FILE_NAME#' => $fileArray['ORIGINAL_NAME']]
                    );
                }
            } elseif ($propertyType[$key] === 'S' && $propertyUserType[$key] === 'HTML') {
                $fieldValue = $value['VALUE']['TEXT'];
            } else {
                $fieldValue = is_array($value) ? implode(', ', $value) : $value;
            }

            $eventFields[$key] = $fieldValue;
            $formDataHtml .= sprintf(
                '<tr><td style="border: 1px solid #ddd; padding: 8px;">%s</td><td style="border: 1px solid #ddd; padding: 8px;">%s</td></tr>',
                htmlspecialchars($fieldName),
                htmlspecialchars($fieldValue)
            );
        }

        $formDataHtml .= '</table>';

        $eventFields['FORM_DATA'] = $formDataHtml;

        $messageParams = [
            'EVENT_NAME' => 'ACROWEB_UIBF_FORM_SUBMITTED',
            'LID' => SITE_ID,
            'C_FIELDS' => $eventFields,
        ];

        if (!empty($files)) {
            $messageParams['FILE'] = array_column($files, 'ID');
        }

        Event::send($messageParams);
    }

    private function importEmailTemplate()
    {
        $eventName = 'ACROWEB_UIBF_FORM_SUBMITTED';
        $cache = Cache::createInstance();
        $cacheId = $eventName . "_event_type_and_message";
        $cacheDir = "/acroweb.mage/universal_iblock_form/";

        if ($cache->initCache(3600, $cacheId, $cacheDir)) {
            $vars = $cache->getVars();
            $eventTypeExists = $vars['eventTypeExists'];
            $eventMessageExists = $vars['eventMessageExists'];
        } elseif ($cache->startDataCache()) {
            $eventTypeExists = \CEventType::GetList(['TYPE_ID' => $eventName])->Fetch() !== false;
            $eventMessageExists = \CEventMessage::GetList('id', 'desc', ['EVENT_NAME' => $eventName])->Fetch(
                ) !== false;

            if (!$eventTypeExists) {
                $et = new \CEventType;
                $et->Add([
                    'LID' => 'ru',
                    'EVENT_NAME' => $eventName,
                    'NAME' => Loc::getMessage('ACROWEB_UIBF_EVENT_TYPE_NAME'),
                    'DESCRIPTION' => Loc::getMessage('ACROWEB_UIBF_EVENT_TYPE_DESCRIPTION'),
                ]);
                $eventTypeExists = true;
            }

            if (!$eventMessageExists) {
                $em = new \CEventMessage;
                $em->Add([
                    'ACTIVE' => 'Y',
                    'EVENT_NAME' => $eventName,
                    'LID' => SITE_ID,
                    'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
                    'EMAIL_TO' => '#DEFAULT_EMAIL_FROM#',
                    'SUBJECT' => '#FORM_SUBJECT#',
                    'BODY_TYPE' => 'html',
                    'MESSAGE' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>#FORM_SUBJECT#</title>
</head>
<body>
    <h2>#FORM_SUBJECT#</h2>
    <p>Форма: #FORM_NAME#</p>
    #FORM_DATA#
</body>
</html>',
                ]);
                $eventMessageExists = true;
            }

            $cache->endDataCache([
                'eventTypeExists' => $eventTypeExists,
                'eventMessageExists' => $eventMessageExists,
            ]);
        }
    }

    private function getErrorResponse(string $errorCode): array
    {
        return [
            'success' => false,
            'errors' => [Loc::getMessage($errorCode)],
        ];
    }

    /**
     * Check CAPTCHA.
     *
     * @param string $captchaWord
     * @param string $captchaSid
     * @return bool
     */
    protected function checkCaptcha(string $captchaWord, string $captchaSid): bool
    {
        if (!$captchaWord || !$captchaSid) {
            return false;
        }

        return $GLOBALS['APPLICATION']->CaptchaCheckCode($captchaWord, $captchaSid);
    }
}