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
<div class="block-contacts block1">
    <div class="container container_bordered1">
        <div class="block-contacts__content">


