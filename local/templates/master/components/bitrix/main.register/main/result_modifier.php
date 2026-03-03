<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Context;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

// Получаем данные из запроса для сохранения значений при ошибках
$request = Context::getCurrent()->getRequest();
$postData = $request->getPostList()->toArray();

// Дополняем VALUES данными из REGISTER массива (личные данные)
$registerFields = [
    'NAME',
    'LAST_NAME',
    'SECOND_NAME',
    'PERSONAL_PHONE',
    'EMAIL',
    'PASSWORD',
    'CONFIRM_PASSWORD',
];

foreach ($registerFields as $field) {
    if (!isset($arResult['VALUES'][$field]) && isset($postData['REGISTER'][$field])) {
        $arResult['VALUES'][$field] = $postData['REGISTER'][$field];
    }
}

// Дополняем VALUES данными из полей формы (организация и доп. информация)
$formFields = [
    // Поля для создания организации
    'ORG_INN',
    'ORG_NAME',
    'ORG_KPP',
    'ORG_UR_ADDRESS',
    'ORG_FILE',
    // Пользовательское поле профиля
    'UF_DOP_INFO',
];

foreach ($formFields as $field) {
    if (!isset($arResult['VALUES'][$field])) {
        $arResult['VALUES'][$field] = $request->get($field) ?: '';
    }
}

// Состояние чекбокса согласия
$arResult['VALUES']['agreement'] = $request->get('agreement') === 'Y';
