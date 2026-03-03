/**
 * Скрипт автоматического переключения кнопок "Купить" → "В корзине"
 * 
 * Производительность:
 * - Один проход по DOM на старте
 * - Поиск строго по data-product-id из серверного массива
 * - Минимум обращений к DOM при изменении кнопки
 * - Подписка на нативное событие Bitrix OnBasketChange
 */

(function () {
    'use strict';

    /**
     * Переключает кнопку в режим "В корзине"
     * 
     * @param {HTMLElement} button - Элемент кнопки
     */
    function switchButton(button) {
        if (!button) {
            return;
        }

        try {
            // Клонируем кнопку (это удалит все обработчики событий Bitrix)
            const newButton = button.cloneNode(true);
            
            // Меняем текст
            const textNode = newButton.querySelector('.btn-text') || newButton;
            if (textNode.textContent) {
                textNode.textContent = 'В корзине';
            } else if (newButton.value) {
                newButton.value = 'В корзине';
            }

            // Меняем href (если это ссылка)
            if (newButton.tagName === 'A') {
                newButton.href = '/personal/basket/';
                // Удаляем onclick и другие атрибуты, связанные с добавлением в корзину
                newButton.removeAttribute('onclick');
                newButton.removeAttribute('data-add-basket');
            } else if (newButton.dataset.href) {
                newButton.dataset.href = '/personal/basket/';
            }

            // Меняем класс
            newButton.className = 'btn btn_black';

            // Отключаем повторное добавление
            newButton.dataset.inBasket = '1';
            
            // Удаляем data-атрибуты Bitrix для корзины
            newButton.removeAttribute('data-entity');
            
            // Добавляем обработчик для перехода в корзину
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                window.location.href = '/personal/basket/';
            });

            // Заменяем старую кнопку на новую (без мигания)
            if (button.parentNode) {
                button.parentNode.replaceChild(newButton, button);
            }
        } catch (error) {
            console.error('[CartButtonSwitcher] Ошибка переключения кнопки:', error);
        }
    }

    /**
     * Инициализация компонента
     */
    function init() {
        const root = document.querySelector('[data-role="cart-button-switcher"]');
        if (!root) {
            console.warn('[CartButtonSwitcher] Контейнер компонента не найден');
            return;
        }

        let basketIds;
        try {
            basketIds = new Set(JSON.parse(root.dataset.basketIds || '[]'));
        } catch (error) {
            console.error('[CartButtonSwitcher] Ошибка парсинга basketIds:', error);
            return;
        }

        // Один проход по всем кнопкам с data-product-id
        const allButtons = document.querySelectorAll('[data-product-id]');
        
        allButtons.forEach(function (button) {
            const productId = parseInt(button.dataset.productId, 10);
            
            // Если товар в корзине и кнопка ещё не переключена
            if (basketIds.has(productId) && button.dataset.inBasket !== '1') {
                switchButton(button);
            }
        });

        // Подписываемся на события добавления в корзину
        subscribeToBasketEvents();
    }

    /**
     * Получение актуального списка товаров в корзине через AJAX
     */
    function updateBasketIds(callback) {
        if (typeof BX === 'undefined' || !BX.ajax || !BX.ajax.runComponentAction) {
            console.error('[CartButtonSwitcher] BX.ajax.runComponentAction недоступен');
            return;
        }

        BX.ajax.runComponentAction('acroweb:cart.buttonswitcher', 'getBasketIds', {
            mode: 'class'
        }).then(function (response) {
            if (response && response.data && typeof callback === 'function') {
                callback(response.data);
            }
        }).catch(function (error) {
            console.error('[CartButtonSwitcher] Ошибка получения списка корзины:', error);
        });
    }

    /**
     * Подписка на события изменения корзины
     */
    function subscribeToBasketEvents() {
        let lastClickedProductId = null;

        // Отслеживаем клики по кнопкам "Купить" через делегирование
        document.addEventListener('click', function (event) {
            const target = event.target.closest('[data-product-id]');
            
            if (!target || target.dataset.inBasket === '1') {
                return;
            }

            const productId = parseInt(target.dataset.productId, 10);
            if (!productId) {
                return;
            }

            // Запоминаем ID последнего кликнутого товара
            lastClickedProductId = productId;
        });

        // Стандартное событие Bitrix при добавлении товара
        if (typeof BX !== 'undefined' && BX.addCustomEvent) {
            BX.addCustomEvent('OnBasketChange', function () {
                // Если знаем ID товара из последнего клика - переключаем сразу
                if (lastClickedProductId) {
                    const button = document.querySelector('[data-product-id="' + lastClickedProductId + '"]');
                    if (button && button.dataset.inBasket !== '1') {
                        switchButton(button);
                    }
                    lastClickedProductId = null;
                } else {
                    // Если не знаем ID - обновляем весь список через AJAX
                    updateBasketIds(function (basketIds) {
                        const buttons = document.querySelectorAll('[data-product-id]');
                        buttons.forEach(function (btn) {
                            const productId = parseInt(btn.dataset.productId, 10);
                            if (basketIds.indexOf(productId) !== -1 && btn.dataset.inBasket !== '1') {
                                switchButton(btn);
                            }
                        });
                    });
                }
            });
        }
    }

    // Запуск после полной загрузки DOM и инициализации BX
    if (typeof BX !== 'undefined' && BX.ready) {
        BX.ready(init);
    } else if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

