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

<div class="container">
    <div class="block-login__top">
        <h1 class="title2">сброс пароля</h1>
        <p>
            Пароль должен содержать не менее 6 символов.
        </p>
    </div>
</div>

<div class="container container_bordered1">
    <form 
        data-validate 
        data-onsubmit-trigger="change_password_submit" 
        method="post" 
        action="<?= $arResult['AUTH_URL'] ?>" 
        name="bform" 
        class="form"
    >
                <input type="hidden" name="AUTH_FORM" value="Y">
                <input type="hidden" name="TYPE" value="CHANGE_PWD">
        <?php if ($arResult['BACKURL'] !== ''): ?>
            <input type="hidden" name="backurl" value="<?= $arResult['BACKURL'] ?>">
        <?php endif ?>

        <!-- Скрытое поле контрольной строки -->
        <input 
            type="hidden" 
            name="USER_CHECKWORD" 
            value="<?= htmlspecialcharsbx($arResult['USER_CHECKWORD']) ?>"
        >

        <div class="form-grid1">
                <div class="form-grid1__row">
                <div class="form-group1">
                    <input 
                        id="change_password_login" 
                        class="field-input1 form-group1__field" 
                        placeholder=" " 
                        type="text" 
                        name="USER_LOGIN" 
                        maxlength="50" 
                        value="<?= htmlspecialcharsbx($arResult['LAST_LOGIN']) ?>"
                        required
                    >
                    <label class="form-group1__label form-group1__label_req" for="change_password_login">Логин</label>
                </div>
            </div>

                <div class="form-grid1__row">
                <div class="form-group1">
                    <input 
                        id="change_password_new" 
                        class="field-input1 form-group1__field" 
                        placeholder=" " 
                        type="password" 
                        name="USER_PASSWORD" 
                        value="<?= htmlspecialcharsbx($arResult['USER_PASSWORD']) ?>"
                        autocomplete="new-password"
                        required
                    >
                    <label class="form-group1__label form-group1__label_req" for="change_password_new">Пароль</label>
                    <div class="form-group1__icon">
                        <img class="eye1" src="<?= SITE_TEMPLATE_PATH ?>/img/eye1.svg" alt="">
                        <img class="eye2 hidden" src="<?= SITE_TEMPLATE_PATH ?>/img/eye2.svg" alt="">
                    </div>
                </div>
            </div>

                <div class="form-grid1__row">
                <div class="form-group1">
                    <input 
                        id="change_password_confirm" 
                        class="field-input1 form-group1__field" 
                        placeholder=" " 
                        type="password" 
                        name="USER_CONFIRM_PASSWORD" 
                        value="<?= htmlspecialcharsbx($arResult['USER_CONFIRM_PASSWORD']) ?>"
                        autocomplete="new-password"
                        required
                    >
                    <label class="form-group1__label form-group1__label_req" for="change_password_confirm">Подтверждение пароля</label>
                    <div class="form-group1__icon">
                        <img class="eye1" src="<?= SITE_TEMPLATE_PATH ?>/img/eye1.svg" alt="">
                        <img class="eye2 hidden" src="<?= SITE_TEMPLATE_PATH ?>/img/eye2.svg" alt="">
                    </div>
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

        <div class="form-grid1__buttons">
            <button class="btn btn_primary" type="submit" name="change_pwd">
                Изменить пароль
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
