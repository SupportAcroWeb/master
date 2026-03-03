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
    </div>
</div>
<div class="block-order block-order-info">
    <div class="container">
        <a href="/personal/moi-zakazy/" class="back">
            <svg aria-hidden="true" width="14" height="14">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
            </svg>
            <span>к моим заказам</span>
        </a>
    </div>