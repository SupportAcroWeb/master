<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Шаблон смены пароля
 * 
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 * @global CMain $APPLICATION
 */

global $APPLICATION, $USER;

$APPLICATION->SetTitle('Сброс пароля');
?>

<div class="form-block">
    <h1 class="title2 title">введите новый пароль</h1>
    <p class="desk">Пароль должен быть не менее 6 символов длиной</p>
    <form
        data-validate
        data-onsubmit-trigger="change_password_submit"
        method="post"
        action="<?= $arResult['AUTH_URL'] ?>"
        name="bform"
        class="form-grid1"
    >
        <input type="hidden" name="AUTH_FORM" value="Y">
        <input type="hidden" name="TYPE" value="CHANGE_PWD">
        <?php if ($arResult['BACKURL'] !== ''): ?>
            <input type="hidden" name="backurl" value="<?= $arResult['BACKURL'] ?>">
        <?php endif ?>

        <input
            type="hidden"
            name="USER_CHECKWORD"
            value="<?= htmlspecialcharsbx($arResult['USER_CHECKWORD']) ?>"
        >

        <div class="form-grid1__row">
            <div class="form-group1">
                <input
                    id="change_password_login"
                    class="field-input1 form-group__field"
                    placeholder=" "
                    type="text"
                    name="USER_LOGIN"
                    maxlength="50"
                    value="<?= htmlspecialcharsbx($arResult['LAST_LOGIN']) ?>"
                    required
                >
                <label class="form-group1__label" for="change_password_login">
                    Логин <span class="req">*</span>
                </label>
            </div>
        </div>

        <div class="form-grid1__row">
            <div class="form-group1 form-group1--password">
                <input
                    id="change_password_new"
                    class="field-input1 form-group__field"
                    placeholder=" "
                    type="password"
                    name="USER_PASSWORD"
                    value="<?= htmlspecialcharsbx($arResult['USER_PASSWORD']) ?>"
                    autocomplete="new-password"
                    required
                >
                <label class="form-group1__label" for="change_password_new">
                    Новый пароль <span class="req">*</span>
                </label>
                <div class="password-toggle">
                    <button type="button" class="password-toggle__btn show">
                        <svg class="btn-text__icon" width="24" height="24" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#eye-on1"></use>
                        </svg>
                    </button>
                    <button type="button" class="password-toggle__btn hide">
                        <svg class="btn-text__icon" width="24" height="24" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#eye-off1"></use>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="form-grid1__row">
            <div class="form-group1 form-group1--password">
                <input
                    id="change_password_confirm"
                    class="field-input1 form-group__field"
                    placeholder=" "
                    type="password"
                    name="USER_CONFIRM_PASSWORD"
                    value="<?= htmlspecialcharsbx($arResult['USER_CONFIRM_PASSWORD']) ?>"
                    autocomplete="new-password"
                    required
                >
                <label class="form-group1__label" for="change_password_confirm">
                    Подтвердите пароль <span class="req">*</span>
                </label>
                <div class="password-toggle">
                    <button type="button" class="password-toggle__btn show">
                        <svg class="btn-text__icon" width="24" height="24" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#eye-on1"></use>
                        </svg>
                    </button>
                    <button type="button" class="password-toggle__btn hide">
                        <svg class="btn-text__icon" width="24" height="24" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#eye-off1"></use>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <?php if ($arParams['~AUTH_RESULT']): ?>
            <div id="bx_chpass_resend" class="form-grid1__row">
                <?php ShowMessage($arParams['~AUTH_RESULT']); ?>
            </div>
        <?php endif ?>

        <div id="bx_chpass_error" style="display:none" class="form-grid1__row">
            <?php ShowError('error'); ?>
        </div>

        <div class="form-grid1__row form-grid1__btns">
            <button
                class="btn btn_small btn_black btn_wide form-grid1__btns_m"
                type="submit"
                name="change_pwd"
            >
                Изменить пароль
            </button>
            <a
                href="<?= $arResult['AUTH_AUTH_URL'] ?>"
                class="btn btn_small btn_grey btn_wide form-grid1__btns_bottom"
            >
                авторизоваться
            </a>
        </div>
    </form>
</div>

<?php
// Автоматическая авторизация после успешной смены пароля
if ($arParams['AUTH_RESULT']['TYPE'] === 'OK') {
    $rsUser = CUser::GetByLogin($arResult['LAST_LOGIN']);
            $arUser = $rsUser->Fetch();
    if ($arUser) {
        $USER->Authorize($arUser['ID']);
        LocalRedirect('/personal/');
    }
        }
        ?>

<script>
// Обработчик события валидации формы смены пароля
$(document).on('change_password_submit', function(event, data) {
    if (data && data.form) {
        const password = $(data.form).find('input[name="USER_PASSWORD"]').val();
        const confirmPassword = $(data.form).find('input[name="USER_CONFIRM_PASSWORD"]').val();
        
        // Проверка совпадения паролей
        if (password !== confirmPassword) {
            alert('Пароли не совпадают');
            return false;
        }
        
        // Проверка минимальной длины пароля
        if (password.length < 6) {
            alert('Пароль должен содержать не менее 6 символов');
            return false;
        }
        
        // Форма прошла валидацию, отправляем
        data.form.submit();
    }
});
</script>
