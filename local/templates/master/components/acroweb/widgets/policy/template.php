<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\UserConsent\Agreement;

/**
 * Шаблон виджета политики
 *
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

global $APPLICATION;

$agreementId = $arParams['AGREEMENT_ID'];
$agreement = new Agreement($agreementId);

if ($agreement->isExist()) {
    echo $agreement->getHtml();
} else {
    echo '<p>Текст политики конфиденциальности временно недоступен.</p>';
}