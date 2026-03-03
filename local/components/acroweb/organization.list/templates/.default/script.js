;(function(window) {
    'use strict';
    
    if (typeof window.BX === 'undefined') {
        return;
    }
    
    if (typeof BX.Acroweb === 'undefined') {
        BX.Acroweb = {};
    }
    
    BX.Acroweb.OrganizationList = {
        params: null,
        
        init: function(params) {
            this.params = params;
            this.bindEvents();
        },
        
        bindEvents: function() {
            this.bindFileUpload();
            this.bindVerify();
            this.bindDelete();
            this.bindModalInnInput();
            this.bindModalForm();
            this.bindModalClose();
        },
        
        bindFileUpload: function() {
            var self = this;
            document.querySelectorAll('[data-org-file]').forEach(function(input) {
                input.addEventListener('change', function() {
                    var orgId = this.getAttribute('data-org-file');
                    var fileInput = this;
                    
                    if (!this.files || !this.files[0]) {
                        return;
                    }
                    
                    var file = this.files[0];
                    var sizeMB = (file.size / 1024 / 1024).toFixed(2);
                    
                    // Показываем уведомление о начале загрузки
                    AW.showModalNotification('Загрузка', 'Загружается файл ' + file.name + ' (' + sizeMB + ' МБ)...', 'Закрыть');
                    
                    // Блокируем input
                    fileInput.disabled = true;
                    
                    // Создаем FormData для отправки файла и параметров
                    var formData = new FormData();
                    formData.append('file', file);
                    formData.append('organizationId', orgId);
                    formData.append('signedParameters', self.params.signedParameters);
                    
                    BX.ajax.runComponentAction('acroweb:organization.list', 'uploadCard', {
                        mode: 'class',
                        data: formData,
                        timeout: 60000 // 60 секунд для больших файлов
                    }).then(function(response) {
                        fileInput.disabled = false;
                        if (response.data && response.data.status === 'success') {
                            AW.showModalNotification('Успешно', response.data.message, 'Закрыть');
                            setTimeout(function() {
                                // Показываем лоадер перед перезагрузкой
                                document.querySelector('.organization-loader').classList.add('organization-loader_active');
                                location.reload();
                            }, 2000);
                        } else {
                            AW.showModalNotification('Ошибка', response.data?.message || 'Произошла ошибка', 'Закрыть');
                        }
                    }).catch(function(error) {
                        console.error(error);
                        fileInput.disabled = false;
                        AW.showModalNotification('Ошибка', 'Произошла ошибка при загрузке файла. Возможно файл слишком большой.', 'Закрыть');
                    });
                });
            });
        },
        
        bindVerify: function() {
            var self = this;
            document.querySelectorAll('[data-action-org="verify"]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var orgId = this.getAttribute('data-org-id');
                    var button = this;
                    
                    // Блокируем кнопку и меняем текст
                    button.disabled = true;
                    var originalText = button.textContent;
                    button.textContent = 'Отправка...';
                    
                    BX.ajax.runComponentAction('acroweb:organization.list', 'sendToVerify', {
                        mode: 'class',
                        data: {
                            organizationId: parseInt(orgId),
                            signedParameters: self.params.signedParameters
                        }
                    }).then(function(response) {
                        if (response.data && response.data.status === 'success') {
                            AW.showModalNotification('Успешно', response.data.message, 'Закрыть');
                            setTimeout(function() {
                                // Показываем лоадер перед перезагрузкой
                                document.querySelector('.organization-loader').classList.add('organization-loader_active');
                                location.reload();
                            }, 2000);
                        } else {
                            // Разблокируем кнопку при ошибке
                            button.disabled = false;
                            button.textContent = originalText;
                            AW.showModalNotification('Ошибка', response.data?.message || 'Произошла ошибка', 'Закрыть');
                        }
                    }).catch(function(error) {
                        console.error(error);
                        // Разблокируем кнопку при ошибке
                        button.disabled = false;
                        button.textContent = originalText;
                        AW.showModalNotification('Ошибка', 'Произошла ошибка', 'Закрыть');
                    });
                });
            });
        },
        
        bindDelete: function() {
            var self = this;
            document.querySelectorAll('[data-action-org="delete"]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    if (!confirm('Вы уверены, что хотите удалить эту организацию?')) {
                        return;
                    }
                    
                    var orgId = this.getAttribute('data-org-id');
                    var button = this;
                    
                    // Блокируем кнопку и меняем текст
                    button.disabled = true;
                    var originalText = button.textContent;
                    button.textContent = 'Удаление...';
                    
                    BX.ajax.runComponentAction('acroweb:organization.list', 'deleteOrganization', {
                        mode: 'class',
                        data: {
                            organizationId: parseInt(orgId),
                            signedParameters: self.params.signedParameters
                        }
                    }).then(function(response) {
                        if (response.data && response.data.status === 'success') {
                            AW.showModalNotification('Успешно', response.data.message, 'Закрыть');
                            setTimeout(function() {
                                // Показываем лоадер перед перезагрузкой
                                document.querySelector('.organization-loader').classList.add('organization-loader_active');
                                location.reload();
                            }, 2000);
                        } else {
                            // Разблокируем кнопку при ошибке
                            button.disabled = false;
                            button.textContent = originalText;
                            AW.showModalNotification('Ошибка', response.data?.message || 'Произошла ошибка', 'Закрыть');
                        }
                    }).catch(function(error) {
                        console.error(error);
                        // Разблокируем кнопку при ошибке
                        button.disabled = false;
                        button.textContent = originalText;
                        AW.showModalNotification('Ошибка', 'Произошла ошибка', 'Закрыть');
                    });
                });
            });
        },
        
        bindModalInnInput: function() {
            var self = this;
            var form = document.getElementById('add-organization-form');
            var orgFields = document.querySelectorAll('[data-org-field-modal]');
            var fileRows = form ? form.querySelectorAll('[data-org-card-row]') : [];
            var fileInput = form ? form.querySelector('[data-org-card-file]') : null;
            var fileLabel = form ? form.querySelector('.file-user-cabinet__file') : null;
            var defaultFileLabel = fileLabel ? fileLabel.textContent : 'Файл не выбран';
            var statusMessages = {
                "LIQUIDATING": "Организация ликвидируется по данным ЮГРЮЛ",
                "LIQUIDATED": "Организация ликвидирована по данным ЮГРЮЛ",
                "BANKRUPT": "Банкротство по данным ЮГРЮЛ",
                "REORGANIZING": "Организация в процессе присоединения к другому юрлицу, с последующей ликвидацией"
            };

            function setField(input, value) {
                if (!input) {
                    return;
                }
                input.value = value;
                if (value) {
                    input.classList.add('filled');
                } else {
                    input.classList.remove('filled');
                }
            }

            function resetFileInput() {
                if (fileInput) {
                    fileInput.value = '';
                }
                if (fileLabel) {
                    fileLabel.textContent = defaultFileLabel;
                }
            }

            function toggleFileRow(show) {
                if (!fileRows || !fileRows.length) {
                    return;
                }
                fileRows.forEach(function(row) {
                    if (show) {
                        row.classList.remove('hide');
                    } else {
                        row.classList.add('hide');
                    }
                });
                if (!show) {
                    resetFileInput();
                }
            }

            function showOrgFields() {
                orgFields.forEach(function(field) {
                    field.classList.remove('hide');
                });
            }

            function hideOrgFields() {
                orgFields.forEach(function(field) {
                    field.classList.add('hide');
                });
                resetFileInput();
            }

            function fillFromOrganization(organization) {
                if (!form) {
                    return;
                }

                setField(form.querySelector('[name="title"]'), organization.NAME || '');
                setField(form.querySelector('[name="kpp"]'), organization.KPP || '');
                setField(form.querySelector('[name="address"]'), organization.ADDRESS || '');

                showOrgFields();
                toggleFileRow(!(organization.HAS_FILE));
            }

            function fillFromDadata(company) {
                if (!form) {
                    return;
                }

                setField(form.querySelector('[name="title"]'), company.value || '');
                setField(form.querySelector('[name="kpp"]'), company.data.kpp || '');
                setField(
                    form.querySelector('[name="address"]'),
                    company.data.address?.unrestricted_value || ''
                );

                showOrgFields();
                toggleFileRow(true);
            }

            document.addEventListener('input', function(e) {
                if (!e.target.matches('[data-inn-input-modal]')) {
                    return;
                }

                var inn = e.target.value.trim();

                if (/^\d{10}$|^\d{12}$/.test(inn)) {
                    BX.ajax.runComponentAction('acroweb:organization.list', 'getInfoByInn', {
                        mode: 'class',
                        data: {
                            inn: inn,
                            signedParameters: self.params.signedParameters
                        }
                    }).then(function(response) {
                        if (response.data && response.data.status === 'success') {
                            var payload = response.data.data;

                            if (payload && payload.source === 'iblock' && payload.organization) {
                                fillFromOrganization(payload.organization);
                                return;
                            }

                            if (payload && payload.source === 'dadata') {
                                var suggestions = payload.suggestions;

                                if (suggestions && suggestions.length > 0) {
                                    var company = suggestions[0];

                                    if (company.data.state.status === "ACTIVE") {
                                        fillFromDadata(company);
                                    } else {
                                        var statusMessage = statusMessages[company.data.state.status] || 'Организация имеет недопустимый статус';
                                        AW.showModalNotification('Ошибка', statusMessage, 'Закрыть');
                                        hideOrgFields();
                                    }
                                } else {
                                    AW.showModalNotification('Ошибка', 'В данных ЮГРЮЛ нет информации по данной организации', 'Закрыть');
                                    hideOrgFields();
                                }
                            } else {
                                AW.showModalNotification('Ошибка', 'Не удалось получить данные организации', 'Закрыть');
                                hideOrgFields();
                            }
                        } else {
                            AW.showModalNotification('Ошибка', response.data?.message || 'Произошла ошибка', 'Закрыть');
                            hideOrgFields();
                        }
                    }).catch(function(error) {
                        console.error(error);
                        AW.showModalNotification('Ошибка', 'Произошла ошибка при получении данных', 'Закрыть');
                        hideOrgFields();
                    });
                } else if (inn === '') {
                    hideOrgFields();
                }
            });
        },
        
        bindModalForm: function() {
            var self = this;
            var addOrgForm = document.getElementById('add-organization-form');
            
            if (addOrgForm) {
                // Обработка выбора файла
                var fileInput = addOrgForm.querySelector('[data-org-card-file]');
                var fileLabel = addOrgForm.querySelector('.file-user-cabinet__file');
                
                if (fileInput && fileLabel) {
                    fileInput.addEventListener('change', function() {
                        if (this.files && this.files[0]) {
                            var file = this.files[0];
                            var sizeMB = (file.size / 1024 / 1024).toFixed(2);
                            fileLabel.textContent = file.name + ' (' + sizeMB + ' МБ)';
                        } else {
                            fileLabel.textContent = 'Файл не выбран';
                        }
                    });
                }
                
                // Обработчик события триггера валидации
                $(document).on('organization_add', function(event, data) {
                    if (data && data.form) {
                        var form = data.form;
                        var formData = new FormData(form);
                        var submitBtn = form.querySelector('button[type="submit"]');
                        
                        // Блокируем кнопку и показываем загрузку
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.textContent = 'Загрузка...';
                        }
                        
                        // Добавляем signedParameters в FormData
                        formData.append('signedParameters', self.params.signedParameters);
                        
                        BX.ajax.runComponentAction('acroweb:organization.list', 'addOrganization', {
                            mode: 'class',
                            data: formData,
                            timeout: 60000 // 60 секунд для больших файлов
                        }).then(function(response) {
                            if (response.data && response.data.status === 'success') {
                                AW.showModalNotification(
                                    self.params.messages.successTitle,
                                    self.params.messages.successMessage,
                                    self.params.messages.closeButton
                                );
                                
                                setTimeout(function() {
                                    // Показываем лоадер перед перезагрузкой
                                    document.querySelector('.organization-loader').classList.add('organization-loader_active');
                                    location.reload();
                                }, 2000);
                            } else {
                                AW.showModalNotification('Ошибка', response.data?.message || 'Произошла ошибка', 'Закрыть');
                                // Разблокируем кнопку
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.textContent = 'Сохранить';
                                }
                            }
                        }).catch(function(error) {
                            console.error(error);
                            AW.showModalNotification('Ошибка', 'Произошла ошибка при добавлении организации. Возможно файл слишком большой.', 'Закрыть');
                            // Разблокируем кнопку
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.textContent = 'Сохранить';
                            }
                        });
                    }
                });
            }
        },
        
        bindModalClose: function() {
            var closeBtn = document.querySelector('#add-organization [data-hystclose]');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    var form = document.getElementById('add-organization-form');
                    if (form) {
                        form.reset();
                        document.querySelectorAll('[data-org-field-modal]').forEach(function(field) {
                            field.classList.add('hide');
                        });
                    }
                });
            }
        }
    };
    
})(window);
