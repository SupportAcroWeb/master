$(document).ready(function () {
    // Синхронизация EMAIL с LOGIN, как в регистрации
    $('body').on('change', 'input[name="EMAIL"]', function () {
        var email = $(this).val();
        var $login = $('input[name="LOGIN"]');

        if ($login.length) {
            $login.val(email);
        }
    });

    // Обработчик события валидации формы профиля
    $(document).on('profile_save', function (event, data) {
        if (!data || !data.form) {
            return;
        }

        data.form.submit();
    });
});