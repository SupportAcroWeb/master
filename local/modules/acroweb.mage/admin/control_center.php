<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$module_id = 'acroweb.mage';

// Определяем, где находится модуль
if (file_exists($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$module_id."/include.php")) {
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$module_id."/include.php");
} elseif (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php")) {
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php");
} else {
    // Если файл не найден ни в local, ни в bitrix
    ShowError('File not found: include.php');
    return;
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage("ACROWEB_CORE_CONTROL_CENTER_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// Здесь будет код панели управления
echo "Добро пожаловать в панель управления Acroweb Mage!";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");