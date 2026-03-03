<?php

/**
 * Header template file
 *
 * @global CMain $APPLICATION
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Acroweb\Mage\Helpers\TemplateHelper;

include __DIR__ . '/template_init.php';

Loc::loadLanguageFile(__FILE__);

// Uncomment the following line to enable technical mode for specific IP
TemplateHelper::technicalMode(true, '111.111.111.111');
?>
    <!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">
    <head>
        <title><?php $APPLICATION->ShowTitle() ?></title>
        <?php $APPLICATION->ShowHead(); ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
<body class="preload<?php TemplateHelper::showDivClass('bodyClass'); ?>">
<?php
$APPLICATION->ShowPanel();

TemplateHelper::includeLayout('header');