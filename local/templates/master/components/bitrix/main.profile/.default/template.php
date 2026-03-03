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

<div class="block-user-cabinet__column">
    <div class="block-user-cabinet__top">
        <div class="block-user-cabinet__title">личные данные</div>
        <div class="block-user-cabinet__text">
            Здесь вы можете отредактировать ваши данные.
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
    
    <form data-validate data-onsubmit-trigger="profile_save" method="post" name="profile_form" action="<?= $arResult["FORM_TARGET"] ?>" enctype="multipart/form-data" class="form">
        <?= $arResult["BX_SESSION_CHECK"] ?>
        <input type="hidden" name="lang" value="<?= LANG ?>" />
        <input type="hidden" name="ID" value="<?= $arResult["ID"] ?>" />
        <input type="hidden" name="save" value="Y" />
        <input type="hidden" name="PROFILE_FORM" value="Y" />
        <?php
        global $USER;
        if (!$USER->IsAdmin()): ?>
            <input type="hidden" name="LOGIN" maxlength="50" value="<?= $arResult["arUser"]["LOGIN"] ?>" />
        <?php endif; ?>
        
        <div class="form-grid1__rows">
            <div class="form-grid1">
                <div class="form-grid1__row">
                    <div class="form-group1">
                        <?php
                        // Собираем полное ФИО для отображения
                        $fullName = trim(
                            $arResult["arUser"]["LAST_NAME"] . ' ' . 
                            $arResult["arUser"]["NAME"] . ' ' . 
                            $arResult["arUser"]["SECOND_NAME"]
                        );
                        ?>
                        <input 
                            id="cabinet_full_name" 
                            class="field-input1 form-group1__field" 
                            type="text" 
                            maxlength="150" 
                            value="<?= htmlspecialcharsbx($fullName) ?>"
                            data-fio-input
                            required
                        >
                        <label class="form-group1__label form-group1__label_req" for="cabinet_full_name">ФИО</label>
                        
                        <!-- Скрытые поля для отправки в компонент -->
                        <input type="hidden" name="LAST_NAME" value="<?= htmlspecialcharsbx($arResult["arUser"]["LAST_NAME"]) ?>" data-fio-last>
                        <input type="hidden" name="NAME" value="<?= htmlspecialcharsbx($arResult["arUser"]["NAME"]) ?>" data-fio-name>
                        <input type="hidden" name="SECOND_NAME" value="<?= htmlspecialcharsbx($arResult["arUser"]["SECOND_NAME"]) ?>" data-fio-second>
                    </div>
                </div>
                <div class="form-grid1__row">
                    <div class="form-group1">
                        <input 
                            id="cabinet_phone" 
                            data-type="phone" 
                            data-mask="phone" 
                            class="field-input1 form-group1__field" 
                            type="text" 
                            name="PERSONAL_PHONE" 
                            maxlength="50" 
                            value="<?= htmlspecialcharsbx($arResult["arUser"]["PERSONAL_PHONE"]) ?>" 
                            required
                        >
                        <label class="form-group1__label form-group1__label_hidden" for="cabinet_phone">Телефон</label>
                        <span class="form-group1__placeholder1 form-group1__placeholder1_req">
                            <span>+7</span> (999) 999-99-99
                        </span>
                    </div>
                </div>
                <div class="form-grid1__row">
                    <div class="form-group1">
                        <input 
                            id="cabinet_email" 
                            class="field-input1 form-group1__field" 
                            data-type="email" 
                            data-mask="email" 
                            type="text" 
                            name="EMAIL" 
                            maxlength="50" 
                            value="<?= htmlspecialcharsbx($arResult["arUser"]["EMAIL"]) ?>" 
                            required
                        >
                        <label class="form-group1__label form-group1__label_req" for="cabinet_email">E-mail</label>
                    </div>
                </div>
            </div>
            
            <div class="form-grid1">
                <?php if ($arResult["MANAGERS"]): ?>
                    <div class="form-grid1__row form-grid1_select">
                        <label class="form-group1__label form-group1__label_req">Личный менеджер</label>
                        <select 
                            data-select 
                            class="ts-wrapper_wide select4" 
                            name="UF_MANAGER_ID" 
                            required
                        >
                            <option value="0">Без менеджера</option>
                            <?php foreach ($arResult["MANAGERS"] as $manager):
                                $name = trim($manager["LAST_NAME"] . ' ' . $manager["NAME"] . ' ' . $manager["SECOND_NAME"]);
                                $isSelected = $manager["ID"] == $arResult["USER_PROPERTIES"]["DATA"]["UF_MANAGER_ID"]["VALUE"];
                            ?>
                                <option value="<?= $manager["ID"] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                    <?= htmlspecialcharsbx($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                
                <div class="form-grid1__row">
                    <div class="form-group1">
                        <textarea 
                            id="cabinet_text1" 
                            class="field-input1 form-group1__field" 
                            name="UF_DOP_INFO"
                        ><?= htmlspecialcharsbx($arResult["USER_PROPERTIES"]["DATA"]["UF_DOP_INFO"]["VALUE"]) ?></textarea>
                        <label class="form-group1__label" for="cabinet_text1">Дополнительная информация</label>
                    </div>
                </div>
            </div>
        </div>
        
        <button class="btn btn_primary" type="submit">
            <?= ($arResult["ID"] > 0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD") ?>
        </button>
    </form>
</div>