<?php
/**
 * Шаблон профиля пользователя
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

$APPLICATION->SetTitle("Личные данные");
?>
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

<form
        data-validate
        data-onsubmit-trigger="profile_save"
        method="post"
        name="profile_form"
        action="<?= $arResult["FORM_TARGET"] ?>"
        enctype="multipart/form-data"
        class="form form-grid1"
>
    <?= $arResult["BX_SESSION_CHECK"] ?>
    <input type="hidden" name="lang" value="<?= LANG ?>"/>
    <input type="hidden" name="ID" value="<?= $arResult["ID"] ?>"/>
    <input type="hidden" name="save" value="Y"/>
    <input type="hidden" name="PROFILE_FORM" value="Y"/>
    <?php
    global $USER;
    if (!$USER->IsAdmin()): ?>
        <input type="hidden" name="LOGIN" maxlength="50" value="<?= $arResult["arUser"]["LOGIN"] ?>"/>
    <?php endif; ?>

    <div class="form-grid1__row form-grid1__row_3">
        <p class="form-group1__title">Фамилия <span class="req">*</span></p>
        <div class="form-group1">
            <input
                    id="profile_last_name"
                    class="field-input2 form-group1__field"
                    type="text"
                    name="LAST_NAME"
                    placeholder="Введите Фамилию"
                    value="<?= htmlspecialcharsbx($arResult["arUser"]["LAST_NAME"]) ?>"
                    required
            >
        </div>
    </div>

    <div class="form-grid1__row form-grid1__row_3">
        <p class="form-group1__title">Имя <span class="req">*</span></p>
        <div class="form-group1">
            <input
                    id="profile_name"
                    class="field-input2 form-group1__field"
                    type="text"
                    name="NAME"
                    placeholder="Введите Имя"
                    value="<?= htmlspecialcharsbx($arResult["arUser"]["NAME"]) ?>"
                    required
            >
        </div>
    </div>

    <div class="form-grid1__row form-grid1__row_3">
        <p class="form-group1__title">Отчество</p>
        <div class="form-group1">
            <input
                    id="profile_second_name"
                    class="field-input2 form-group1__field"
                    type="text"
                    name="SECOND_NAME"
                    placeholder="Введите Отчество"
                    value="<?= htmlspecialcharsbx($arResult["arUser"]["SECOND_NAME"]) ?>"
            >
        </div>
    </div>

    <div class="form-grid1__row">
        <p class="form-group1__desk title3">
            <b>Контактные данные</b>
        </p>
    </div>

    <div class="form-grid1__row form-grid1__row_2">
        <p class="form-group1__title">E-mail <span class="req">*</span></p>
        <div class="form-group1">
            <input
                    id="profile_email"
                    class="field-input2 form-group1__field"
                    type="email"
                    data-type="email"
                    data-mask="email"
                    name="EMAIL"
                    placeholder="Введите E-mail"
                    value="<?= htmlspecialcharsbx($arResult["arUser"]["EMAIL"]) ?>"
                    required
            >
        </div>
    </div>

    <div class="form-grid1__row form-grid1__row_2">
        <p class="form-group1__title">Телефон <span class="req">*</span></p>
        <div class="form-group1">
            <input
                    id="profile_phone"
                    class="field-input3 form-group1__field"
                    type="text"
                    data-type="phone"
                    data-mask="phone"
                    name="PERSONAL_PHONE"
                    placeholder=" "
                    value="<?= htmlspecialcharsbx($arResult["arUser"]["PERSONAL_PHONE"]) ?>"
                    required
            >
                <label class="form-group1__label form-group1__label_hidden" for="profile_phone">
                    Телефон
                </label>
        </div>
    </div>

    <div class="form-grid1__row">
        <button class="btn btn_primary" type="submit">
            <?= ($arResult["ID"] > 0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD") ?>
        </button>
    </div>
</form>