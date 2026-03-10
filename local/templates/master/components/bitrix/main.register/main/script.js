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

        // Все проверки пройдены — отправляем форму стандартным способом
        data.form.submit();
    });
});

})();
