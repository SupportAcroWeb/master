<?php

declare(strict_types=1);

/**
 * Шаблон регистрации
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 * @global CUser $USER
 * @global CMain $APPLICATION
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

global $APPLICATION;

$isRegisterPost = $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['register_submit_button']);

$APPLICATION->SetTitle('');

if ($arResult['SHOW_SMS_FIELD'] === true) {
    CJSCore::Init('phone_auth');
} 
?>

<div class="form-block">
    <h2 class="title2 title">Регистрация</h2>
    <form
        data-validate
        data-onsubmit-trigger="register_submit"
        method="post"
        action="<?= POST_FORM_ACTION_URI ?>"
        name="regform"
        enctype="multipart/form-data"
        autocomplete="off"
        class="form-grid1 form-registration"
    >
        <?= bitrix_sessid_post() ?>
        <input type="hidden" name="REGISTER[LOGIN]" value="<?= htmlspecialcharsbx($arResult['VALUES']['EMAIL']) ?>">

        <?php if ($isRegisterPost && empty($arResult['ERRORS'])): ?>
            <div class="form-grid1__row">
                <div class="form-success1">
                    <?php if ($arResult['USE_EMAIL_CONFIRMATION'] === 'Y'): ?>
                        <p>На указанный вами E-mail отправлено письмо с ссылкой для подтверждения регистрации.</p>
                        <p>Перейдите по ссылке из письма, чтобы завершить регистрацию.</p>
                    <?php else: ?>
                        <p>Регистрация успешно завершена.</p>
                        <p>Теперь вы можете <a href="/auth/">авторизоваться</a>.</p>
                    <?php endif ?>
                </div>
            </div>
        <?php endif ?>

        <?php if (!empty($arResult['ERRORS'])): ?>
            <div class="form-grid1__row">
                <div class="form-error1">
                    <?php
                    foreach ($arResult['ERRORS'] as $key => $error) {
                        if ((int) $key === 0 && $key !== 0) {
                            $arResult['ERRORS'][$key] = str_replace(
                                '#FIELD_NAME#',
                                '&quot;' . GetMessage('REGISTER_FIELD_' . $key) . '&quot;',
                                $error
                            );
                        }
                    }

                    ShowError(implode('<br />', $arResult['ERRORS']));
                    ?>
                </div>
            </div>
        <?php endif ?>

        <div class="form-grid1__row">
            <div class="form-group1">
                <input
                    id="register_last_name_top"
                    class="field-input1 form-group__field"
                    placeholder=" "
                    type="text"
                    name="REGISTER[LAST_NAME]"
                    value="<?= htmlspecialcharsbx($arResult['VALUES']['LAST_NAME']) ?>"
                    required
                >
                <label class="form-group1__label" for="register_last_name_top">
                    Фамилия <span class="req">*</span>
                </label>
            </div>
        </div>

        <div class="form-grid1__row">
            <div class="form-group1">
                <input
                    id="register_name_top"
                    class="field-input1 form-group__field"
                    placeholder=" "
                    type="text"
                    name="REGISTER[NAME]"
                    value="<?= htmlspecialcharsbx($arResult['VALUES']['NAME']) ?>"
                    required
                >
                <label class="form-group1__label" for="register_name_top">
                    Имя <span class="req">*</span>
                </label>
            </div>
        </div>

        <div class="form-grid1__row">
            <div class="form-group1">
                <input
                    id="register_second_name_top"
                    class="field-input1 form-group__field"
                    placeholder=" "
                    type="text"
                    name="REGISTER[SECOND_NAME]"
                    value="<?= htmlspecialcharsbx($arResult['VALUES']['SECOND_NAME']) ?>"
                >
                <label class="form-group1__label" for="register_second_name_top">
                    Отчество
                </label>
            </div>
        </div>

        <div class="form-grid1__row">
            <div class="form-group1">
                <input
                    id="register_email_top"
                    class="field-input1 form-group__field"
                    placeholder=" "
                    type="email"
                    data-type="email"
                    data-mask="email"
                    name="REGISTER[EMAIL]"
                    value="<?= htmlspecialcharsbx($arResult['VALUES']['EMAIL']) ?>"
                    required
                >
                <label class="form-group1__label" for="register_email_top">
                    E-mail <span class="req">*</span>
                </label>
            </div>
        </div>

        <div class="form-grid1__row">
            <div class="form-group1">
                <input
                    id="register_phone_top"
                    data-type="phone"
                    data-mask="phone"
                    placeholder=" "
                    class="field-input1 form-group__field"
                    type="text"
                    name="REGISTER[PERSONAL_PHONE]"
                    value="<?= htmlspecialcharsbx($arResult['VALUES']['PERSONAL_PHONE']) ?>"
                    required
                >
                <label class="form-group1__label" for="register_email_top">
                    Номер телефона <span class="req">*</span>
                </label> 
            </div>
        </div>

        <div class="form-grid1__row">
            <div class="form-group1 form-group1--password">
                <input
                    id="register_password_top"
                    class="field-input1 form-group__field"
                    placeholder=" "
                    type="password"
                    name="REGISTER[PASSWORD]"
                    value="<?= htmlspecialcharsbx($arResult['VALUES']['PASSWORD']) ?>"
                    autocomplete="new-password"
                    required
                >
                <label class="form-group1__label" for="register_password_top">
                    Пароль <span class="req">*</span>
                </label>
                <div class="password-toggle">
                    <button type="button" class="password-toggle__btn password-toggle__btn--masked">
                        <svg class="btn-text__icon" width="24" height="24" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#eye-on1"></use>
                        </svg>
                    </button>
                    <button type="button" class="password-toggle__btn password-toggle__btn--plain">
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
                    id="register_confirm_password_top"
                    class="field-input1 form-group__field"
                    placeholder=" "
                    type="password"
                    name="REGISTER[CONFIRM_PASSWORD]"
                    value="<?= htmlspecialcharsbx($arResult['VALUES']['CONFIRM_PASSWORD']) ?>"
                    autocomplete="new-password"
                    required
                >
                <label class="form-group1__label" for="register_confirm_password_top">
                    Повторить пароль <span class="req">*</span>
                </label>
                <div class="password-toggle">
                    <button type="button" class="password-toggle__btn password-toggle__btn--masked">
                        <svg class="btn-text__icon" width="24" height="24" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#eye-on1"></use>
                        </svg>
                    </button>
                    <button type="button" class="password-toggle__btn password-toggle__btn--plain">
                        <svg class="btn-text__icon" width="24" height="24" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#eye-off1"></use>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <?php if ($arResult['USE_CAPTCHA'] === 'Y'): ?>
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
                        class="field-input1 form-group__field"
                        type="text"
                        name="captcha_word"
                        maxlength="50"
                        placeholder=" "
                        autocomplete="off"
                        required
                    >
                    <label class="form-group1__label">
                        <?= GetMessage('REGISTER_CAPTCHA_PROMT') ?>
                    </label>
                </div>
            </div>
        <?php endif ?>

        <div class="form-grid1__row form-grid1__btns">
            <?php
            $agreementSubmitEventName = 'register_submit';
            ?>
            <?php $APPLICATION->IncludeComponent(
                'bitrix:main.userconsent.request',
                '',
                [
                    'ID' => 2,
                    'IS_INSERTED' => 'Y',
                    'AUTO_SAVE' => 'Y',
                    'IS_CHECKED' => 'N',
                    'IS_LOADED' => 'Y',
                    'INPUT_NAME' => 'agreement',
                    'SUBMIT_EVENT_NAME' => $agreementSubmitEventName,
                    'REPLACE' => [
                        'button_caption' => GetMessage('AUTH_REGISTER'),
                    ],
                ],
                null,
                ['HIDE_ICONS' => 'Y']
            ); ?>
            <button
                class="btn btn_small btn_black btn_wide form-grid1__btns_m"
                type="submit"
                name="register_submit_button"
                value="Y"
            >
                <?= GetMessage('AUTH_REGISTER') ?>
            </button>
            <a
                href="/auth/"
                class="btn btn_small btn_grey btn_wide form-grid1__btns_bottom"
            >
                авторизоваться
            </a>
        </div>

        <div class="form-loader">
            <div class="loader"></div>
        </div>
    </form>
</div>