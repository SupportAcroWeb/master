<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

$this->setFrameMode(true);

use Bitrix\Main\Web\Json;

?>

<form id="<?= $arResult['FORM_UNIQUE_ID'] ?>"
      class="form-grid1"
      action="<?= POST_FORM_ACTION_URI ?>"
      method="POST"
      enctype="multipart/form-data"
      data-validate
      data-onsubmit-trigger="contactUsFormSubmit<?= $arResult['PREFIX_UNIQUE'] ?>"
>
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="WEB_FORM_ID" value="<?= $arResult['FORM']['ID'] ?>">

    <?php foreach ($arResult['QUESTIONS'] as $question): ?>
        <div class="form-group form-grid1__row ">
            <label class="v-h" for="<?= $question['INPUT_ID'] ?>"><?= $question['TITLE'] ?></label>
            <?php if ($question['SID'] === 'TASK'): ?>
                <textarea id="<?= $question['INPUT_ID'] ?>"
                          class="field-input2 form-group__field <?= $question['EXTRA_CLASS'] ?>"
                          name="<?= $question['INPUT_NAME'] ?>"
                          placeholder="<?= $question['TITLE'] ?>"
                          <?= $question['EXTRA_ATTRS'] ?>
                    <?= $question['REQUIRED'] === 'Y' ? 'required' : '' ?>></textarea>
            <?php else: ?>
                <input id="<?= $question['INPUT_ID'] ?>"
                       class="field-input2 form-group__field <?= $question['EXTRA_CLASS'] ?>"
                       type="text"
                       name="<?= $question['INPUT_NAME'] ?>"
                       placeholder="<?= $question['SID'] !== 'PHONE' ? $question['TITLE'] : '' ?>"
                    <?= $question['EXTRA_ATTRS'] ?>
                    <?= $question['REQUIRED'] === 'Y' ? 'required' : '' ?>>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php if ($arResult['FORM']['USE_CAPTCHA'] == 'Y'): ?>
        <div class="form-grid1__row">
            <div class="form-group">
                <label for="<?= $arResult['FORM_UNIQUE_ID'] ?>_captcha_word"><?= Loc::getMessage('FORM_CAPTCHA') ?></label>
                <div class="captcha-wrapper">
                    <input type="hidden" name="captcha_sid" value="<?= $arResult['CAPTCHA_CODE'] ?>" id="<?= $arResult['FORM_UNIQUE_ID'] ?>_captcha_sid">
                    <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult['CAPTCHA_CODE'] ?>" alt="CAPTCHA" class="captcha-image" id="<?= $arResult['FORM_UNIQUE_ID'] ?>_captcha_image">
                    <button type="button" class="btn btn-refresh-captcha">↻</button>
                    <input type="text" name="captcha_word" id="<?= $arResult['FORM_UNIQUE_ID'] ?>_captcha_word" class="field-input2 form-group__field" required>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-grid1__row block-application__text">
        <?= Loc::getMessage('FORM_AGREEMENT_TEXT', [
            '#POLICY_LINK#' => '/policy/',
            '#OFFER_LINK#' => '/policy/'
        ]) ?>
    </div>

    <div class="form-grid1__row">
        <button class="btn btn_primary" type="submit">
            <span><?= $arResult['FORM']['BUTTON'] ?: Loc::getMessage('FORM_SUBMIT_BUTTON') ?></span>
            <svg class="btn__icon" width="16" height="14" aria-hidden="true">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
            </svg>
        </button>
    </div>
</form>

<script>
    BX.ready(function() {
        class ContactUsForm<?= $arResult['PREFIX_UNIQUE'] ?> extends UniversalForm {
            onSuccess(message) {
                AW.showModalNotification(
                    '<?= Loc::getMessage('FORM_SUCCESS_TITLE') ?>',
                    '<?= Loc::getMessage('FORM_SUCCESS_MESSAGE') ?>',
                    '<?= Loc::getMessage('FORM_CLOSE_BUTTON') ?>'
                );
                if (this.successUrl) {
                    window.location.href = this.successUrl;
                }
            }
        }

        let universalForm<?= $arResult['PREFIX_UNIQUE'] ?> = new ContactUsForm<?= $arResult['PREFIX_UNIQUE'] ?>(<?= Json::encode($arResult['JS_PARAMS']) ?>);

        $(document).on('contactUsFormSubmit<?= $arResult['PREFIX_UNIQUE'] ?>', function(event, form) {
            universalForm<?= $arResult['PREFIX_UNIQUE'] ?>.submitForm(form.form);
        });
    });
</script>