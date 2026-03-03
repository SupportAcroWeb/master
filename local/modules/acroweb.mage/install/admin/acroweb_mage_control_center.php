<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$module_id = 'acroweb.mage';

// Определяем, где находится модуль
if (file_exists($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$module_id."/admin/control_center.php")) {
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$module_id."/admin/control_center.php");
} elseif (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/admin/control_center.php")) {
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/admin/control_center.php");
} else {
    // Если файл не найден ни в local, ни в bitrix
    ShowError('File not found: control_center.php');
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");