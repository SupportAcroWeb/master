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

    // Обработка ввода ИНН
    $form.on('input', '[name="ORG_INN"]', function () {
        const $input = $(this);
        
        // Убираем все кроме цифр
        this.value = this.value.replace(/[^0-9]/g, '');
        
        const inn = $input.val().trim();
        
        if (inn.length === 10 || inn.length === 12) {
            // Валидный ИНН - проверяем его
            checkINN(inn, $input);
        } else if (inn.length > 0) {
            // Невалидная длина ИНН
            showInnError('Номер ИНН должен состоять из 10 или 12 цифр.', $input);
            hideOrganizationFields();
        } else {
            // Пустое поле - просто скрываем поля организации
            clearInnError($input);
            hideOrganizationFields();
        }
    });

    /**
     * Проверка существования организации с данным ИНН
     * 
     * @param {string} inn ИНН организации
     * @param {jQuery} $input Поле ввода ИНН
     */
    function checkINN(inn, $input) {
        $('.form-loader').addClass('form-loader_active');
        
        BX.ajax.runAction('acroweb:mage.api.innajax.checkINN', {
            data: {
                inn: inn
            }
        }).then(function (response) {
            $('.form-loader').removeClass('form-loader_active');
            
            // Битрикс возвращает данные в response.data
            if (response.data && response.data.status === 'success' && response.data.data) {
                clearInnError($input);
                getOrganizationDataByInn(inn, $input);
            } else if (response.data && response.data.status === 'error') {
                // Ошибка от сервера
                showInnError(response.data.message || 'Ошибка при проверке ИНН', $input);
                hideOrganizationFields();
            } else {
                showInnError('Ошибка при проверке ИНН. Попробуйте позже.', $input);
                hideOrganizationFields();
            }
        }).catch(function (response) {
            console.error('Ошибка проверки ИНН:', response);
            $('.form-loader').removeClass('form-loader_active');
            showInnError('Ошибка при проверке ИНН. Попробуйте позже.', $input);
        });
    }

    /**
     * Получить данные организации по ИНН из DaData
     * 
     * @param {string} inn ИНН организации
     * @param {jQuery} $input Поле ввода ИНН
     */
    function getOrganizationDataByInn(inn, $input) {
        $('.form-loader').addClass('form-loader_active');
        
        BX.ajax.runAction('acroweb:mage.api.innajax.getInfo', {
            data: {
                inn: inn
            }
        }).then(function (response) {
            $('.form-loader').removeClass('form-loader_active');
            
            // Битрикс возвращает данные в response.data
            if (response.data && response.data.status === 'success' && response.data.data) {
                const payload = response.data.data;
                clearInnError($input);

                if (payload.source === 'iblock' && payload.organization) {
                    fillOrganizationFieldsFromIblock(payload.organization);
                } else if (payload.source === 'dadata') {
                    const statusMessages = {
                        "LIQUIDATING": "Организация ликвидируется по данным ЮГРЮЛ",
                        "LIQUIDATED": "Организация ликвидирована по данным ЮГРЮЛ",
                        "BANKRUPT": "Банкротство по данным ЮГРЮЛ",
                        "REORGANIZING": "Организация в процессе присоединения к другому юрлицу, с последующей ликвидацией"
                    };
                    
                    const suggestions = payload.suggestions;
                    
                    if (suggestions && suggestions.length > 0) {
                        const company = suggestions[0];
                        
                        if (company.data.state.status === "ACTIVE") {
                            fillOrganizationFieldsFromDadata(company, inn);
                        } else {
                            const errorMessage = statusMessages[company.data.state.status] || 'Организация имеет недопустимый статус';
                            showInnError(errorMessage, $input);
                            hideOrganizationFields();
                        }
                    } else {
                        showInnError('В данных ЮГРЮЛ нет информации по данной организации', $input);
                        hideOrganizationFields();
                    }
                } else {
                    showInnError('Не удалось получить данные по организации', $input);
                    hideOrganizationFields();
                }
            } else if (response.data && response.data.status === 'error') {
                // Ошибка от сервера
                showInnError(response.data.message || 'Ошибка при получении данных организации', $input);
                hideOrganizationFields();
            } else {
                console.error('Некорректный ответ:', response);
                showInnError('Ошибка при получении данных организации', $input);
                hideOrganizationFields();
            }
        }).catch(function (response) {
            console.error('Ошибка запроса к DaData:', response);
            $('.form-loader').removeClass('form-loader_active');
            showInnError('Ошибка при получении данных организации', $input);
            hideOrganizationFields();
        });
    }

    /**
     * Заполнить поля организации данными из DaData
     * 
     * @param {object} company Данные компании
     * @param {string} inn ИНН
     */
    function fillOrganizationFieldsFromDadata(company, inn) {
        // Заполняем название организации
        if (company.value) {
            $('[name="ORG_NAME"]')
                .val(company.value)
                .addClass('filled')
                .closest('.form-grid1__row')
                .removeClass('hide')
                .show();
        }
        
        // Заполняем КПП
        if (company.data.kpp) {
            $('[name="ORG_KPP"]')
                .val(company.data.kpp)
                .addClass('filled')
                .closest('.form-grid1__row')
                .removeClass('hide')
                .show();
        }
        
        // Заполняем юридический адрес
        if (company.data.address?.unrestricted_value) {
            $('[name="ORG_UR_ADDRESS"]')
                .val(company.data.address.unrestricted_value)
                .addClass('filled')
                .closest('.form-grid1__row')
                .removeClass('hide')
                .show();
        }
        
        toggleFileField(true);
        
        // Показываем поле для дополнительной информации
        $('[name="UF_DOP_INFO"]')
            .closest('.form-grid1__row')
            .removeClass('hide')
            .show();
    }

    /**
     * Заполнить поля организации данными из инфоблока
     *
     * @param {object} organization Данные организации
     */
    function fillOrganizationFieldsFromIblock(organization) {
        if (!organization) {
            return;
        }

        const hasFile = Number(organization.FILE_ID || 0) > 0;

        if (organization.NAME) {
            $('[name="ORG_NAME"]')
                .val(organization.NAME)
                .addClass('filled')
                .closest('.form-grid1__row')
                .removeClass('hide')
                .show();
        }

        if (organization.KPP) {
            $('[name="ORG_KPP"]')
                .val(organization.KPP)
                .addClass('filled')
                .closest('.form-grid1__row')
                .removeClass('hide')
                .show();
        }

        if (organization.ADDRESS) {
            $('[name="ORG_UR_ADDRESS"]')
                .val(organization.ADDRESS)
                .addClass('filled')
                .closest('.form-grid1__row')
                .removeClass('hide')
                .show();
        }

        if (hasFile) {
            $('[name="ORG_FILE"]').val('').removeClass('filled');
        }

        toggleFileField(!hasFile);

        $('[name="UF_DOP_INFO"]')
            .closest('.form-grid1__row')
            .removeClass('hide')
            .show();
    }

    /**
     * Скрыть все поля организации
     */
    function hideOrganizationFields() {
        $('[name="ORG_NAME"]').val('').removeClass('filled');
        $('[name="ORG_KPP"]').val('').removeClass('filled');
        $('[name="ORG_UR_ADDRESS"]').val('').removeClass('filled');
        $('[name="UF_DOP_INFO"]').val('');
        
        // Скрываем поля с атрибутом data-org-field
        $('[data-org-field]').addClass('hide').hide();
    }

    /**
     * Показать или скрыть поле загрузки файла
     *
     * @param {boolean} show Нужно ли показывать поле
     */
    function toggleFileField(show) {
        const $row = $('[name="ORG_FILE"]').closest('.form-grid1__row');

        if (show) {
            $row.removeClass('hide').show();
        } else {
            $row.addClass('hide').hide();
        }
    }

    /**
     * Показать ошибку в поле ИНН
     * 
     * @param {string} message Текст ошибки
     * @param {jQuery} $input Поле ввода
     */
    function showInnError(message, $input) {
        const $formGroup = $input.closest('.form-group1');
        
        $formGroup.addClass('form-group_error');
        $formGroup.find('.form-group__error, .error').remove();
        $formGroup.append('<label class="form-group__error error">' + message + '</label>');
    }

    /**
     * Очистить ошибку в поле ИНН
     * 
     * @param {jQuery} $input Поле ввода
     */
    function clearInnError($input) {
        const $formGroup = $input.closest('.form-group1');
        
        $formGroup.removeClass('form-group_error');
        $formGroup.find('.form-group__error, .error').remove();
    }

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
        // Останавливаем стандартную отправку формы
        if (data && data.event) {
            data.event.preventDefault();
        }
        
        if (!data || !data.form) {
            return;
        }
        
        // Включаем лоадер сразу при начале обработки
        $('.form-loader').addClass('form-loader_active');
        
        const $form = $(data.form);
        const inn = $form.find('input[name="ORG_INN"]').val().trim();
        const $innInput = $form.find('[name="ORG_INN"]');
        const password = $form.find('input[name="REGISTER[PASSWORD]"]').val();
        const confirmPassword = $form.find('input[name="REGISTER[CONFIRM_PASSWORD]"]').val();
        const $passwordInput = $form.find('input[name="REGISTER[PASSWORD]"]');
        const $confirmPasswordInput = $form.find('input[name="REGISTER[CONFIRM_PASSWORD]"]');
        
        // Проверка наличия ИНН
        if (!inn) {
            $('.form-loader').removeClass('form-loader_active');
            showInnError('Пожалуйста, заполните ИНН организации', $innInput);
            return;
        }
        
        // Проверка длины ИНН
        if (inn.length !== 10 && inn.length !== 12) {
            $('.form-loader').removeClass('form-loader_active');
            showInnError('Номер ИНН должен состоять из 10 или 12 цифр.', $innInput);
            return;
        }
        
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
        
        // Повторная проверка ИНН через API перед отправкой
        checkINNBeforeSubmit(inn, $innInput, data.form);
    });
    
    /**
     * Проверка ИНН перед отправкой формы
     * Последовательно: 1) DaData 2) Проверка на существование в БД
     * 
     * @param {string} inn ИНН организации
     * @param {jQuery} $input Поле ввода ИНН
     * @param {HTMLFormElement} form Форма для отправки
     */
    function checkINNBeforeSubmit(inn, $input, form) {
        // Шаг 1: Проверка через DaData
        BX.ajax.runAction('acroweb:mage.api.innajax.getInfo', {
            data: {
                inn: inn
            }
        }).then(function (response) {
            if (response.data && response.data.status === 'success' && response.data.data) {
                const payload = response.data.data;
                const statusMessages = {
                    "LIQUIDATING": "Организация ликвидируется по данным ЮГРЮЛ",
                    "LIQUIDATED": "Организация ликвидирована по данным ЮГРЮЛ",
                    "BANKRUPT": "Банкротство по данным ЮГРЮЛ",
                    "REORGANIZING": "Организация в процессе присоединения к другому юрлицу, с последующей ликвидацией"
                };

                if (payload.source === 'iblock') {
                    checkINNInDatabase(inn, $input, form);
                    return;
                }

                if (payload.source === 'dadata') {
                    const suggestions = payload.suggestions;
                    
                    if (suggestions && suggestions.length > 0) {
                        const company = suggestions[0];
                        
                        if (company.data.state.status === "ACTIVE") {
                            checkINNInDatabase(inn, $input, form);
                        } else {
                            $('.form-loader').removeClass('form-loader_active');
                            const errorMessage = statusMessages[company.data.state.status] || 'Организация имеет недопустимый статус';
                            showInnError(errorMessage, $input);
                            hideOrganizationFields();
                        }
                    } else {
                        $('.form-loader').removeClass('form-loader_active');
                        showInnError('В данных ЮГРЮЛ нет информации по данной организации', $input);
                        hideOrganizationFields();
                    }
                } else {
                    $('.form-loader').removeClass('form-loader_active');
                    showInnError('Не удалось получить данные организации', $input);
                    hideOrganizationFields();
                }
            } else {
                $('.form-loader').removeClass('form-loader_active');
                showInnError(response.data?.message || 'Ошибка при получении данных организации', $input);
                hideOrganizationFields();
            }
        }).catch(function (response) {
            $('.form-loader').removeClass('form-loader_active');
            showInnError('Ошибка при получении данных организации', $input);
            hideOrganizationFields();
        });
    }
    
    /**
     * Проверка ИНН на существование в базе данных
     * 
     * @param {string} inn ИНН организации
     * @param {jQuery} $input Поле ввода ИНН
     * @param {HTMLFormElement} form Форма для отправки
     */
    function checkINNInDatabase(inn, $input, form) {
        BX.ajax.runAction('acroweb:mage.api.innajax.checkINN', {
            data: {
                inn: inn
            }
        }).then(function (response) {
            if (response.data && response.data.status === 'success') {
                if (!form.querySelector('input[name="register_submit_button"]')) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'register_submit_button';
                    hiddenInput.value = 'Y';
                    form.appendChild(hiddenInput);
                }
                form.submit();
            } else {
                $('.form-loader').removeClass('form-loader_active');
                showInnError(response.data?.message || 'Ошибка при проверке ИНН', $input);
                hideOrganizationFields();
            }
        }).catch(function (response) {
            $('.form-loader').removeClass('form-loader_active');
            showInnError('Ошибка при проверке ИНН. Попробуйте позже.', $input);
        });
    }
});

})();
