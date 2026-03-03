<?php

global $APPLICATION;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Helpers\TemplateHelper;

?>
<div class="block-breadcrumbs breadcrumbs-delivery">
    <div class="container"> <?
        TemplateHelper::includePartial('breadcrumbs'); ?>
    </div>
</div>
<div class="block-login block1 block-problem">
    <div class="container">
        <div class="block-login__top">
            <h1 class="title2"><? $APPLICATION->IncludeFile(SITE_DIR . 'include/problem/title.php') ?></h1>
            <p><? $APPLICATION->IncludeFile(SITE_DIR . 'include/problem/description.php') ?></p>
        </div>
    </div>
    <div class="container container_bordered1">

