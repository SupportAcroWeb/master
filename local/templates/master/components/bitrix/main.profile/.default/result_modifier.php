<?php
/**
 * Модификатор результата для шаблона профиля
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Acroweb\Mage\Service\Manager;
use Acroweb\Mage\Helpers\ComponentHelper;

if (!Loader::includeModule('acroweb.mage')) {
    ShowError('Не включен модуль acroweb.mage');
    return false;
}

$request = Context::getCurrent()->getRequest();

// Получаем список менеджеров
$arResult["MANAGERS"] = [];
$managers = Manager::getManagersId();
if ($managers) {
    $arSortManagers = ComponentHelper::customMultiSort($managers, 'LAST_NAME');
    foreach ($arSortManagers as $manager) {
        $arResult["MANAGERS"][$manager['ID']] = $manager;
    }
}

// Если отправлена форма пароля (не профиля), то не показываем сообщения
if ($request->isPost() && $request->getPost('PASSWORD_FORM') === 'Y') {
    $arResult['DATA_SAVED'] = 'N';
    $arResult['strProfileError'] = '';
}
