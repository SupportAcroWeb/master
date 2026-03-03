<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

$this->setFrameMode(true);
$formId = $this->getEditAreaId($arParams['IBLOCK_ID'] . '_' . $this->randString());
?>

<div class="hystmodal__inner">
    <h2 class="hystmodal__title"><?= htmlspecialcharsbx($arParams['FORM_TITLE']) ?></h2>
    <div class="hystmodal__description textblock"><?= htmlspecialcharsbx($arParams['FORM_DESCRIPTION']) ?></div>
    <form id="<?= $formId ?>"
          class="universal-form"
          method="post"
          enctype="multipart/form-data"
          data-validate
          data-onsubmit-trigger="callbackFormSubmit_<?= CUtil::JSEscape($formId) ?>"
    >
        <?= bitrix_sessid_post() ?>
        <input type="hidden" name="IBLOCK_ID" value="<?= $arParams['IBLOCK_ID'] ?>">
        <input type="hidden" name="FORM_NAME" value="<?= $arParams['FORM_NAME'] ?>">
        <input type="hidden" name="ELEMENT_NAME" value="<?= $arParams['ELEMENT_NAME'] ?>">
        <div class="form-grid1">
            <?php foreach ($arResult['FIELDS'] as $field): ?>
                <div class="form-grid1__row">
                    <div class="form-group">
                        <label class="form-group__label<?= $field['IS_REQUIRED'] === 'Y' ? ' form-group__label_req' : '' ?>" for="<?= $field['CODE'] ?>"><?= $field['NAME'] ?></label>
                        <?php
                        switch ($field['PROPERTY_TYPE']) {
                            case 'S': // String
                                if ($field['USER_TYPE'] === 'HTML') {
                                    echo '<textarea id="' . $field['CODE'] . '" name="PROPERTY[' . $field['CODE'] . ']" class="field-input1 form-group__field"' . ($field['IS_REQUIRED'] === 'Y' ? ' required' : '') . '></textarea>';
                                } else {
                                    echo '<input ' . ($field['CODE'] === 'EMAIL' ? 'data-type="email"' : '') . ' type="text" id="' . $field['CODE'] . '" name="PROPERTY[' . $field['CODE'] . ']" ' . ($field['CODE'] === 'PHONE' ? 'data-type="phone" data-mask="phone" ' : '') . ' class="field-input1 form-group__field"' . ($field['IS_REQUIRED'] === 'Y' ? ' required' : '') . '>';
                                }
                                break;
                            case 'N': // Number
                                echo '<input type="number" id="' . $field['CODE'] . '" name="PROPERTY[' . $field['CODE'] . ']" class="field-input1 form-group__field"' . ($field['IS_REQUIRED'] === 'Y' ? ' required' : '') . '>';
                                break;
                            case 'L': // List
                                echo '<select id="' . $field['CODE'] . '" name="PROPERTY[' . $field['CODE'] . ']" class="field-input1 form-group__field"' . ($field['IS_REQUIRED'] === 'Y' ? ' required' : '') . '>';
                                foreach ($field['ENUM'] as $enumValue) {
                                    echo '<option value="' . $enumValue['ID'] . '">' . $enumValue['VALUE'] . '</option>';
                                }
                                echo '</select>';
                                break;
                            case 'F': // File
                                echo '<input type="file" id="' . $field['CODE'] . '" name="PROPERTY[' . $field['CODE'] . ']" class="field-input1 form-group__field"' . ($field['IS_REQUIRED'] === 'Y' ? ' required' : '') . '>';
                                break;
                            // Add more cases for other property types as needed
                        }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="form-grid1__row">
                <div class="textblock">
                    <?= Loc::getMessage('ACROWEB_UIBF_AGREEMENT_TEXT', [
                        '#BUTTON_TEXT#' => $arParams['BUTTON_TEXT'] ?: Loc::getMessage('ACROWEB_UIBF_SUBMIT_BUTTON'),
                        '#POLICY_LINK#' => '/policy/',
                        '#OFFER_LINK#' => '/policy/'
                    ]) ?>
                </div>
            </div>

            <?php
            if ($arParams['USE_CAPTCHA']): ?>
                <div class="form-grid1__row">
                    <div class="form-group">
                        <label for="<?= $formId ?>_captcha_word"><?= Loc::getMessage('ACROWEB_UIBF_CAPTCHA_LABEL') ?></label>
                        <div class="captcha-wrapper">
                            <input type="hidden" name="captcha_sid" value="<?= $arResult['CAPTCHA_CODE'] ?>" id="<?= $formId ?>_captcha_sid">
                            <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult['CAPTCHA_CODE'] ?>" alt="CAPTCHA" class="captcha-image" id="<?= $formId ?>_captcha_image">
                            <button type="button" class="btn btn-refresh-captcha">↻</button>
                            <input type="text" name="captcha_word" id="<?= $formId ?>_captcha_word" class="field-input1" required>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-grid1__row">
                <button type="submit" class="btn btn_primary btn_wide"><?= $arParams['BUTTON_TEXT'] ?: Loc::getMessage('ACROWEB_UIBF_SUBMIT_BUTTON') ?></button>
            </div>
        </div>
    </form>
</div>

<script>
    BX.ready(function() {
        class CallbackForm_<?= CUtil::JSEscape($formId) ?> extends UniversalIblockForm {
            onSuccess(message) {
                AW.showModalNotification(
                    '<?= Loc::getMessage('ACROWEB_UIBF_SUCCESS_TITLE') ?>',
                    '<?= Loc::getMessage('ACROWEB_UIBF_SUCCESS_MESSAGE') ?>',
                    '<?= Loc::getMessage('ACROWEB_UIBF_CLOSE_BUTTON') ?>'
                );
                if (this.successUrl) {
                    window.location.href = this.successUrl;
                }
            }
        }

        let callbackForm_<?= CUtil::JSEscape($formId) ?> = new CallbackForm_<?= CUtil::JSEscape($formId) ?>({
            formId: '<?= CUtil::JSEscape($formId) ?>',
            componentName: '<?= $component->getName() ?>',
            useAjax: <?= $arParams['AJAX'] ? 'true' : 'false' ?>,
            successUrl: '<?= CUtil::JSEscape($arParams['SUCCESS_URL']) ?>'
        });

        $(document).on('callbackFormSubmit_<?= CUtil::JSEscape($formId) ?>', function(event, formData) {
            if (formData.form.id === '<?= CUtil::JSEscape($formId) ?>') {
                callbackForm_<?= CUtil::JSEscape($formId) ?>.submitForm(formData.form);
            }
        });
    });
</script>