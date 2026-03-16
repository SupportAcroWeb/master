<?php

global $APPLICATION;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Helpers\TemplateHelper;
use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();
?>
    <div class="container">
        <?
        TemplateHelper::includePartial('breadcrumbs'); ?>
<?php
if (!$request->get('ORDER_ID')) {
?>
    <div class="order-header">
        <h1 class="title3">Оформление заказа</h1>
    </div>
<?php }