BX.ready(function () {
    var tabMenuItems = document.querySelectorAll('.acroweb-options-tab-menu-item');
    var tabContents = document.querySelectorAll('.acroweb-options-tab-content');

    tabMenuItems.forEach(function (item) {
        item.addEventListener('click', function () {
            var tabName = this.getAttribute('data-tab');

            tabMenuItems.forEach(function (menuItem) {
                menuItem.classList.remove('active');
            });
            tabContents.forEach(function (content) {
                content.classList.remove('active');
            });

            this.classList.add('active');
            document.querySelector('.acroweb-options-tab-content[data-tab="' + tabName + '"]').classList.add('active');
        });
    });

    var colorThemeSelectors = document.querySelectorAll('.adm-color-theme-selector');
    colorThemeSelectors.forEach(function(selector) {
        var customColorInput = selector.nextElementSibling;
        var customColorPicker = selector.querySelector('.adm-color-theme-custom');
        var radioInputs = selector.querySelectorAll('input[type="radio"]');

        // Установка начального цвета для кружка custom
        if (customColorInput.value) {
            customColorPicker.style.backgroundColor = customColorInput.value;
        }

        if (customColorPicker) {
            BX.bind(customColorPicker, 'click', function(e) {
                e.preventDefault();
                if (BX.ColorPicker) {
                    var colorPicker = new BX.ColorPicker({
                        bindElement: customColorPicker,
                        onColorSelected: function(color) {
                            customColorInput.value = color;
                            customColorPicker.style.backgroundColor = color;
                            selector.querySelector('input[value="custom"]').checked = true;
                        }
                    });
                    colorPicker.open();
                } else {
                    console.error('BX.ColorPicker is not available');
                }
            });
        }

        // Обработчик изменения радио-кнопок
        radioInputs.forEach(function(radio) {
            BX.bind(radio, 'change', function() {
                if (this.value !== 'custom') {
                    customColorInput.value = ''; // Очищаем значение кастомного цвета
                    customColorPicker.style.backgroundColor = ''; // Сбрасываем цвет кружка
                }
            });
        });

        document.querySelectorAll('.adm-designed-file').forEach(function(fileInput) {
            var previewContainer = document.getElementById('preview_' + fileInput.id);
            var currentFile = fileInput.getAttribute('data-current-file');

            if (currentFile) {
                updatePreview(previewContainer, currentFile);
            }

            fileInput.addEventListener('change', function(event) {
                var file = event.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        updatePreview(previewContainer, e.target.result, file.name);
                    }
                    reader.readAsDataURL(file);
                } else {
                    previewContainer.innerHTML = '';
                }
            });
        });

        function updatePreview(container, src, fileName) {
            if (src.match(/\.(jpeg|jpg|gif|png)$/i) != null || src.startsWith('data:image/')) {
                container.innerHTML = '<img src="' + src + '" alt="Selected file">';
            } else {
                container.innerHTML = '<img src="/bitrix/images/fileman/types/file.gif" alt="File icon">' +
                    (fileName ? '<span>' + fileName + '</span>' : '');
            }
        }
    });
});