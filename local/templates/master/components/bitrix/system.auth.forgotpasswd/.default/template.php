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

<div class="form-block">
    <h1 class="title2 title">Восстановление пароля</h1>
    <form
        data-validate
        data-onsubmit-trigger="forgot_password_submit"
        name="bform"
        method="post"
        target="_top"
        action="<?= $arResult['AUTH_URL'] ?>"
        class="form-grid1"
    >
        <input type="hidden" name="AUTH_FORM" value="Y">
        <input type="hidden" name="TYPE" value="SEND_PWD">
        <input type="hidden" name="USER_EMAIL">
        <?php if ($arResult['BACKURL'] !== ''): ?>
            <input type="hidden" name="backurl" value="<?= $arResult['BACKURL'] ?>">
        <?php endif ?>

        <div class="form-grid1__row">
            <div class="form-group1">
                <input
                    id="forgot_password_email"
                    class="field-input1 form-group__field"
                    type="text"
                    name="USER_LOGIN"
                    placeholder=" "
                    value="<?= htmlspecialcharsbx($arResult['USER_EMAIL']) ?>"
                    required
                >
                <label class="form-group1__label" for="forgot_password_email">
                    E-mail <span class="req">*</span>
                </label>
            </div>
        </div>

        <?php if ($arParams['~AUTH_RESULT']): ?>
            <div class="form-grid1__row">
                <?php ShowMessage($arParams['~AUTH_RESULT']); ?>
            </div>
        <?php endif ?>

        <div class="form-grid1__row form-grid1__btns">
            <button
                class="btn btn_small btn_black btn_wide form-grid1__btns_m"
                type="submit"
                name="send_account_info"
            >
                Отправить пароль
            </button>
            <a
                href="<?= $arResult['AUTH_AUTH_URL'] ?>"
                class="btn btn_small btn_grey btn_wide form-grid1__btns_bottom"
            >
                к авторизации
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
