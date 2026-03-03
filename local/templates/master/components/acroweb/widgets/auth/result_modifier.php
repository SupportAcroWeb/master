<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Context;
use Bitrix\Main\Engine\CurrentUser;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

/** @global CMain $APPLICATION */
global $APPLICATION;

// Редирект авторизованных пользователей
if (CurrentUser::get()->getId() > 0) {
    LocalRedirect('/personal/');
}

$request = Context::getCurrent()->getRequest();

// Определяем режим работы страницы авторизации
$arResult['MODE'] = 'login'; // По умолчанию - вход

if ($request->get('change_password') === 'yes') {
    $arResult['MODE'] = 'change_password';
} elseif ($request->get('forgot_password') === 'yes') {
    $arResult['MODE'] = 'forgot_password';
} elseif ($request->get('register') === 'yes') {
    $arResult['MODE'] = 'register';
} elseif ($request->get('confirm_registration') === 'yes') {
    $arResult['MODE'] = 'confirm_registration';
} elseif ($request->get('confirm_request') === 'yes') {
    $arResult['MODE'] = 'confirm_request';
}

// Параметры для регистрации
$arResult['REGISTER_PARAMS'] = [
    'AUTH_RESULT' => $APPLICATION->arAuthResult ?? [],
    'SHOW_ERRORS' => 'Y',
    'AUTH' => 'Y',
    'REQUIRED_FIELDS' => [
        'EMAIL',
        'NAME',
        'PERSONAL_PHONE',
    ],
    'SET_TITLE' => 'Y',
    'SHOW_FIELDS' => [
        'EMAIL',
        'NAME',
        'SECOND_NAME',
        'LAST_NAME',
        'PERSONAL_PHONE',
    ],
    'SUCCESS_PAGE' => '/auth/?confirm_request=yes',
    'USER_PROPERTY' => [
        'UF_DOP_INFO',
        'UF_MANAGER_ID',
    ],
    'USER_PROPERTY_NAME' => '',
    'USE_BACKURL' => 'Y',
];

// Общие параметры для системных компонентов
$arResult['AUTH_PARAMS'] = [
    'SHOW_ERRORS' => 'Y',
    'AUTH_RESULT' => $APPLICATION->arAuthResult ?? [],
];

