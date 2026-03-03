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
    $question['INPUT_TYPE'] = 'text';

    if ($question['SID'] === 'PHONE') {
        $question['EXTRA_ATTRS'] = 'data-type="phone" data-mask="phone"';
        $question['EXTRA_CLASS'] = 'phone';
    } elseif ($question['SID'] === 'EMAIL') {
        $question['EXTRA_ATTRS'] = 'data-type="email"';
        $question['EXTRA_CLASS'] = '';
    } elseif ($question['SID'] === 'SERVICES_NAME') {
        $question['EXTRA_ATTRS'] = 'data-modal-service="position" readonly';
        $question['EXTRA_CLASS'] = '';
    } elseif ($question['SID'] === 'SERVICES_ID') {
        $question['EXTRA_ATTRS'] = 'data-modal-service="id"';
        $question['EXTRA_CLASS'] = '';
        $question['INPUT_TYPE'] = 'hidden';
    } else {
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