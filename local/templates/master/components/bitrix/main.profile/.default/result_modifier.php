<?php
/**
 * Модификатор результата для шаблона профиля
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Context;

$request = Context::getCurrent()->getRequest();

// Если отправлена форма пароля (не профиля), то не показываем сообщения профиля
if ($request->isPost() && $request->getPost('PASSWORD_FORM') === 'Y') {
    $arResult['DATA_SAVED'] = 'N';
    $arResult['strProfileError'] = '';
}
