$(document).ready(function () {
	/**
	 * Синхронизация EMAIL с LOGIN
	 */
	$('body').on('change', 'input[name="EMAIL"]', function (e) {
		var email = $(this).val();
		if ($('input[name="LOGIN"]').length) {
			$('input[name="LOGIN"]').val(email);
		}
	});

	/**
	 * Парсинг ФИО в отдельные поля
	 * Умный алгоритм разбиения: Фамилия Имя Отчество
	 */
	function parseFIO(fullName) {
		// Убираем лишние пробелы и разбиваем по пробелам
		var parts = fullName.trim().replace(/\s+/g, ' ').split(' ');
		var result = {
			lastName: '',
			firstName: '',
			secondName: ''
		};

		if (parts.length === 1) {
			// Только одно слово - считаем именем
			result.firstName = parts[0];
		} else if (parts.length === 2) {
			// Два слова - Фамилия Имя
			result.lastName = parts[0];
			result.firstName = parts[1];
		} else if (parts.length >= 3) {
			// Три и более слов - Фамилия Имя Отчество (остальное к отчеству)
			result.lastName = parts[0];
			result.firstName = parts[1];
			result.secondName = parts.slice(2).join(' ');
		}

		return result;
	}

	/**
	 * Обработчик изменения поля ФИО
	 */
	$('body').on('input', '[data-fio-input]', function () {
		var $input = $(this);
		var fullName = $input.val();
		var $form = $input.closest('form');
		
		// Парсим ФИО
		var fio = parseFIO(fullName);
		
		// Обновляем скрытые поля
		$form.find('[data-fio-last]').val(fio.lastName);
		$form.find('[data-fio-name]').val(fio.firstName);
		$form.find('[data-fio-second]').val(fio.secondName);
	});

	/**
	 * Обработчик отправки формы профиля
	 */
	$(document).on('profile_save', function(event, data) {
		if (data && data.form) {
			// Проверяем, что это именно форма профиля
			var $form = $(data.form);
			if ($form.find('input[name="PROFILE_FORM"]').length > 0) {
				// Перед отправкой еще раз обновляем скрытые поля ФИО
				var $fioInput = $form.find('[data-fio-input]');
				if ($fioInput.length) {
					var fio = parseFIO($fioInput.val());
					$form.find('[data-fio-last]').val(fio.lastName);
					$form.find('[data-fio-name]').val(fio.firstName);
					$form.find('[data-fio-second]').val(fio.secondName);
				}
				
				data.form.submit();
			}
		}
	});
});