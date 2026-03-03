<?php

global $APPLICATION;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Helpers\TemplateHelper;

?>
<div class="block-breadcrumbs">
    <div class="container"> <?
        TemplateHelper::includePartial('breadcrumbs'); ?>
        <h1 class="title2"><? $APPLICATION->ShowTitle(false, false); ?></h1>
    </div>
</div>
<div class="block-user-cabinet block1 favorites-cabinet<?php TemplateHelper::showDivClass('organizationClass'); ?>">
    <div class="container<?php TemplateHelper::showDivClass('personalClass'); ?>">
        <div class="nav-user-cabinet">
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
    </div>
