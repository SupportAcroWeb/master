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

    });
});