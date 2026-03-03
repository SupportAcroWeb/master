/**
 * Дополнительный функционал для корзины
 */
(function() {
	'use strict';

	BX.namespace('BX.Sale.BasketComponent.Custom');

	BX.Sale.BasketComponent.Custom = {
		/**
		 * Инициализация дополнительного функционала
		 */
		init: function() {
			this.bindClearBasketButton();
		},

		/**
		 * Привязка события к кнопке очистки корзины
		 */
		bindClearBasketButton: function() {
			var clearButton = document.querySelector('[data-entity="basket-clear-button"]');
			if (clearButton) {
				BX.bind(clearButton, 'click', function() {
					if (confirm(BX.message('SBB_BASKET_CLEAR_CONFIRM'))) {
						BX.Sale.BasketComponent.clearBasket();
					}
				});
			}
		}
	};

	/**
	 * Расширение базового компонента корзины
	 */
	if (BX.Sale && BX.Sale.BasketComponent) {
		var originalInit = BX.Sale.BasketComponent.init;
		
		BX.Sale.BasketComponent.init = function(parameters) {
			originalInit.call(this, parameters);
			BX.Sale.BasketComponent.Custom.init();
		};

		/**
		 * Метод очистки корзины через AJAX
		 */
		BX.Sale.BasketComponent.clearBasket = function() {
			BX.ajax({
				method: 'POST',
				dataType: 'json',
				url: window.location.href,
				data: {
					action: 'clearBasket',
					sessid: BX.bitrix_sessid()
				},
				onsuccess: function(result) {
					if (result && result.status === 'success') {
						window.location.reload();
					} else {
						console.error('Ошибка очистки корзины:', result.errors);
					}
				},
				onfailure: function() {
					console.error('Ошибка AJAX запроса очистки корзины');
				}
			});
		};
	}
})();
