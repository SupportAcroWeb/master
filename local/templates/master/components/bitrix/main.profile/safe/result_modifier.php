<?php
/**
 * Модификатор результата для шаблона смены пароля
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Context;

$request = Context::getCurrent()->getRequest();

// Если отправлена форма профиля (не пароля), то не показываем сообщения
if ($request->isPost() && $request->getPost('PROFILE_FORM') === 'Y') {
    $arResult['DATA_SAVED'] = 'N';
    $arResult['strProfileError'] = '';
}

