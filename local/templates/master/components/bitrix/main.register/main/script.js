/**
 * Обработка формы регистрации
 */
(function() {
    'use strict';

$(document).ready(function () {
    const $form = $('form[name="regform"]');
    
    if (!$form.length) {
        return;
    }

    let lastRegisterFormData = null;

    // Синхронизация EMAIL с LOGIN
    $form.on('input', 'input[name="REGISTER[EMAIL]"]', function () {
        const email = $(this).val();
        $form.find('input[name="REGISTER[LOGIN]"]').val(email);
    });

    /**
     * Показать ошибку в произвольном поле
     * 
     * @param {string} message Текст ошибки
     * @param {jQuery} $input Поле ввода
     */
    function showFieldError(message, $input) {
        const $formGroup = $input.closest('.form-group1');
        
        $formGroup.addClass('form-group_error');
        $formGroup.find('.form-group__error, .error').remove();
        $formGroup.append('<label class="form-group__error error">' + message + '</label>');
    }

    /**
     * Обработчик события валидации формы регистрации
     */
    $(document).on('register_submit', function(event, data) {
        if (!data || !data.form) {
            return;
        }
        
        // Включаем лоадер сразу при начале обработки
        $('.form-loader').addClass('form-loader_active');
        
        const $form = $(data.form);
        const password = $form.find('input[name="REGISTER[PASSWORD]"]').val();
        const confirmPassword = $form.find('input[name="REGISTER[CONFIRM_PASSWORD]"]').val();
        const $passwordInput = $form.find('input[name="REGISTER[PASSWORD]"]');
        const $confirmPasswordInput = $form.find('input[name="REGISTER[CONFIRM_PASSWORD]"]');
        
        // Проверка совпадения паролей
        if (password !== confirmPassword) {
            $('.form-loader').removeClass('form-loader_active');
            showFieldError('Пароли не совпадают', $confirmPasswordInput);
            return;
        }
        
        // Проверка минимальной длины пароля
        if (password.length < 6) {
            $('.form-loader').removeClass('form-loader_active');
            showFieldError('Пароль должен содержать не менее 6 символов', $passwordInput);
            return;
        }

        const bx = window.BX;
        // Все проверки пройдены — отправка делается только после сохранения согласия
        // (bitrix:main.userconsent.request слушает BX custom event с этим именем).
        if (bx && bx.UserConsent) {
            lastRegisterFormData = data;
            $('.form-loader').removeClass('form-loader_active');
            bx.onCustomEvent('register_submit', []);
            return;
        }

        // fallback: если userconsent не инициализирован, отправляем как раньше
        data.form.submit();
    });

    const bx = window.BX;
    const formNode = document.querySelector('form[name="regform"]');
    if (formNode && bx && bx.UserConsent) {
        const consentControl = bx.UserConsent.load(formNode);
        if (consentControl) {
            bx.addCustomEvent(
                bx.UserConsent,
                bx.UserConsent.events.save,
                function(item) {
                    if (!lastRegisterFormData) {
                        return;
                    }
                    if (item !== consentControl) {
                        return;
                    }

                    const formToSubmit = lastRegisterFormData.form;
                    lastRegisterFormData = null;

                    // Важно: при вызове form.submit() браузер может не передать
                    // data-ключ сабмит-кнопки (submitter), а сервер main.register
                    // проверяет isset($_POST['register_submit_button']).
                    // Поэтому добавляем hidden-поле гарантированно.
                    if (!formToSubmit.querySelector('input[type="hidden"][name="register_submit_button"]')) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'register_submit_button';
                        hiddenInput.value = 'Y';
                        formToSubmit.appendChild(hiddenInput);
                    }

                    $('.form-loader').addClass('form-loader_active');
                    formToSubmit.submit();
                }
            );
        }
    }
});

})();
