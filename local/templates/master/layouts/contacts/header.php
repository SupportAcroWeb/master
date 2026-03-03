<?php

global $APPLICATION;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Helpers\TemplateHelper;

?>
<div class="container"> <?
    TemplateHelper::includePartial('breadcrumbs'); ?>
    <h1 class="title3 title-wrapper"><? $APPLICATION->ShowTitle(false, false); ?></h1>
</div>