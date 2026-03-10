<?php
/**
 * Шаблон смены пароля пользователя
 * 
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if ($arResult['SHOW_SMS_FIELD'] === true) {
    CJSCore::Init('phone_auth');
}
?>

<div class="block-user-cabinet__column">
    <div class="block-user-cabinet__top" style="margin: 20px 0;">
        <div class="block-user-cabinet__text">
            После изменения пароля вам нужно будет авторизоваться заново.
        </div>
    </div>
    
    <?php if ($arResult['strProfileError']): ?>
        <div class="error">
            <?php ShowError($arResult['strProfileError']); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($arResult['DATA_SAVED'] === 'Y'): ?>
        <div class="success">
            <?php ShowNote(GetMessage('PROFILE_DATA_SAVED')); ?>
        </div>
    <?php endif; ?>
    
    <div id="bx_profile_error" style="display:none">
        <?php ShowError('error'); ?>
    </div>
    <div id="bx_profile_resend"></div>
    
    <form
        data-validate
        data-onsubmit-trigger="password_save"
        method="post"
        name="password_form"
        action="<?= $arResult['FORM_TARGET'] ?>"
        enctype="multipart/form-data"
        class="form form-grid1"
    >
        <?= $arResult['BX_SESSION_CHECK'] ?>
        <input type="hidden" name="lang" value="<?= LANG ?>" />
        <input type="hidden" name="ID" value="<?= $arResult['ID'] ?>" />
        <input type="hidden" name="save" value="Y" />
        <input type="hidden" name="PASSWORD_FORM" value="Y" />
        
        <div class="form-grid1__row">
            <div class="form-group1 form-group1--password">
                <p class="form-group1__title">
                    Новый пароль <span class="req">*</span>
                </p>
                <div class="form-group1 form-group1--password">
                    <input
                        id="cabinet_password2"
                        class="field-input1 form-group__field"
                        type="password"
                        name="NEW_PASSWORD"
                        placeholder="Новый пароль"
                        required
                    >
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
        </div>
        
        <div class="form-grid1__row">
            <div class="form-group1 form-group1--password">
                <p class="form-group1__title">
                    Повторить новый пароль <span class="req">*</span>
                </p>
                <div class="form-group1 form-group1--password">
                    <input
                        id="cabinet_password3"
                        class="field-input1 form-group__field"
                        type="password"
                        name="NEW_PASSWORD_CONFIRM"
                        placeholder="Повторить новый пароль"
                        required
                    >
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
        </div>
        
        <div class="form-grid1__row">
            <button type="submit" class="btn btn_primary btn_small">
                <span>изменить пароль</span>
            </button>
        </div>
    </form>
</div>