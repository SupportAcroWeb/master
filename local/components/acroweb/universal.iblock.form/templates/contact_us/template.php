<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);
$formId = $this->getEditAreaId($arParams['IBLOCK_ID'] . '_' . $this->randString());
?>

<form id="<?= $formId ?>"
      class="form-grid1"
      action="<?= POST_FORM_ACTION_URI ?>"
      method="POST"
      enctype="multipart/form-data"
      data-validate
      data-onsubmit-trigger="contactUsFormSubmit_<?= CUtil::JSEscape($formId) ?>"
>
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="IBLOCK_ID" value="<?= $arParams['IBLOCK_ID'] ?>">
    <input type="hidden" name="FORM_NAME" value="<?= $arParams['FORM_NAME'] ?>">
    <input type="hidden" name="ELEMENT_NAME" value="<?= $arParams['ELEMENT_NAME'] ?>">

    <?php foreach ($arResult['FIELDS'] as $field): ?>
        <div class="form-group form-grid1__row ">
            <label class="v-h" for="<?= $field['CODE'] ?>"><?= $field['NAME'] ?></label>
            <?php
            switch ($field['PROPERTY_TYPE']) {
                case 'S': // String
                    if ($field['USER_TYPE'] === 'HTML') {
                        echo '<textarea id="' . $field['CODE'] . '" name="PROPERTY[' . $field['CODE'] . ']" class="field-input2 form-group__field"' . ($field['IS_REQUIRED'] === 'Y' ? ' required' : '') . ' placeholder="' . $field['NAME'] . '"></textarea>';
                    } else {
                        echo '<input ' . ($field['CODE'] === 'EMAIL' ? 'data-type="email"' : '') . ' type="text" id="' . $field['CODE'] . '" name="PROPERTY[' . $field['CODE'] . ']" ' . ($field['CODE'] === 'PHONE' ? 'data-type="phone" data-mask="phone" ' : '') . ' class="field-input2 form-group__field"' . ($field['IS_REQUIRED'] === 'Y' ? ' required' : '') . ' placeholder="' . ($field['CODE'] !== 'PHONE' ? $field['NAME'] : '') . '">';
                    }
                    break;
                // Add more cases for other property types as needed
            }
            ?>
        </div>
    <?php endforeach; ?>

    <?php if ($arParams['USE_CAPTCHA']): ?>
        <div class="form-grid1__row">
            <div class="form-group">
                <label for="<?= $formId ?>_captcha_word"><?= Loc::getMessage('ACROWEB_UIBF_CAPTCHA_LABEL') ?></label>
                <div class="captcha-wrapper">
                    <input type="hidden" name="captcha_sid" value="<?= $arResult['CAPTCHA_CODE'] ?>" id="<?= $formId ?>_captcha_sid">
                    <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult['CAPTCHA_CODE'] ?>" alt="CAPTCHA" class="captcha-image" id="<?= $formId ?>_captcha_image">
                    <button type="button" class="btn btn-refresh-captcha">↻</button>
                    <input type="text" name="captcha_word" id="<?= $formId ?>_captcha_word" class="field-input2 form-group__field" required>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-grid1__row block-application__text">
        <?= Loc::getMessage('ACROWEB_UIBF_AGREEMENT_TEXT', [
            '#BUTTON_TEXT#' => $arParams['BUTTON_TEXT'] ?: Loc::getMessage('ACROWEB_UIBF_SUBMIT_BUTTON'),
            '#POLICY_LINK#' => '/policy/',
            '#OFFER_LINK#' => '/policy/'
        ]) ?>
    </div>

    <div class="form-grid1__row">
        <button class="btn btn_primary" type="submit">
            <span><?= $arParams['BUTTON_TEXT'] ?: Loc::getMessage('ACROWEB_UIBF_SUBMIT_BUTTON') ?></span>
            <svg class="btn__icon" width="16" height="14" aria-hidden="true">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
            </svg>
        </button>
    </div>
</form>

<script>
    BX.ready(function() {
        class ContactUsForm_<?= CUtil::JSEscape($formId) ?> extends UniversalIblockForm {
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

        let contactUsForm_<?= CUtil::JSEscape($formId) ?> = new ContactUsForm_<?= CUtil::JSEscape($formId) ?>({
            formId: '<?= CUtil::JSEscape($formId) ?>',
            componentName: '<?= $component->getName() ?>',
            useAjax: <?= $arParams['AJAX'] ? 'true' : 'false' ?>,
            successUrl: '<?= CUtil::JSEscape($arParams['SUCCESS_URL']) ?>'
        });

        $(document).on('contactUsFormSubmit_<?= CUtil::JSEscape($formId) ?>', function(event, formData) {
            if (formData.form.id === '<?= CUtil::JSEscape($formId) ?>') {
                contactUsForm_<?= CUtil::JSEscape($formId) ?>.submitForm(formData.form);
            }
        });
    });
</script>