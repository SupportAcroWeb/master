<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Шаблон подтверждения регистрации
 * 
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 * @global CMain $APPLICATION
 */

global $APPLICATION;
?>

<div class="container">
    <div class="block-login__top">
        <h1 class="title2">Подтверждение регистрации нового пользователя</h1>
        <p>
            <?= $arResult['MESSAGE_TEXT'] ?>
        </p>
        <p>
            Пожалуйста авторизуйтесь.
        </p>
    </div>
</div>

<div class="container container_bordered1">
    <?php
    $APPLICATION->IncludeComponent(
        'bitrix:system.auth.authorize',
        'no_body',
        []
    );
    ?>
</div>
