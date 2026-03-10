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

<div class="form-block">
    <h1 class="title2 title">Подтверждение регистрации</h1>

    <div class="form-grid1">
        <div class="form-grid1__row">
            <div class="form-group1">
                <p>
                    <?= $arResult['MESSAGE_TEXT'] ?>
                </p>
                <p>
                    Пожалуйста, авторизуйтесь, используя свои учетные данные.
                </p>
            </div>
        </div>

        <div class="form-grid1__row form-grid1__btns">
            <a
                href="<?= $arParams['LOGIN_URL'] ?? $arResult['AUTH_AUTH_URL'] ?? SITE_DIR . 'auth/' ?>"
                class="btn btn_small btn_black btn_wide form-grid1__btns_m"
            >
                к авторизации
            </a>
        </div>
    </div>
</div>
