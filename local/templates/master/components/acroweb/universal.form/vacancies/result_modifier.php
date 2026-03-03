<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

use Bitrix\Main\Security\Random;

$arResult['FORM_UNIQUE_ID'] = 'universal-form-' . Random::getString(8);
$arResult['PREFIX_UNIQUE'] = '_' . Random::getString(8);

foreach ($arResult['QUESTIONS'] as &$question) {
    $question['INPUT_NAME'] = 'form_text_' . $question['ID'];
    $question['INPUT_ID'] = $arResult['FORM_UNIQUE_ID'] . '_' . $question['SID'];

    switch ($question['SID']) {
        case 'NAME':
            $question['EXTRA_ATTRS'] = '';
            $question['EXTRA_CLASS'] = '';
            break;
        case 'PHONE':
            $question['EXTRA_ATTRS'] = 'data-type="phone" data-mask="phone"';
            $question['EXTRA_CLASS'] = 'phone';
            break;
        case 'TASK':
            $question['INPUT_NAME'] = 'form_textarea_' . $question['ID'];
            $question['EXTRA_ATTRS'] = '';
            $question['EXTRA_CLASS'] = '';
            break;
        case 'FILE':
            $question['EXTRA_ATTRS'] = '';
            $question['EXTRA_CLASS'] = '';
            $question['INPUT_TYPE'] = 'file';
            $question['INPUT_NAME'] = 'form_file_' . $question['ID'];
        default:
            $question['EXTRA_ATTRS'] = '';
            $question['EXTRA_CLASS'] = '';
    }

if (!isset($question['REQUIRED'])) {
        $question['REQUIRED'] = 'N';
    }
}
unset($question);

$arResult['JS_PARAMS'] = [
    'formId' => $arResult['FORM_UNIQUE_ID'],
    'componentName' => $this->__component->getName(),
    'useAjax' => $arParams['AJAX'] ? true : false,
    'successUrl' => $arParams['SUCCESS_URL'],
];