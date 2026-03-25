<?php

global $APPLICATION;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Helpers\TemplateHelper;

?>
<div class="container">
    <?
    TemplateHelper::includePartial('breadcrumbs'); ?>
    <div class="layout-sidebar features-wrapper<?php TemplateHelper::showDivClass('personalClassTwo'); ?>">
        <aside class="layout-sidebar__aside lk-aside">
            <div class="lk-aside__inner">
                <button class="lk-aside__toggle" type="button">
                    <span class="lk-aside__toggle-text"><? $APPLICATION->ShowTitle(false, false); ?></span>
                    <span class="lk-aside__toggle-icon"></span>
                </button>
                <? $APPLICATION->IncludeComponent(
                        "bitrix:menu",
                        "personal_menu",
                        array(
                                "ALLOW_MULTI_SELECT" => "N",
                                "CHILD_MENU_TYPE" => "personal",
                                "DELAY" => "N",
                                "MAX_LEVEL" => "1",
                                "MENU_CACHE_GET_VARS" => array(),
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_TYPE" => "N",
                                "MENU_CACHE_USE_GROUPS" => "Y",
                                "ROOT_MENU_TYPE" => "personal",
                                "USE_EXT" => "N",
                                "COMPONENT_TEMPLATE" => "personal_menu"
                        ),
                        false
                ); ?>

            </div>
        </aside>
        <div class="layout-sidebar__main lk-main">
            <div class="lk-main__inner">
                <h2 class="title2 title"><? $APPLICATION->ShowTitle(false, false); ?></h2>