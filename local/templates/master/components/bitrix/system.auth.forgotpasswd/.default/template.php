<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Шаблон восстановления пароля
 * 
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 * @global CMain $APPLICATION
 */

global $APPLICATION;

$APPLICATION->SetTitle('Восстановление пароля');
?>

<div class="container">
    <div class="block-login__top">
        <h1 class="title2">восстановление пароля</h1>
        <p>
            Подтвердите адрес вашей электронной почты. На него будет отправлен пароль.
        </p>
    </div>
</div>

<div class="container container_bordered1">
    <form 
        data-validate 
        data-onsubmit-trigger="forgot_password_submit" 
        name="bform" 
        method="post" 
        target="_top" 
        action="<?= $arResult['AUTH_URL'] ?>" 
        class="form"
    >
        <input type="hidden" name="AUTH_FORM" value="Y">
        <input type="hidden" name="TYPE" value="SEND_PWD">
        <input type="hidden" name="USER_EMAIL">
        <?php if ($arResult['BACKURL'] !== ''): ?>
            <input type="hidden" name="backurl" value="<?= $arResult['BACKURL'] ?>">
        <?php endif ?>

        <div class="form-grid1">
            <div class="form-grid1__row">
                <div class="form-group1">
                    <input 
                        id="forgot_password_email" 
                        class="field-input1 form-group1__field" 
                        placeholder=" " 
                        type="text" 
                        name="USER_LOGIN" 
                        value="<?= htmlspecialcharsbx($arResult['USER_EMAIL']) ?>"
                        required
                    >
                    <label class="form-group1__label form-group1__label_req" for="forgot_password_email">E-mail</label>
                </div>
            </div>
        </div>

        <?php if ($arParams['~AUTH_RESULT']): ?>
            <div class="form-grid1__row">
                <?php ShowMessage($arParams['~AUTH_RESULT']); ?>
            </div>
        <?php endif ?>

        <div class="form-grid1__buttons">
            <button class="btn btn_primary" type="submit" name="send_account_info">
                Отправить пароль
            </button>
            <a href="<?= $arResult['AUTH_AUTH_URL'] ?>" class="btn-text2">
                <span>к авторизации</span>
                <svg aria-hidden="true" width="14" height="14">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                </svg>
            </a>
        </div>
    </form>
</div>

<script>
(function() {
    'use strict';
    
    // Синхронизация USER_EMAIL с USER_LOGIN при отправке
    document.bform.onsubmit = function() {
        document.bform.USER_EMAIL.value = document.bform.USER_LOGIN.value;
    };
    
    // Автофокус на поле email
    try {
        document.bform.USER_LOGIN.focus();
    } catch(e) {}
})();

// Обработчик события валидации формы восстановления пароля
$(document).on('forgot_password_submit', function(event, data) {
    if (data && data.form) {
        // Форма прошла валидацию, отправляем стандартным способом
        data.form.submit();
    }
});
</script>
