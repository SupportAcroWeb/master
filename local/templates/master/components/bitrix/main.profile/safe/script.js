$(document).ready(function () {
	$(document).on('password_save', function(event, data) {
		if (data && data.form) {
			// Проверяем, что это именно форма смены пароля
			var $form = $(data.form);
			if ($form.find('input[name="PASSWORD_FORM"]').length > 0) {
				data.form.submit();
			}
		}
	});
});
