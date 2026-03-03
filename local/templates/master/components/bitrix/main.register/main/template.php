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

$APPLICATION->SetTitle('');

if ($arResult['SHOW_SMS_FIELD'] === true) {
    CJSCore::Init('phone_auth');
}
?>

<div class="container">
    <div class="block-login__top">
        <h1 class="title2">Регистрация</h1>
        <p>
            У Вас уже есть учётная запись?
        </p>
        <a href="/auth/" class="btn-text2">
            <span>авторизоваться</span>
            <svg aria-hidden="true" width="14" height="14">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
            </svg>
        </a>
    </div>
</div>

<div class="container container_bordered1" style="position: relative;">
    <?php if (!empty($arResult['ERRORS'])): ?>
        <div class="form-grid1__row">
            <?php
            foreach ($arResult['ERRORS'] as $key => $error) {
                if (intval($key) == 0 && $key !== 0) {
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
    <?php endif ?>

    <form
            data-validate
            data-onsubmit-trigger="register_submit"
            method="post"
            action="<?= POST_FORM_ACTION_URI ?>"
            name="regform"
            enctype="multipart/form-data"
            autocomplete="off"
            class="form form-registration"
    >
        <input type="hidden" name="REGISTER[LOGIN]" value="<?= htmlspecialcharsbx($arResult['VALUES']['EMAIL']) ?>">

        <!-- Личные данные -->
        <div class="form-grid1">
            <div class="form-grid1__row form-grid1-top">
                <div class="form-grid1__title">личные данные</div>
                <?php if ($arResult['USE_EMAIL_CONFIRMATION'] === 'Y'): ?>
                    <p>
                        На указанный в форме email придет запрос на подтверждение регистрации.
                    </p>
                <?php endif ?>
            </div>

            <div class="form-grid1__row">
                <div class="form-group1">
                    <input
                            id="register_name"
                            class="field-input1 form-group1__field"
                            placeholder=" "
                            type="text"
                            name="REGISTER[NAME]"
                            value="<?= htmlspecialcharsbx($arResult['VALUES']['NAME']) ?>"
                            required
                    >
                    <label class="form-group1__label form-group1__label_req" for="register_name">Имя</label>
                </div>
            </div>

            <div class="form-grid1__row form-grid1__row_2">
                <div class="form-group1">
                    <input
                            id="register_last_name"
                            class="field-input1 form-group1__field"
                            placeholder=" "
                            type="text"
                            name="REGISTER[LAST_NAME]"
                            value="<?= htmlspecialcharsbx($arResult['VALUES']['LAST_NAME']) ?>"
                            required
                    >
                    <label class="form-group1__label form-group1__label_req" for="register_last_name">Фамилия</label>
                </div>
            </div>

            <div class="form-grid1__row form-grid1__row_2">
                <div class="form-group1">
                    <input
                            id="register_second_name"
                            class="field-input1 form-group1__field"
                            placeholder=" "
                            type="text"
                            name="REGISTER[SECOND_NAME]"
                            value="<?= htmlspecialcharsbx($arResult['VALUES']['SECOND_NAME']) ?>"
                    >
                    <label class="form-group1__label" for="register_second_name">Отчество</label>
                </div>
            </div>

            <div class="form-grid1__row form-grid1__row_2">
                <div class="form-group1">
                    <input
                            id="register_phone"
                            data-type="phone"
                            data-mask="phone"
                            placeholder=" "
                            class="field-input1 form-group1__field"
                            type="text"
                            name="REGISTER[PERSONAL_PHONE]"
                            value="<?= htmlspecialcharsbx($arResult['VALUES']['PERSONAL_PHONE']) ?>"
                            required
                    >
                    <label class="form-group1__label form-group1__label_hidden" for="register_phone">Номер
                        телефона</label>
                    <span class="form-group1__placeholder1 form-group1__placeholder1_req">
                        <span>+7</span> (999) 999-99-99
                    </span>
                </div>
            </div>

            <div class="form-grid1__row form-grid1__row_2">
                <div class="form-group1">
                    <input
                            id="register_email"
                            class="field-input1 form-group1__field"
                            placeholder=" "
                            type="email"
                            data-type="email"
                            data-mask="email"
                            name="REGISTER[EMAIL]"
                            value="<?= htmlspecialcharsbx($arResult['VALUES']['EMAIL']) ?>"
                            required
                    >
                    <label class="form-group1__label form-group1__label_req" for="register_email">E-mail</label>
                </div>
            </div>

            <div class="form-grid1__row form-grid1__row_2">
                <div class="form-group1">
                    <input
                            id="register_password"
                            class="field-input1 form-group1__field"
                            placeholder=" "
                            type="password"
                            name="REGISTER[PASSWORD]"
                            value="<?= htmlspecialcharsbx($arResult['VALUES']['PASSWORD']) ?>"
                            autocomplete="new-password"
                            required
                    >
                    <label class="form-group1__label form-group1__label_req" for="register_password">Пароль</label>
                    <div class="form-group1__icon">
                        <img class="eye1" src="<?= SITE_TEMPLATE_PATH ?>/img/eye1.svg" alt="">
                        <img class="eye2 hidden" src="<?= SITE_TEMPLATE_PATH ?>/img/eye2.svg" alt="">
                    </div>
                </div>
            </div>

            <div class="form-grid1__row form-grid1__row_2">
                <div class="form-group1">
                    <input
                            id="register_confirm_password"
                            class="field-input1 form-group1__field"
                            placeholder=" "
                            type="password"
                            name="REGISTER[CONFIRM_PASSWORD]"
                            value="<?= htmlspecialcharsbx($arResult['VALUES']['CONFIRM_PASSWORD']) ?>"
                            autocomplete="new-password"
                            required
                    >
                    <label class="form-group1__label form-group1__label_req" for="register_confirm_password">Подтверждение
                        пароля</label>
                    <div class="form-group1__icon">
                        <img class="eye1" src="<?= SITE_TEMPLATE_PATH ?>/img/eye1.svg" alt="">
                        <img class="eye2 hidden" src="<?= SITE_TEMPLATE_PATH ?>/img/eye2.svg" alt="">
                    </div>
                </div>
            </div>
        </div>

        <!-- Данные организации -->
        <div class="form-grid1">
            <div class="form-grid1__row form-grid1-top">
                <div class="form-grid1__title">данные организации</div>
            </div>

            <div class="form-grid1__row">
                <div class="form-group1">
                    <input
                            id="register_inn"
                            data-mask="inn"
                            class="field-input1 form-group1__field<?= !empty($arResult['VALUES']['ORG_INN']) ? ' filled' : '' ?>"
                            type="text"
                            name="ORG_INN"
                            value="<?= htmlspecialcharsbx($arResult['VALUES']['ORG_INN']) ?>"
                            placeholder=" "
                            data-inn-input
                            required
                    >
                    <label class="form-group1__label form-group1__label_req" for="register_inn">ИНН</label>
                </div>
            </div>

            <div class="form-grid1__row<?= empty($arResult['VALUES']['ORG_NAME']) ? ' hide' : '' ?>" data-org-field>
                <div class="form-group1">
                    <input
                            id="register_org_name"
                            class="field-input1 form-group1__field<?= !empty($arResult['VALUES']['ORG_NAME']) ? ' filled' : '' ?>"
                            type="text"
                            name="ORG_NAME"
                            value="<?= htmlspecialcharsbx($arResult['VALUES']['ORG_NAME']) ?>"
                            placeholder=" "
                            readonly
                    >
                    <label class="form-group1__label form-group1__label_req" for="register_org_name">Название</label>
                </div>
            </div>

            <div class="form-grid1__row<?= empty($arResult['VALUES']['ORG_KPP']) ? ' hide' : '' ?>" data-org-field>
                <div class="form-group1">
                    <input
                            id="register_kpp"
                            class="field-input1 form-group1__field<?= !empty($arResult['VALUES']['ORG_KPP']) ? ' filled' : '' ?>"
                            type="text"
                            name="ORG_KPP"
                            value="<?= htmlspecialcharsbx($arResult['VALUES']['ORG_KPP']) ?>"
                            placeholder=" "
                            readonly
                    >
                    <label class="form-group1__label" for="register_kpp">КПП</label>
                </div>
            </div>

            <div class="form-grid1__row<?= empty($arResult['VALUES']['ORG_UR_ADDRESS']) ? ' hide' : '' ?>"
                 data-org-field>
                <div class="form-group1">
                    <textarea
                            id="register_ur_address"
                            class="field-input1 form-group1__field<?= !empty($arResult['VALUES']['ORG_UR_ADDRESS']) ? ' filled' : '' ?>"
                            name="ORG_UR_ADDRESS"
                            placeholder=" "
                            readonly
                    ><?= htmlspecialcharsbx($arResult['VALUES']['ORG_UR_ADDRESS']) ?></textarea>
                    <label class="form-group1__label" for="register_ur_address">Юридический адрес</label>
                </div>
            </div>

            <div class="form-grid1__row<?= empty($arResult['VALUES']['ORG_NAME']) ? ' hide' : '' ?>" data-org-field>
                <div class="form-group1">
                    <div class="file-user-cabinet">
                        <div class="file-user-cabinet__content">
                            <div class="file-user-cabinet__body">
                                <div class="file-user-cabinet__label">Загрузить карточку организации</div>
                                <div class="file-user-cabinet__file">Файл не выбран</div>
                            </div>
                            <div class="file-user-cabinet__button">
                                Выберите файл
                                <input
                                        id="register_file"
                                        class="field-input1 form-group1__field"
                                        type="file"
                                        name="ORG_FILE"
                                        placeholder=" "
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-notification">Приложите карточку организации для самостоятельной оплаты заказов</p>
            </div>

            <div class="form-grid1__row<?= empty($arResult['VALUES']['ORG_INN']) ? ' hide' : '' ?>" data-org-field>
                <div class="form-group1">
                    <textarea
                            id="register_dop_info"
                            class="field-input1 form-group1__field"
                            name="UF_DOP_INFO"
                            placeholder=" "
                    ><?= htmlspecialcharsbx($arResult['VALUES']['UF_DOP_INFO']) ?></textarea>
                    <label class="form-group1__label" for="register_dop_info">Дополнительная информация</label>
                </div>
            </div>
        </div>
        <div class="form-grid1 form-grid1_1">
        <?php if ($arResult['USE_CAPTCHA'] === 'Y'): ?>
            <?/*
            <div class="form-grid1__row">
                <div class="form-group1">
                    <b><?= GetMessage('REGISTER_CAPTCHA_TITLE') ?></b>
                </div>
            </div>
            */?>
            <div class="form-grid1__row form-grid1__row_2">
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
            <div class="form-grid1__row form-grid1__row_2">
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
                        <?= GetMessage('REGISTER_CAPTCHA_PROMT') ?>
                    </label>
                </div>
            </div>
        <?php endif ?>
        </div>
        <div class="form-grid1__buttons">
            <div class="form-grid1__row form-grid1__row_sparse">
                <div class="form-group">
                    <label class="checkbox-text">
                        <span class="checkbox">
                            <input
                                    class="checkbox__input"
                                    type="checkbox"
                                    name="agreement"
                                    value="Y"
                                <?= !empty($arResult['VALUES']['agreement']) ? ' checked="checked"' : '' ?>
                                required
                            >
                            <span class="checkbox__visual">
                                <svg class="checkbox__mark" width="12" height="11" aria-hidden="true">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mark1"></use>
                                </svg>
                            </span>
                        </span>
                        <span class="checkbox-text__label">
                            <span>Даю согласие на обработку своих <a href="/privacy-policy/">персональных данных</a></span>
                        </span>
                    </label>
                </div>
            </div>

            <button class="btn btn_primary" type="submit" name="register_submit_button">
                <?= GetMessage('AUTH_REGISTER') ?>
            </button>
        </div>

        <div class="form-loader">
            <div class="loader"></div>
        </div>
    </form>
</div>
