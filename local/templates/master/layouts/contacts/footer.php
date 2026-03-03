<?php
global $APPLICATION;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Acroweb\Mage\Helpers\TemplateHelper;

global $APPLICATION;
?>

<? TemplateHelper::includePartial('block_bank_details'); ?>
<? TemplateHelper::includePartial('block_questions'); ?>