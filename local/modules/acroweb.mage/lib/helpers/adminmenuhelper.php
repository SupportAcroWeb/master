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

                $moduleID4Url = 'acroweb_helper';
                $arMenuStore = array(
                    'menu_id' => 'global_menu_acroweb_helper',
                    'text' => 'Склады',
                    'title' => 'Настройка помощника',
                    'sort' => 1,
                    'items_id' => 'global_menu_acroweb_helper_items',
                    'icon' => 'sale_menu_icon_store',
                    'page_icon' => 'acroweb_helper_page_icon',
                    'items' => array(
                        array(
                            'text' => 'Список складов',
                            'title' => 'Список складов',
                            'sort' => 20,
                            'url' => '/bitrix/admin/' . $moduleID4Url . '_edit_stores.php?lang=' . LANGUAGE_ID,
                            'icon' => 'iblock_menu_icon_types',
                            'page_icon' => 'iblock_page_icon_types',
                            'items_id' => 'store_list',
                        ),
                        array(
                            'text' => 'Управление списком складов',
                            'title' => 'Управление списком складов',
                            'sort' => 20,
                            'url' => '/bitrix/admin/' . $moduleID4Url . '_edit_store.php?lang=' . LANGUAGE_ID,
                            'icon' => 'adm-menu-setting',
                            'page_icon' => 'adm-menu-setting',
                            'items_id' => 'settings',
                        ),
                    ),
                );

                $arGlobalMenu['global_menu_store']['items'][$moduleID] = $arMenuStore;
                $arGlobalMenu['global_menu_acroweb']['items'][$moduleID] = $arMenu;
            }
        }
    }
}