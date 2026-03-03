<?php

global $APPLICATION;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Helpers\TemplateHelper;
use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();
?>
<div class="block-breadcrumbs">
    <div class="container"> <?
        TemplateHelper::includePartial('breadcrumbs'); ?>
    </div>
</div>
<?php
if (!$request->get('ORDER_ID')) {
?>
<div class="block-order">
    <div class="container">
        <a href="/personal/basket/" class="back">
            <svg aria-hidden="true" width="16" height="16">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
            </svg>
            <span>в корзину</span>
        </a>
        <div class="heading-cols1">
            <h1 class="title2">оформление заказа</h1>
        </div>
    </div>
    <div class="container container_bordered1">
<?php }