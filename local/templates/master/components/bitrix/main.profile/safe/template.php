<?php
/**
 * Шаблон смены пароля пользователя
 * 
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

if ($arResult["SHOW_SMS_FIELD"] == true) {
    CJSCore::Init('phone_auth');
}
?>

<div class="block-user-cabinet__column">
    <div class="block-user-cabinet__top">
        <div class="block-user-cabinet__title">сменить пароль</div>
        <div class="block-user-cabinet__text">
            После изменения пароля вам нужно будет авторизоваться заново.
        </div>
    </div>
    
    <?php if ($arResult["strProfileError"]): ?>
        <div class="error">
            <?php ShowError($arResult["strProfileError"]); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($arResult['DATA_SAVED'] == 'Y'): ?>
        <div class="success">
            <?php ShowNote(GetMessage('PROFILE_DATA_SAVED')); ?>
        </div>
    <?php endif; ?>
    
    <div id="bx_profile_error" style="display:none">
        <?php ShowError("error"); ?>
    </div>
    <div id="bx_profile_resend"></div>
    
    <form data-validate data-onsubmit-trigger="password_save" method="post" name="password_form" action="<?= $arResult["FORM_TARGET"] ?>" enctype="multipart/form-data" class="form">
        <?= $arResult["BX_SESSION_CHECK"] ?>
        <input type="hidden" name="lang" value="<?= LANG ?>" />
        <input type="hidden" name="ID" value="<?= $arResult["ID"] ?>" />
        <input type="hidden" name="save" value="Y" />
        <input type="hidden" name="PASSWORD_FORM" value="Y" />
        
        <div class="form-grid1__rows">
            <div class="form-grid1">
                <div class="form-grid1__row">
                    <div class="form-group1">
                        <input 
                            id="cabinet_password2" 
                            class="field-input1 form-group1__field" 
                            type="password" 
                            name="NEW_PASSWORD" 
                            required
                        >
                        <label class="form-group1__label form-group1__label_req" for="cabinet_password2">Новый пароль</label>
                        <div class="form-group1__icon">
                            <img class="eye1" src="<?= SITE_TEMPLATE_PATH ?>/img/eye1.svg" alt="">
                            <img class="eye2 hidden" src="<?= SITE_TEMPLATE_PATH ?>/img/eye2.svg" alt="">
                        </div>
                    </div>
                </div>
                <div class="form-grid1__row">
                    <div class="form-group1">
                        <input 
                            id="cabinet_password3" 
                            class="field-input1 form-group1__field" 
                            type="password" 
                            name="NEW_PASSWORD_CONFIRM" 
                            required
                        >
                        <label class="form-group1__label form-group1__label_req" for="cabinet_password3">Повторить новый пароль</label>
                        <div class="form-group1__icon">
                            <img class="eye1" src="<?= SITE_TEMPLATE_PATH ?>/img/eye1.svg" alt="">
                            <img class="eye2 hidden" src="<?= SITE_TEMPLATE_PATH ?>/img/eye2.svg" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn_primary">
            Сменить пароль
        </button>
    </form>
</div>