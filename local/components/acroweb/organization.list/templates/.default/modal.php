<?php

/**
 * Модальное окно добавления организации
 * 
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var OrganizationListComponent $component
 */

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

?>

<div class="hystmodal hystmodal-add-organization" id="add-organization" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window" role="dialog" aria-modal="true">
            <div class="hystmodal__inner">
                <h2 class="hystmodal__title">данные организации</h2>
                <form data-validate data-onsubmit-trigger="organization_add" action="#" class="form block-user-cabinet__column" id="add-organization-form">
                    <div class="form-grid1">
                        <div class="form-grid1__row">
                            <div class="form-group1">
                                <input id="popup_login_inn" class="field-input1 form-group1__field" type="text" name="inn" required data-inn-input-modal>
                                <label class="form-group1__label form-group1__label_req" for="popup_login_inn">ИНН</label>
                            </div>
                        </div>
                        <div class="form-grid1__row hide" data-org-field-modal>
                            <div class="form-group1">
                                <input id="popup_login_title" class="field-input1 form-group1__field" type="text" name="title" required readonly>
                                <label class="form-group1__label form-group1__label_req" for="popup_login_title">Название</label>
                            </div>
                        </div>
                        <div class="form-grid1__row hide" data-org-field-modal>
                            <div class="form-group1">
                                <input id="popup_login_kpp" class="field-input1 form-group1__field" type="text" name="kpp" readonly>
                                <label class="form-group1__label form-group1__label_req" for="popup_login_kpp">КПП</label>
                            </div>
                        </div>
                        <div class="form-grid1__row hide" data-org-field-modal>
                            <div class="form-group1">
                                <textarea id="popup_login_address2" class="field-input1 form-group1__field" type="text" name="address" readonly></textarea>
                                <label class="form-group1__label" for="popup_login_address2">Юридический адрес</label>
                            </div>
                        </div>
                        <div class="form-grid1__row hide" data-org-field-modal data-org-card-row>
                            <div class="file-user-cabinet">
                                <div class="file-user-cabinet__content">
                                    <div class="file-user-cabinet__body">
                                        <div class="file-user-cabinet__label">Загрузить карточку организации</div>
                                        <div class="file-user-cabinet__file">Файл не выбран</div>
                                    </div>
                                    <div class="file-user-cabinet__button">
                                        Выберите файл
                                        <input type="file" name="file" data-org-card-file>
                                    </div>
                                </div>
                        </div>
                        <div class="textsmall" data-org-card-row>Загрузите карточку организации для самостоятельной оплаты заказов</div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn_primary">Сохранить</button>
                </form>
            </div>
            <button data-hystclose class="hystmodal__close">
                <svg aria-hidden="true" width="20" height="20">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#close1"></use>
                </svg>
                <span class="v-h">Закрыть</span>
            </button>
        </div>
    </div>
</div>

