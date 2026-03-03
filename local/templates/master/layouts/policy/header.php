<?php

global $APPLICATION;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Helpers\TemplateHelper;

?>
<div class="container"> <?
    TemplateHelper::includePartial('breadcrumbs'); ?>
    <div class="block-policy">
        <div class="catalog-text textblock1">