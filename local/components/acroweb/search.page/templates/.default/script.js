(function () {
    'use strict';

    if (!!window.NewsComponent)
        return;

    window.NewsComponent = function (params) {
        this.busy = false;
        this.siteId = params.siteId || '';
        this.ajaxId = params.ajaxId || '';
        this.template = params.template || '';
        this.componentTemplatePath = params.componentTemplatePath || '';
        this.parameters = params.parameters || '';

        if (params.navParams) {
            this.navParams = {
                NavNum: params.navParams.NavNum || 1,
                NavPageNomer: parseInt(params.navParams.NavPageNomer) || 1,
                NavPageCount: parseInt(params.navParams.NavPageCount) || 1
            };
        }

        this.container = document.querySelector('[data-entity="' + params.container + '"]');
        this.showMoreButton = document.querySelector('[data-use="show-more-' + this.navParams.NavNum + '"]');

        if (this.showMoreButton)
            this.showMoreButton.addEventListener('click', this.showMore.bind(this));

        if (params.loadOnScroll) {
            BX.bind(window, 'scroll', BX.proxy(this.loadOnScroll, this));
        }
    };

    window.NewsComponent.prototype = {
        reload: function (url) {
            this.navParams.NavNum = 1
            this.navParams.NavPageNomer = 1;

            const data = {};
            data['action'] = 'deferredLoad';

            if (!this.busy) {
                //window.history.replaceState({}, "", url);
                this.sendRequest(data, url);
            }
        },

        startLoad: function () {
            this.busy = true;
            this.disableButton();
        },

        endLoad: function () {
            this.busy = false;
            this.enableButton();
            this.checkButton();
        },

        loadOnScroll: function () {
            const scrollTop = BX.GetWindowScrollPos().scrollTop,
                containerBottom = BX.pos(this.container).bottom;

            if (scrollTop + window.innerHeight > containerBottom)
                this.showMore();
        },
        showMore: function () {
            if (this.navParams.NavPageNomer < this.navParams.NavPageCount) {
                const data = {};
                data['action'] = 'showMore';
                data['PAGEN_' + this.navParams.NavNum] = this.navParams.NavPageNomer + 1;

                if (!this.busy) {
                    this.busy = true;
                    this.disableButton();
                    this.sendRequest(data);
                }
            }
        },
        disableButton: function () {
            if (this.showMoreButton) {
                this.showMoreButton.disabled = true;
                // this.showMoreButton.innerHTML = BX.message('BTN_MESSAGE_LAZY_LOAD_WAITER');

                const spinner = this.showMoreButton.querySelector('.btn-load');

                if (spinner) {
                    spinner.classList.add('btn-load_active');
                }
            }
        },
        enableButton: function () {
            if (this.showMoreButton) {
                this.showMoreButton.removeAttribute('disabled');
                // this.showMoreButton.innerHTML = BX.message('BTN_MESSAGE_LAZY_LOAD');

                const spinner = this.showMoreButton.querySelector('.btn-load');

                if (spinner) {
                    spinner.classList.remove('btn-load_active');
                }
            }
        },
        sendRequest: function (data, url) {
            const defaultData = {
                siteId: this.siteId,
                template: this.template,
                parameters: this.parameters
            };

            if (this.ajaxId) {
                defaultData.AJAX_ID = this.ajaxId;
            }

            this.startLoad();

            BX.ajax({
                url: url || document.location.href,
                method: 'POST',
                dataType: 'json',
                timeout: 60,
                data: BX.merge(defaultData, data),
                onsuccess: BX.delegate(function (result) {
                    if (!result) {
                        console.error('Empty result received');
                        return;
                    }

                    if (!result.JS) {
                        this.processShowMoreAction(result);
                    } else {
                        BX.ajax.processScripts(
                            BX.processHTML(result.JS).SCRIPT,
                            false,
                            BX.delegate(function () {
                                this.processShowMoreAction(result)
                            }, this)
                        );
                    }

                    this.endLoad();

                }, this),
                onfailure: BX.delegate(function (error) {
                    console.error('Request failed:', error);
                    this.endLoad();
                }, this)
            });
        },
        processShowMoreAction: function (result) {
            if (result) {
                this.updateNavParams(result.navParams);
                this.processItems(result.items, result.deferredLoad);
                this.processPagination(result.pagination);
                this.checkButton();
            }
        },
        processItems: function (items, reload) {
            if (this.container) {
                if (reload) {
                    this.container.innerHTML = items;
                } else {
                    this.container.innerHTML = this.container.innerHTML + items;
                }
            }
        },
        checkButton() {
            if (this.showMoreButton) {
                this.showMoreButton.style.display =
                    this.navParams.NavPageNomer === this.navParams.NavPageCount ? 'none' : '';
            }
        },
        processPagination: function (paginationHtml) {
            if (!paginationHtml) {
                // return;
            }

            var pagination = document.querySelectorAll('[data-pagination-num="' + this.navParams.NavNum + '"]');
            for (var k in pagination) {
                if (pagination.hasOwnProperty(k)) {
                    pagination[k].innerHTML = paginationHtml;
                }
            }
        },
        processEpilogue: function (epilogueHtml) {
            if (!epilogueHtml)
                return;

            var processed = BX.processHTML(epilogueHtml, false);
            BX.ajax.processScripts(processed.SCRIPT);
        },
        updateNavParams: function (newParams) {
            if (newParams) {
                this.navParams = {
                    ...this.navParams,
                    ...newParams
                };
            }
        },
        updateUrl: function (url) {
            if (url && typeof history.replaceState === 'function') {
                history.replaceState(null, '', url);
            }
        }
    }
})();