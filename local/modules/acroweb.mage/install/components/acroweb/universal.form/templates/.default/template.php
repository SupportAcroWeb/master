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

<div class="hystmodal__inner">
    <h2 class="hystmodal__title"><?= $arResult['FORM']['NAME'] ?></h2>
    <div class="hystmodal__description textblock"><?= $arResult['FORM']['DESCRIPTION'] ?></div>
    <form id="<?= $arResult['FORM_UNIQUE_ID'] ?>"
          class="universal-form"
          action="<?= POST_FORM_ACTION_URI ?>"
          method="POST"
          enctype="multipart/form-data"
          data-validate
          data-onsubmit-trigger="callbackFormSubmit<?= $arResult['PREFIX_UNIQUE'] ?>"
    >
        <?= bitrix_sessid_post() ?>
        <input type="hidden" name="WEB_FORM_ID" value="<?= $arResult['FORM']['ID'] ?>">
        <div class="form-grid1">
            <?php foreach ($arResult['QUESTIONS'] as $question): ?>
                <div class="form-grid1__row">
                    <div class="form-group">
                        <label class="form-group__label<?= $question['REQUIRED'] === 'Y' ? ' form-group__label_req' : '' ?>" for="<?= $question['INPUT_ID'] ?>"><?= $question['TITLE'] ?></label>
                        <input id="<?= $question['INPUT_ID'] ?>"
                               class="field-input1 form-group__field <?= $question['EXTRA_CLASS'] ?>"
                               type="text"
                               name="<?= $question['INPUT_NAME'] ?>"
                            <?= $question['EXTRA_ATTRS'] ?>
                            <?= $question['REQUIRED'] === 'Y' ? 'required' : '' ?>>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="form-grid1__row">
                <div class="textblock">
                    <?= Loc::getMessage('FORM_AGREEMENT_TEXT', [
                        '#POLICY_LINK#' => '/policy/',
                        '#OFFER_LINK#' => '/policy/'
                    ]) ?>
                </div>
            </div>

            <?php if ($arResult['FORM']['USE_CAPTCHA'] == 'Y'): ?>
                <div class="form-grid1__row">
                    <div class="form-group">
                        <label for="<?= $arResult['FORM_UNIQUE_ID'] ?>_captcha_word"><?= Loc::getMessage('FORM_CAPTCHA') ?></label>
                        <div class="captcha-wrapper">
                            <input type="hidden" name="captcha_sid" value="<?= $arResult['CAPTCHA_CODE'] ?>" id="<?= $arResult['FORM_UNIQUE_ID'] ?>_captcha_sid">
                            <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult['CAPTCHA_CODE'] ?>" alt="CAPTCHA" class="captcha-image" id="<?= $arResult['FORM_UNIQUE_ID'] ?>_captcha_image">
                            <button type="button" class="btn btn-refresh-captcha">↻</button>
                            <input type="text" name="captcha_word" id="<?= $arResult['FORM_UNIQUE_ID'] ?>_captcha_word" class="field-input1" required>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-grid1__row">
                <button type="submit" class="btn btn_primary btn_wide"><?= $arResult['FORM']['BUTTON'] ?: Loc::getMessage('FORM_SUBMIT_BUTTON') ?></button>
            </div>
        </div>
    </form>
</div>
<script>
    BX.ready(function() {
        class CallbackForm<?= $arResult['PREFIX_UNIQUE'] ?> extends UniversalForm {
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

        let universalForm<?= $arResult['PREFIX_UNIQUE'] ?> = new CallbackForm<?= $arResult['PREFIX_UNIQUE'] ?>(<?= Json::encode($arResult['JS_PARAMS']) ?>);

        $(document).on('callbackFormSubmit<?= $arResult['PREFIX_UNIQUE'] ?>', function(event, form) {
            universalForm<?= $arResult['PREFIX_UNIQUE'] ?>.submitForm(form.form);
        });
    });
</script>