(function() {
    if (typeof window.UniversalForm !== 'undefined') {
        return;
    }

    class UniversalForm {
        constructor(params) {
            this.formId = params.formId;
            this.componentName = params.componentName;
            this.useAjax = params.useAjax;
            this.successUrl = params.successUrl;
            this.bindEvents();
        }

        bindEvents() {
            const refreshCaptchaButton = document.querySelector(`#${this.formId} .btn-refresh-captcha`);
            if (refreshCaptchaButton) {
                refreshCaptchaButton.addEventListener('click', () => this.refreshCaptcha());
            }
        }

        submitForm(form) {
            if (this.useAjax) {
                this.submitAjax(form);
            } else {
                form.submit();
            }
        }

        submitAjax(form) {
            let formData = new FormData(form);
            BX.ajax.runComponentAction(this.componentName, 'submitForm', {
                mode: 'class',
                data: formData,
            }).then((response) => {
                if (response.data.success) {
                    this.onSuccess(response.data.message);
                } else {
                    this.onError(response.data.errors);
                }
            }).catch((response) => {
                this.onError(['Произошла ошибка при отправке формы']);
            });
        }

        refreshCaptcha() {
            BX.ajax.runComponentAction(this.componentName, 'refreshCaptcha', {
                mode: 'class',
                data: {}
            }).then((response) => {
                if(response.status === 'success') {
                    const captchaImg = document.querySelector(`#${this.formId}_captcha_image`);
                    const captchaSid = document.querySelector(`#${this.formId}_captcha_sid`);
                    const captchaWord = document.querySelector(`#${this.formId}_captcha_word`);

                    if (captchaImg && captchaSid && captchaWord) {
                        captchaImg.src = '/bitrix/tools/captcha.php?captcha_sid=' + response.data.captchaCode;
                        captchaSid.value = response.data.captchaCode;
                        captchaWord.value = '';
                    }
                }
            }).catch((error) => {
                console.error('Error refreshing captcha:', error);
            });
        }

        onSuccess(message) {
            alert(message);
            if (this.successUrl) {
                window.location.href = this.successUrl;
            }
        }

        onError(errors) {
            alert(errors.join('\n'));
        }
    }

    window.UniversalForm = UniversalForm;
})();