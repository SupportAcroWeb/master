<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

/**
 * @var array $arParams
 * @var array $arResult
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @global CDatabase $DB
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);
?>

<form id="<?= $arResult['FORM_UNIQUE_ID'] ?>"
      class="form-grid1"
      action=""
      method="POST"
      enctype="multipart/form-data"
      data-validate
      data-onsubmit-trigger="contactUsFormSubmit<?= $arResult['PREFIX_UNIQUE'] ?>"
>
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="WEB_FORM_ID" value="<?= $arResult['FORM']['ID'] ?>">

    <div class="form-grid1">
        <?php foreach ($arResult['QUESTIONS'] as $question): ?>
            <div class="form-grid1__row">
                <p class="form-group1__title"><?= htmlspecialcharsbx($question['TITLE']) ?><?= $question['REQUIRED'] === 'Y' ? ' <span class="req">*</span>' : '' ?></p>
                <div class="form-group1">
                    <?php if ($question['SID'] === 'TASK'): ?>
                        <textarea id="<?= $question['INPUT_ID'] ?>"
                                  class="field-input1 form-group1__field <?= $question['EXTRA_CLASS'] ?>"
                                  name="<?= $question['INPUT_NAME'] ?>"
                                  <?= $question['EXTRA_ATTRS'] ?>
                                  <?= $question['REQUIRED'] === 'Y' ? 'required' : '' ?>></textarea>
                        <label class="form-group1__label" for="<?= $question['INPUT_ID'] ?>"><?= htmlspecialcharsbx($question['TITLE']) ?></label>
                    <?php elseif ($question['SID'] === 'PHONE'): ?>
                        <input id="<?= $question['INPUT_ID'] ?>"
                               class="field-input3 form-group1__field <?= $question['EXTRA_CLASS'] ?>"
                               type="tel"
                               name="<?= $question['INPUT_NAME'] ?>"
                               placeholder=" "
                               <?= $question['EXTRA_ATTRS'] ?>
                               <?= $question['REQUIRED'] === 'Y' ? 'required' : '' ?>>
                        <label class="form-group1__label" for="<?= $question['INPUT_ID'] ?>"><b>+7 </b>(999)-99-99</label>
                    <?php else: ?>
                        <input id="<?= $question['INPUT_ID'] ?>"
                               class="field-input1 form-group1__field <?= $question['EXTRA_CLASS'] ?>"
                               type="text"
                               name="<?= $question['INPUT_NAME'] ?>"
                               placeholder=""
                               <?= $question['EXTRA_ATTRS'] ?>
                               <?= $question['REQUIRED'] === 'Y' ? 'required' : '' ?>>
                        <label class="form-group1__label" for="<?= $question['INPUT_ID'] ?>"><?= $question['SID'] === 'NAME' ? 'Введите ФИО' : htmlspecialcharsbx($question['TITLE']) ?></label>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if ($arResult['FORM']['USE_CAPTCHA'] === 'Y'): ?>
            <div class="form-grid1__row">
                <p class="form-group1__title"><?= Loc::getMessage('FORM_CAPTCHA') ?> <span class="req">*</span></p>
                <div class="form-group1">
                    <input type="hidden" name="captcha_sid" value="<?= $arResult['CAPTCHA_CODE'] ?>"
                           id="<?= $arResult['FORM_UNIQUE_ID'] ?>_captcha_sid">
                    <div class="captcha-wrapper">
                        <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult['CAPTCHA_CODE'] ?>"
                             alt="CAPTCHA" class="captcha-image"
                             id="<?= $arResult['FORM_UNIQUE_ID'] ?>_captcha_image">
                        <button type="button" class="btn btn-refresh-captcha">↻</button>
                    </div>
                    <input type="text"
                           name="captcha_word"
                           id="<?= $arResult['FORM_UNIQUE_ID'] ?>_captcha_word"
                           class="field-input1 form-group1__field"
                           required>
                </div>
            </div>
        <?php endif; ?>

        <?php /* Блок согласия на обработку персональных данных — не менять */ ?>
        <div class="form-grid1__row form-grid1__row_sparse">
            <div class="form-group">
                <?php
                /** @global CMain $APPLICATION */
                $APPLICATION->IncludeComponent(
                    'bitrix:main.userconsent.request',
                    '',
                    [
                        'ID' => 2,
                        'IS_INSERTED' => 'Y',
                        'AUTO_SAVE' => 'Y',
                        'IS_CHECKED' => 'N',
                        'IS_LOADED' => 'Y',
                        'INPUT_NAME' => 'agreement',
                        'SUBMIT_EVENT_NAME' => 'contactUsFormSubmit' . $arResult['PREFIX_UNIQUE'],
                        'REPLACE' => [
                            'button_caption' => $arResult['FORM']['BUTTON'] ?: Loc::getMessage('FORM_SUBMIT_BUTTON'),
                        ],
                    ],
                    $component,
                    ['HIDE_ICONS' => 'Y']
                );
                ?>
            </div>
        </div>

        <div class="btn-form">
            <button type="submit" class="btn btn_arr btn_primary btn_big">
                <span><?= $arResult['FORM']['BUTTON'] ?: Loc::getMessage('FORM_SUBMIT_BUTTON') ?></span>
                <svg width="14" height="14" aria-hidden="true">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                </svg>
            </button>
        </div>
    </div>
</form>

<script>
    BX.ready(function() {
        let lastContactUsFormData<?= $arResult['PREFIX_UNIQUE'] ?> = null;

        class ContactUsForm<?= $arResult['PREFIX_UNIQUE'] ?> extends UniversalForm {
            onSuccess(message) {
                AW.showModalNotification(
                    '<?= Loc::getMessage('FORM_SUCCESS_TITLE') ?>',
                    '<?= Loc::getMessage('FORM_SUCCESS_MESSAGE') ?>',
                    '<?= Loc::getMessage('FORM_CLOSE_BUTTON') ?>'
                );
                if (this.successUrl) {
                    window.location.href = this.successUrl;
                } else {
                    const $form = $('#<?= $arResult['FORM_UNIQUE_ID'] ?>');
                    // Сбрасываем форму стандартным методом
                    $form[0].reset();
                    // Дополнительно очищаем кастомные элементы
                    $form.find('.file-user-cabinet__file').text('Файл не выбран').removeClass('file-selected');
                    // Удаляем классы ошибок валидации
                    $form.find('.form-group, .form-group1').removeClass('form-group_error');
                    $form.find('.form-group__error').remove();
                }
            }

            onError(errors) {
                let errorMessage = '';

                if (typeof errors === 'object' && errors !== null) {
                    const errorMessages = Object.values(errors);
                    errorMessage = errorMessages.join('<br>');
                } else if (Array.isArray(errors)) {
                    errorMessage = errors.join('<br>');
                } else {
                    errorMessage = errors || 'Произошла ошибка при отправке формы';
                }

                AW.showModalNotification(
                    '<?= Loc::getMessage('FORM_ERROR_TITLE') ?: 'Ошибка' ?>',
                    errorMessage,
                    '<?= Loc::getMessage('FORM_CLOSE_BUTTON') ?: 'Закрыть' ?>'
                );
            }
        }

        let universalForm<?= $arResult['PREFIX_UNIQUE'] ?> = new ContactUsForm<?= $arResult['PREFIX_UNIQUE'] ?>(<?= Json::encode($arResult['JS_PARAMS']) ?>);

        // Триггер валидации: сначала запускаем userconsent, отправку формы делаем после события save
        $(document).on('contactUsFormSubmit<?= $arResult['PREFIX_UNIQUE'] ?>', function(event, data) {
            if (!data || !data.form) {
                return;
            }

            lastContactUsFormData<?= $arResult['PREFIX_UNIQUE'] ?> = data;
            BX.onCustomEvent('contactUsFormSubmit<?= $arResult['PREFIX_UNIQUE'] ?>', []);
        });

        // Подписываемся на успешное сохранение согласия для конкретного чекбокса этой формы
        const formNode = document.getElementById('<?= $arResult['FORM_UNIQUE_ID'] ?>');
        if (formNode && BX.UserConsent) {
            const consentControl = BX.UserConsent.load(formNode);
            if (consentControl) {
                BX.addCustomEvent(
                    BX.UserConsent,
                    BX.UserConsent.events.save,
                    function(item) {
                        if (!lastContactUsFormData<?= $arResult['PREFIX_UNIQUE'] ?>) {
                            return;
                        }
                        if (item !== consentControl) {
                            return;
                        }

                        universalForm<?= $arResult['PREFIX_UNIQUE'] ?>.submitForm(
                            lastContactUsFormData<?= $arResult['PREFIX_UNIQUE'] ?>.form
                        );
                    }
                );
            }
        }
    });
</script>