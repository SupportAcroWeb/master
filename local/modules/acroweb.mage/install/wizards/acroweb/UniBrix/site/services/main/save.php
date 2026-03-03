<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();


use Bitrix\Main\Config\Option;

$wizardVars = $wizard->GetVars();

function convertArrayToString($value) {
    if (is_array($value)) {
        return json_encode($value);
    }
    return $value;
}

// Проверяем, есть ли переменные
if (!$wizardVars || !is_array($wizardVars)) {
    return true;
}

// Сохраняем все переменные в опции модуля
foreach ($wizardVars as $key => $value) {
    $convertedValue = convertArrayToString($value);
    Option::set("acroweb.mage", $key, $convertedValue);
}

return true;