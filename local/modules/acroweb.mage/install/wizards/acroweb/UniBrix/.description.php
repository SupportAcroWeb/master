<?php
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Catalog;
use Bitrix\Crm;
use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!defined("WIZARD_DEFAULT_SITE_ID") && !empty($_REQUEST["wizardSiteID"]))
    define("WIZARD_DEFAULT_SITE_ID", $_REQUEST["wizardSiteID"]);

$isSaleModuleInstalled = Loader::includeModule('sale');

$arWizardDescription = [
    "NAME" => Loc::getMessage("ACROWEB_WIZARD_NAME"),
    "DESCRIPTION" => Loc::getMessage("ACROWEB_WIZARD_DESC"),
    "VERSION" => "1.0.0",
    "START_TYPE" => "WINDOW",
    "WIZARD_TYPE" => "INSTALL",
    "IMAGE" => "/images/".LANGUAGE_ID."/solution.png",
    "PARENT" => "wizard_sol",
    "TEMPLATES" => [
        ["SCRIPT" => "wizard_sol"]
    ],
    "STEPS" => [
        'SelectSiteStep',
        'SelectTemplateStep',
        'SiteSettingsStep',
    ],
];

// Добавляем шаги, связанные с модулем sale, только если он установлен
if ($isSaleModuleInstalled) {
    $arWizardDescription["STEPS"] = array_merge($arWizardDescription["STEPS"], [
        'CatalogSettings',
        'ShopSettings',
        'PersonType',
        'PaySystem',
    ]);
}

// Добавляем финальные шаги
$arWizardDescription["STEPS"] = array_merge($arWizardDescription["STEPS"], [
    'DataInstallStep',
    'FinishStep',
]);

// Остальная логика остается без изменений
if (defined("ADDITIONAL_INSTALL"))
{
    $arWizardDescription["STEPS"] = [
        "SelectTemplateStep",
        "SiteSettingsStep",
    ];
    if ($isSaleModuleInstalled) {
        $arWizardDescription["STEPS"] = array_merge($arWizardDescription["STEPS"], [
            "ShopSettings",
            "PersonType",
        ]);
    }
    $arWizardDescription["STEPS"] = array_merge($arWizardDescription["STEPS"], [
        "DataInstallStep",
        "FinishStep",
    ]);
}
elseif (defined("WIZARD_DEFAULT_SITE_ID"))
{
    $arWizardDescription["STEPS"] = [
        "SelectTemplateStep",
        "SiteSettingsStep",
    ];
    if ($isSaleModuleInstalled) {
        if (LANGUAGE_ID == "ru") {
            $arWizardDescription["STEPS"] = array_merge($arWizardDescription["STEPS"], [
                "CatalogSettings",
                "ShopSettings",
                "PersonType",
                "PaySystem",
            ]);
        } else {
            $arWizardDescription["STEPS"] = array_merge($arWizardDescription["STEPS"], [
                "CatalogSettings",
                "PaySystem",
            ]);
        }
    }
    $arWizardDescription["STEPS"] = array_merge($arWizardDescription["STEPS"], [
        "DataInstallStep",
        "FinishStep",
    ]);
}
else
{
    if (LANGUAGE_ID !== "ru")
    {
        $arWizardDescription["STEPS"] = [
            "SelectSiteStep",
            "SelectTemplateStep",
            "SiteSettingsStep",
        ];
        if ($isSaleModuleInstalled) {
            $arWizardDescription["STEPS"] = array_merge($arWizardDescription["STEPS"], [
                "CatalogSettings",
                "PaySystem",
            ]);
        }
        $arWizardDescription["STEPS"] = array_merge($arWizardDescription["STEPS"], [
            "DataInstallStep",
            "FinishStep",
        ]);
    }
}

$removeCatalog = false;
if (Loader::includeModule('catalog'))
{
    if (Catalog\Config\State::isUsedInventoryManagement())
    {
        $removeCatalog = true;
    }
}
if (Loader::includeModule('crm')) // portals
{
    if (
        \CCrmSaleHelper::isWithOrdersMode()
        || Crm\Settings\LeadSettings::isEnabled()
    )
    {
        $removeCatalog = true;
    }
}
if ($removeCatalog)
{
    $indexCatalog = array_search('CatalogSettings', $arWizardDescription['STEPS']);
    if ($indexCatalog !== false)
    {
        unset($arWizardDescription["STEPS"][$indexCatalog]);
    }
}