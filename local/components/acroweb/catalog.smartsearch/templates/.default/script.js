/**
 * Компонент быстрого поиска по каталогу
 * @package Acroweb
 */
(function() {
    'use strict';

    if (typeof BX.Acroweb === 'undefined') {
        BX.Acroweb = {};
    }

    /**
     * Конструктор компонента поиска
     * @param {Object} params Параметры компонента
     */
    BX.Acroweb.CatalogSmartSearch = function(params) {
        this.componentId = params.componentId;
        this.componentName = params.componentName;
        this.signedParameters = params.signedParameters;
        this.minQueryLength = params.minQueryLength || 2;
        this.debounceDelay = params.debounceDelay || 300;
        this.itemsLimit = params.itemsLimit || 5;
        
        this.container = document.getElementById(this.componentId);
        if (!this.container) {
            console.error('Контейнер компонента не найден');
            return;
        }

        this.input = this.container.querySelector('.searchbox__input') || this.container.querySelector('.header-search__input');
        this.clearBtn = this.container.querySelector('.searchbox__clear') || this.container.querySelector('.header-search__clear');
        this.dropdown = this.container.querySelector('.header-search-dd');
        
        this.debounceTimer = null;
        this.currentRequest = null;
        this.isLoading = false;
        this.currentQuery = '';
        this.currentOffset = 0;

        this.init();
    };

    BX.Acroweb.CatalogSmartSearch.prototype = {
        /**
         * Инициализация компонента
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * Привязка событий
         */
        bindEvents: function() {
            var self = this;
            
            // Находим кнопку поиска
            this.submitBtn = this.container.querySelector('.searchbox__btn') || this.container.querySelector('.header-search__btn');
            
            // Изначально делаем кнопку неактивной
            if (this.submitBtn) {
                this.submitBtn.disabled = true;
            }

            if (!this.input) {
                console.error('Поле ввода поиска не найдено');
                return;
            }

            // Событие ввода текста
            BX.bind(this.input, 'input', function() {
                self.onInput();
                self.updateSubmitButton();
            });

            // Событие фокуса
            BX.bind(this.input, 'focus', function() {
                if (self.input.value.length >= self.minQueryLength) {
                    self.showDropdown();
                }
            });

            // Кнопка очистки
            if (this.clearBtn) {
                BX.bind(this.clearBtn, 'click', function() {
                    self.clearSearch();
                });
            }
            
            // Предотвращаем отправку формы если запрос слишком короткий
            BX.bind(this.container, 'submit', function(e) {
                var query = self.input.value.trim();
                if (query.length < self.minQueryLength) {
                    e.preventDefault();
                    return false;
                }
            });

            // Закрытие по клику вне области
            BX.bind(document, 'click', function(e) {
                if (!self.container.contains(e.target)) {
                    self.hideDropdown();
                }
            });

            // Закрытие по ESC
            BX.bind(document, 'keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    self.hideDropdown();
                    self.input.blur();
                }
            });
        },
        
        /**
         * Обновление состояния кнопки "Найти"
         */
        updateSubmitButton: function() {
            if (!this.submitBtn) {
                return;
            }
            
            var query = this.input.value.trim();
            this.submitBtn.disabled = query.length < this.minQueryLength;
        },

        /**
         * Обработчик ввода текста
         */
        onInput: function() {
            var self = this;
            var query = this.input.value.trim();

            // Показываем/скрываем кнопку очистки
            if (this.clearBtn) {
                if (query.length > 0) {
                    this.clearBtn.style.display = 'block';
                } else {
                    this.clearBtn.style.display = 'none';
                }
            }
            if (query.length === 0) {
                this.hideDropdown();
                return;
            }

            // Debounce
            clearTimeout(this.debounceTimer);
            
            if (query.length < this.minQueryLength) {
                this.hideDropdown();
                return;
            }

            this.debounceTimer = setTimeout(function() {
                self.search(query);
            }, this.debounceDelay);
        },

        /**
         * Выполнение поиска
         * @param {string} query Поисковый запрос
         * @param {number} offset Смещение для пагинации
         * @param {boolean} append Добавлять к существующим результатам
         */
        search: function(query, offset, append) {
            var self = this;
            offset = offset || 0;
            append = append || false;

            // Сохраняем текущий запрос
            this.currentQuery = query;
            this.currentOffset = offset;

            // Отменяем предыдущий запрос
            if (this.currentRequest) {
                this.currentRequest.abort();
            }

            this.isLoading = true;
            
            if (!append) {
                this.showLoading();
            }

            // Выполняем ajax-запрос
            this.currentRequest = BX.ajax.runComponentAction(
                this.componentName,
                'search',
                {
                    mode: 'class',
                    signedParameters: this.signedParameters,
                    data: {
                        query: query,
                        offset: offset
                    }
                }
            ).then(
                function(response) {
                    self.isLoading = false;
                    self.currentRequest = null;
                    
                    if (response.status === 'success' && response.data) {
                        if (response.data.error) {
                            self.showError(response.data.error);
                        } else {
                            self.renderResults(response.data, append);
                        }
                    }
                },
                function(error) {
                    self.isLoading = false;
                    self.currentRequest = null;
                    
                    if (error && error.status !== 0) { // 0 = запрос отменён
                        console.error('Ошибка поиска:', error);
                        self.showError('Произошла ошибка при поиске');
                    }
                }
            );
        },

        /**
         * Отрисовка результатов поиска
         * @param {Object} response Ответ от сервера с HTML
         * @param {boolean} append Добавлять к существующим результатам
         */
        renderResults: function(response, append) {
            var self = this;

            if (!response.html) {
                this.dropdown.innerHTML = '<div class="header-search-dd__empty">Ничего не найдено</div>';
                this.showDropdown();
                return;
            }

            var html = response.html;
            
            if (append) {
                // Удаляем старую кнопку "Показать ещё"
                var oldShowMore = this.dropdown.querySelector('.header-search-dd__show-more');
                if (oldShowMore) {
                    oldShowMore.remove();
                }
                
                // Добавляем новый HTML в конец
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                
                // Находим новые товары и кнопку
                var newProducts = tempDiv.querySelector('.header-search-dd__products');
                var existingProducts = this.dropdown.querySelector('.header-search-dd__products');
                
                if (newProducts && existingProducts) {
                    // Переносим товары
                    while (newProducts.firstChild) {
                        existingProducts.appendChild(newProducts.firstChild);
                    }
                }
                
                // Добавляем кнопку "Показать ещё" если есть
                var showMore = tempDiv.querySelector('.header-search-dd__show-more');
                if (showMore) {
                    this.dropdown.appendChild(showMore);
                    this.bindShowMoreButton();
                }
            } else {
                // Полная замена содержимого
                this.dropdown.innerHTML = html;
                this.bindShowMoreButton();
            }
            
            this.showDropdown();
        },

        /**
         * Привязка обработчика к кнопке "Показать ещё"
         */
        bindShowMoreButton: function() {
            var self = this;
            var btn = this.dropdown.querySelector('[data-role="show-more"]');
            
            if (btn) {
                BX.bind(btn, 'click', function(e) {
                    e.preventDefault();
                    self.loadMore();
                });
            }
        },

        /**
         * Загрузка дополнительных результатов
         */
        loadMore: function() {
            // Увеличиваем offset на лимит товаров из параметров компонента
            var newOffset = this.currentOffset + this.itemsLimit;
            this.search(this.currentQuery, newOffset, true);
        },

        /**
         * Показать индикатор загрузки
         */
        showLoading: function() {
            this.dropdown.innerHTML = '<div class="header-search-dd__loading">Поиск...</div>';
            this.showDropdown();
        },

        /**
         * Показать ошибку
         * @param {string} message Текст ошибки
         */
        showError: function(message) {
            this.dropdown.innerHTML = '<div class="header-search-dd__error">' + BX.util.htmlspecialchars(message) + '</div>';
            this.showDropdown();
        },

        /**
         * Показать выпадающий список
         */
        showDropdown: function() {
            this.dropdown.style.display = 'block';
        },

        /**
         * Скрыть выпадающий список
         */
        hideDropdown: function() {
            this.dropdown.style.display = 'none';
        },

        /**
         * Очистить поиск
         */
        clearSearch: function() {
            this.input.value = '';
            if (this.clearBtn) {
                this.clearBtn.style.display = 'none';
            }
            this.hideDropdown();
            this.updateSubmitButton();
            this.input.focus();
        }
    };
})();

