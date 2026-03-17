<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\StoreTable;

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

Loader::includeModule('catalog');

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle("Редактирование складов");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// Получение списка складов
$stores = StoreTable::getList([
    'select' => ['ID', 'TITLE', 'ADDRESS', 'PHONE'],
    'order' => ['SORT' => 'ASC']
])->fetchAll();

// Создаем объект списка
$lAdmin = new CAdminList('stores_list');

// Создаем заголовки таблицы
$lAdmin->AddHeaders(array(
    array("id"=>"ID", "content"=>"ID", "sort"=>"ID", "default"=>true),
    array("id"=>"TITLE", "content"=>"Название", "sort"=>"TITLE", "default"=>true),
    array("id"=>"ADDRESS", "content"=>"Адрес", "sort"=>"ADDRESS", "default"=>true),
    array("id"=>"PHONE", "content"=>"Телефон", "sort"=>"PHONE", "default"=>true),
));

// Заполняем список элементами
foreach ($stores as $store)
{
    $row = $lAdmin->AddRow($store['ID'], $store);

    $row->AddViewField("TITLE", '<a href="acroweb_helper_edit_store.php?ID='.$store['ID'].'&lang='.LANGUAGE_ID.'" title="Редактировать">'.htmlspecialcharsbx($store['TITLE']).'</a>');
    $row->AddViewField("ADDRESS", htmlspecialcharsbx($store['ADDRESS']));
    $row->AddViewField("PHONE", htmlspecialcharsbx($store['PHONE']));

    // Добавляем действия для каждой строки
    $arActions = array();
    $arActions[] = array(
        "ICON" => "edit",
        "TEXT" => "Редактировать",
        "ACTION" => $lAdmin->ActionRedirect("acroweb_helper_edit_store.php?ID=".$store['ID']."&lang=".LANGUAGE_ID),
        "DEFAULT" => true
    );

    $row->AddActions($arActions);
}

// Добавляем кнопку для создания нового склада
$aContext = array(
    array(
        "TEXT" => "Добавить склад",
        "LINK" => "acroweb_helper_edit_store.php?lang=".LANGUAGE_ID,
        "TITLE" => "Добавить новый склад",
        "ICON" => "btn_new",
    ),
);
$lAdmin->AddAdminContextMenu($aContext);

// Выводим список
$lAdmin->CheckListMode();

// Отображаем список
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");