<?php

namespace Acroweb\Mage\Helpers;

use Bitrix\Main\Localization\Loc;

class AdminMenuHelper
{
    public static function onBuildGlobalMenu(&$arGlobalMenu, &$arModuleMenu)
    {
        if (!defined('ACROWEB_CORE_MENU_INCLUDED')) {
            define('ACROWEB_CORE_MENU_INCLUDED', true);

            Loc::loadMessages(__FILE__);
            $moduleID = 'acroweb_mage';  // Заменили точку на подчеркивание

            if ($GLOBALS['APPLICATION']->GetGroupRight($moduleID) >= 'R') {
                $arMenu = array(
                    'menu_id' => 'global_menu_acroweb_mage',
                    'text' => Loc::getMessage('ACROWEB_CORE_GLOBAL_MENU_TEXT'),
                    'title' => Loc::getMessage('ACROWEB_CORE_GLOBAL_MENU_TITLE'),
                    'sort' => 1000,
                    'items_id' => 'global_menu_acroweb_mage_items',
                    'icon' => 'acroweb_mage_icon',
                    'page_icon' => 'acroweb_mage_page_icon',
                    'items' => array(
                        array(
                            'text' => Loc::getMessage('ACROWEB_CORE_MENU_CONTROL_CENTER_TEXT'),
                            'title' => Loc::getMessage('ACROWEB_CORE_MENU_CONTROL_CENTER_TITLE'),
                            'sort' => 10,
                            'url' => '/bitrix/admin/' . $moduleID . '_control_center.php?lang=' . LANGUAGE_ID,
                            'icon' => 'perfmon_menu_icon',
                            'page_icon' => 'perfmon_page_icon',
                            'items_id' => 'control_center',
                        ),
                        array(
                            'text' => Loc::getMessage('ACROWEB_CORE_MENU_SETTINGS_TEXT'),
                            'title' => Loc::getMessage('ACROWEB_CORE_MENU_SETTINGS_TITLE'),
                            'sort' => 20,
                            'url' => '/bitrix/admin/' . $moduleID . '_settings.php?lang=' . LANGUAGE_ID,
                            'icon' => 'adm-menu-setting',
                            'page_icon' => 'adm-menu-setting',
                            'items_id' => 'settings',
                        ),
                    ),
                );

                if (!isset($arGlobalMenu['global_menu_acroweb'])) {
                    $arGlobalMenu['global_menu_acroweb'] = array(
                        'menu_id' => 'global_menu_acroweb',
                        'text' => Loc::getMessage('ACROWEB_CORE_GLOBAL_ACROWEB_MENU_TEXT'),
                        'title' => Loc::getMessage('ACROWEB_CORE_GLOBAL_ACROWEB_MENU_TITLE'),
                        'sort' => 1000,
                        'items_id' => 'global_menu_acroweb_items',
                    );
                }

                $arGlobalMenu['global_menu_acroweb']['items'][$moduleID] = $arMenu;
            }
        }
    }
}