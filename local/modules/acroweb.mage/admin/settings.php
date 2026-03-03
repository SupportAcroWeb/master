<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

global $APPLICATION;

$module_id = 'acroweb.mage';

// Определяем путь к модулю
$modulePath = preg_match("#/local/#", str_replace('\\', '/', __FILE__))
    ? $_SERVER["DOCUMENT_ROOT"]."/local/modules/".$module_id
    : $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id;

$includePath = $modulePath."/include.php";

if (!file_exists($includePath)) {
    ShowError('File not found: include.php');
    return;
}

require_once($includePath);

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage("ACROWEB_CORE_CONTROL_CENTER_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// Подключаем файл options.php
require($modulePath."/options.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");