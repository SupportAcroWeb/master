<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Шаблон авторизации
 * 
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 * @global CMain $APPLICATION
 */

global $APPLICATION;

$APPLICATION->SetTitle('');
?>

<div class="container">
    <div class="block-login__top">
        <h1 class="title2">авторизация</h1>
        <p>
            У Вас еще нет учётной записи?
        </p>
        <a href="<?= $arResult['AUTH_REGISTER_URL'] ?>" class="btn-text2">
            <span>зарегистрироваться</span>
            <svg aria-hidden="true" width="14" height="14">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
            </svg>
        </a>
    </div>
</div>

<div class="container container_bordered1">
    <form 
        data-validate 
        data-onsubmit-trigger="auth_form_submit" 
        name="form_auth" 
        method="post" 
        target="_top" 
        action="<?= $arResult['AUTH_URL'] ?>" 
        class="form"
    >
        <input type="hidden" name="AUTH_FORM" value="Y">
        <input type="hidden" name="TYPE" value="AUTH">
        <?php if ($arResult['BACKURL'] !== ''): ?>
            <input type="hidden" name="backurl" value="<?= $arResult['BACKURL'] ?>">
        <?php endif ?>
        <?php foreach ($arResult['POST'] as $key => $value): ?>
            <input type="hidden" name="<?= $key ?>" value="<?= $value ?>">
        <?php endforeach ?>

        <div class="form-grid1 form-grid1_pad">
            <div class="form-grid1__row">
                <div class="form-group1">
                    <input 
                        id="auth_login_email" 
                        class="field-input1 form-group1__field" 
                        placeholder=" " 
                        type="text" 
                        name="USER_LOGIN" 
                        value="<?= htmlspecialcharsbx($arResult['LAST_LOGIN']) ?>"
                        required
                    >
                    <label class="form-group1__label form-group1__label_req" for="auth_login_email">E-mail</label>
                </div>
            </div>
            <div class="form-grid1__row">
                <div class="form-group1">
                    <input 
                        id="auth_login_password" 
                        class="field-input1 form-group1__field" 
                        placeholder=" " 
                        type="password" 
                        name="USER_PASSWORD" 
                        required
                    >
                    <label class="form-group1__label form-group1__label_req" for="auth_login_password">Пароль</label>
                    <div class="form-group1__icon">
                        <img class="eye1" src="<?= SITE_TEMPLATE_PATH ?>/img/eye1.svg" alt="">
                        <img class="eye2 hidden" src="<?= SITE_TEMPLATE_PATH ?>/img/eye2.svg" alt="">
                    </div>
                    <?php if ($arParams['~AUTH_RESULT'] || $arResult['ERROR_MESSAGE']): ?>
                        <div class="form-error1">
                            <?php
                            ShowMessage($arParams['~AUTH_RESULT']);
                            ShowMessage($arResult['ERROR_MESSAGE']);
                            ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>

            <?php if ($arResult['CAPTCHA_CODE']): ?>
                <div class="form-grid1__row">
                    <div class="form-group1">
                        <input type="hidden" name="captcha_sid" value="<?= $arResult['CAPTCHA_CODE'] ?>">
                        <img 
                            src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult['CAPTCHA_CODE'] ?>" 
                            width="180" 
                            height="40" 
                            alt="CAPTCHA"
                        >
                    </div>
                </div>
                <div class="form-grid1__row">
                    <div class="form-group1">
                        <input 
                            class="field-input1 form-group1__field" 
                            type="text" 
                            name="captcha_word" 
                            maxlength="50" 
                            placeholder=" "
                            autocomplete="off"
                            required
                        >
                        <label class="form-group1__label form-group1__label_req">
                            <?= GetMessage('AUTH_CAPTCHA_PROMT') ?>
                        </label>
                    </div>
                </div>
            <?php endif ?>

            <?php if ($arResult['SECURE_AUTH']): ?>
                <span class="bx-auth-secure" id="bx_auth_secure" title="<?= GetMessage('AUTH_SECURE_NOTE') ?>" style="display:none">
                    <div class="bx-auth-secure-icon"></div>
                </span>
                <noscript>
                    <span class="bx-auth-secure" title="<?= GetMessage('AUTH_NONSECURE_NOTE') ?>">
                        <div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
                    </span>
                </noscript>
            <?php endif ?>
        </div>

        <div class="form-grid1__buttons">
            <button class="btn btn_primary" type="submit" name="Login">
                Авторизоваться
            </button>
            <a href="<?= $arResult['AUTH_FORGOT_PASSWORD_URL'] ?>" class="forgot-password">
                Забыли пароль?
            </a>
        </div>

        <?php if (false && $arResult['AUTH_SERVICES']): ?>
            <div class="form-grid1__row">
                <?php
                $APPLICATION->IncludeComponent(
                    'bitrix:socserv.auth.form',
                    '',
                    [
                        'AUTH_SERVICES' => $arResult['AUTH_SERVICES'],
                        'CURRENT_SERVICE' => $arResult['CURRENT_SERVICE'],
                        'AUTH_URL' => $arResult['AUTH_URL'],
                        'POST' => $arResult['POST'],
                        'SHOW_TITLES' => $arResult['FOR_INTRANET'] ? 'N' : 'Y',
                        'FOR_SPLIT' => $arResult['FOR_INTRANET'] ? 'Y' : 'N',
                        'AUTH_LINE' => $arResult['FOR_INTRANET'] ? 'N' : 'Y',
                    ],
                    $component,
                    ['HIDE_ICONS' => 'Y']
                );
                ?>
            </div>
        <?php endif ?>
    </form>
</div>

<script>
(function() {
    'use strict';
    
    <?php if ($arResult['SECURE_AUTH']): ?>
    const secureEl = document.getElementById('bx_auth_secure');
    if (secureEl) {
        secureEl.style.display = 'inline-block';
    }
    <?php endif ?>

    <?php if ($arResult['LAST_LOGIN'] !== ''): ?>
    try {
        document.form_auth.USER_PASSWORD.focus();
    } catch(e) {}
    <?php else: ?>
    try {
        document.form_auth.USER_LOGIN.focus();
    } catch(e) {}
    <?php endif ?>
})();

// Обработчик события валидации формы авторизации
$(document).on('auth_form_submit', function(event, data) {
    if (data && data.form) {
        // Форма прошла валидацию, отправляем стандартным способом
        data.form.submit();
    }
});
</script>
