<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loader::includeModule('form');

$formList = ['' => Loc::getMessage('FORM_PARAM_FORM_ID_NOT_SELECTED')];
$dbForms = CForm::GetList($by = 'sort', $order = 'asc', [], $filtered = false);
while ($form = $dbForms->Fetch()) {
    $formList[$form['ID']] = '['.$form['ID'].'] '.$form['NAME'];
}

$arComponentParameters = [
    'GROUPS' => [
        'SETTINGS' => [
            'NAME' => Loc::getMessage('FORM_GROUP_SETTINGS'),
            'SORT' => 100,
        ],
    ],
    'PARAMETERS' => [
        'FORM_ID' => [
            'PARENT' => 'SETTINGS',
            'NAME' => Loc::getMessage('FORM_PARAM_FORM_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $formList,
            'DEFAULT' => '',
        ],
        'AJAX' => [
            'PARENT' => 'SETTINGS',
            'NAME' => Loc::getMessage('FORM_PARAM_AJAX'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
        ],
        'SUCCESS_URL' => [
            'PARENT' => 'SETTINGS',
            'NAME' => Loc::getMessage('FORM_PARAM_SUCCESS_URL'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
        ],
    ],
];