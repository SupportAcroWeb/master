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
    <div class="order-header">
        <a class="btn-text btn-text_primary direction-l" href="/personal/moi-zakazy/">
            <svg class="btn-text__icon" width="14" height="14" aria-hidden="true">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow3"></use>
            </svg>
            <span>К моим заказам</span>

        </a>
    </div>