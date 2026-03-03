<?php

global $APPLICATION;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Helpers\TemplateHelper;

?>
<div class="container"> <?
    TemplateHelper::includePartial('breadcrumbs'); ?>
</div>
<section class="section-top">
    <div class="container">
        <h1 class="section-top__title"><? $APPLICATION->ShowTitle(false, false); ?></h1>
    </div>
</section>