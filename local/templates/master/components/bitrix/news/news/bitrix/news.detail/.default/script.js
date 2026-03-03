/**
 * Виджет "Поделиться" с интеграцией Яндекс.Поделиться
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const shareButton = document.getElementById('share-button');
        const shareWidget = document.getElementById('ya-share-widget');

        if (!shareButton || !shareWidget) {
            return;
        }

        let widgetInitialized = false;
        let isOpen = false;

        /**
         * Инициализирует виджет Яндекс.Поделиться
         */
        function initializeWidget() {
            if (typeof Ya !== 'undefined' && Ya.share2) {
                Ya.share2('#ya-share-widget', {
                    theme: {
                        services: 'vkontakte,odnoklassniki,telegram,whatsapp',
                        limit: 4,
                        bare: true
                    }
                });
                widgetInitialized = true;
            }
        }

        /**
         * Переключает отображение виджета
         */
        function toggleWidget() {
            if (!isOpen) {
                shareButton.style.display = 'none';
                shareWidget.style.display = 'block';
                isOpen = true;
            } else {
                shareWidget.style.display = 'none';
                shareButton.style.display = 'flex';
                isOpen = false;
            }
        }

        /**
         * Закрывает виджет
         */
        function closeWidget() {
            shareWidget.style.display = 'none';
            shareButton.style.display = 'flex';
            isOpen = false;
        }

        // Обработчик клика по кнопке "Поделиться"
        shareButton.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (!widgetInitialized) {
                initializeWidget();
            }

            toggleWidget();
        });

        // Закрытие виджета при клике вне его
        document.addEventListener('click', function (e) {
            if (isOpen && !shareWidget.contains(e.target) && !shareButton.contains(e.target)) {
                closeWidget();
            }
        });
    });
})();

