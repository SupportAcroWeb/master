<?php

/**
 * Шаблон списка организаций
 * 
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 * @var OrganizationListComponent $component
 */

use Acroweb\Mage\Organization\Service as OrganizationService;
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<!-- Лоадер для всех операций -->
<div class="organization-loader">
    <div class="loader"></div>
</div>

<div class="container container_bordered1">
    <div class="block-user-cabinet__content">
        <?php if (!empty($arResult['ORGANIZATIONS'])): ?>
            <?php foreach ($arResult['ORGANIZATIONS'] as $org): ?>
                <?php
                $orgId = (int)$org['ID'];
                $orgName = htmlspecialcharsbx($org['NAME']);
                $status = $org['PROPERTIES'][OrganizationService::PROP_STATUS]['VALUE_XML_ID'] ?? '';
                $inn = $org['PROPERTIES'][OrganizationService::PROP_INN]['VALUE'] ?? '';
                $kpp = $org['PROPERTIES'][OrganizationService::PROP_KPP]['VALUE'] ?? '';
                $urAddress = $org['PROPERTIES'][OrganizationService::PROP_UR_ADDRESS]['VALUE'] ?? '';
                $fileId = $org['PROPERTIES'][OrganizationService::PROP_FILE]['VALUE'] ?? 0;
                
                $statusClass = match($status) {
                    OrganizationService::STATUS_APPROVED => 'status_approved',
                    OrganizationService::STATUS_REJECTED => 'status_rejected',
                    OrganizationService::STATUS_PENDING => 'status_not-confirmed',
                    default => '',
                };
                
                $statusText = OrganizationService::getStatusText($status);
                $hasFile = !empty($fileId);
                ?>
                
                <div class="block-user-cabinet__column" data-organization-id="<?= $orgId ?>">
                    <div class="block-user-cabinet__top">
                        <div class="block-user-cabinet__title"><?= htmlspecialchars_decode($orgName) ?></div>
                        <button type="button" class="btn btn_primary btn_hollow" data-action-org="delete" data-org-id="<?= $orgId ?>">Удалить</button>
                    </div>
                    <form data-validate action="#" class="form" data-org-form="<?= $orgId ?>">
                        <div class="form-user-cabinet">
                            <div class="form-user-cabinet__column">
                                <div class="form-user-cabinet__top">
                                    <div class="form-user-cabinet__left">
                                        <div class="form-user-cabinet__title">Статус организации:</div>
                                        <div class="form-user-cabinet__status <?= $statusClass ?>"><?= $statusText ?></div>
                                    </div>
                                </div>
                                <div class="form-grid1">
                                    <div class="form-grid1__row">
                                        <div class="form-group1">
                                            <input id="org_<?= $orgId ?>_title" class="field-input1 form-group1__field filled" value="<?= $orgName ?>" type="text" name="title" readonly>
                                            <label class="form-group1__label form-group1__label_req" for="org_<?= $orgId ?>_title">Название</label>
                                        </div>
                                    </div>
                                    <div class="form-grid1__row">
                                        <div class="form-group1">
                                            <input id="org_<?= $orgId ?>_inn" class="field-input1 form-group1__field filled" value="<?= $inn ?>" type="text" name="inn" readonly>
                                            <label class="form-group1__label form-group1__label_req" for="org_<?= $orgId ?>_inn">ИНН</label>
                                        </div>
                                    </div>
                                    <?php if ($kpp): ?>
                                    <div class="form-grid1__row">
                                        <div class="form-group1">
                                            <input id="org_<?= $orgId ?>_kpp" class="field-input1 form-group1__field filled" value="<?= $kpp ?>" type="text" name="kpp" readonly>
                                            <label class="form-group1__label form-group1__label_req" for="org_<?= $orgId ?>_kpp">КПП</label>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($urAddress): ?>
                                    <div class="form-grid1__row">
                                        <div class="form-group1">
                                            <textarea id="org_<?= $orgId ?>_address" class="field-input1 form-group1__field filled" type="text" name="address" readonly><?= $urAddress ?></textarea>
                                            <label class="form-group1__label" for="org_<?= $orgId ?>_address">Юридический адрес</label>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-user-cabinet__column">
                                <div class="form-user-cabinet__top">
                                    <div class="form-user-cabinet__left">
                                        <div class="form-user-cabinet__title">Карточка организации:</div>
                                        <?php if ($hasFile): ?>
                                            <a href="<?= CFile::GetPath($fileId) ?>" class="form-user-cabinet__downloald" download>Скачать</a>
                                        <?php else: ?>
                                            <div class="form-user-cabinet__text">Карточка не загружена</div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($hasFile): ?>
                                        <p><span class="file-added">Для обновления данных организации, загрузите новую карточку</span></p>
                                    <?php else: ?>
                                        <p class="file-no-added">Для подтверждения Вашей организации, необходимо загрузить карточку организации</p>
                                    <?php endif; ?>
                                </div>
                                <div class="file-user-cabinet">
                                    <div class="file-user-cabinet__content">
                                        <div class="file-user-cabinet__body">
                                            <div class="file-user-cabinet__label">Загрузить карточку организации</div>
                                            <div class="file-user-cabinet__file">Файл не выбран</div>
                                        </div>
                                        <div class="file-user-cabinet__button">
                                            Выберите файл
                                            <input type="file" name="file" data-org-file="<?= $orgId ?>">
                                        </div>
                                    </div>
                                </div>
                                <?php
                                if (empty($status) || $status === 'N'): ?>
                                    <button type="button" class="btn btn_primary button-submit" <?= !$hasFile ? 'disabled' : '' ?> data-action-org="verify" data-org-id="<?= $orgId ?>">Отправить на проверку</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div class="block-user-cabinet__column last">
            <div class="block-user-cabinet__top">
                <div class="block-user-cabinet__title">добавить организацию</div>
            </div>
            <a href="#" data-hystmodal="#add-organization" class="btn btn_primary">Добавить новую организацию</a>
        </div>
    </div>
</div>

<?php
// Подключаем модальное окно
require __DIR__ . '/modal.php';
?>

<script>
BX.ready(function() {
    if (typeof BX.Acroweb === 'undefined') {
        BX.Acroweb = {};
    }
    if (typeof BX.Acroweb.OrganizationList === 'undefined') {
        BX.Acroweb.OrganizationList = {};
    }
    
    BX.Acroweb.OrganizationList.init({
        signedParameters: '<?= $this->getComponent()->getSignedParameters() ?>',
        componentName: '<?= $this->getComponent()->getName() ?>',
        messages: {
            successTitle: '<?= Loc::getMessage("FORM_SUCCESS_TITLE") ?: "Успешно" ?>',
            successMessage: '<?= Loc::getMessage("FORM_SUCCESS_MESSAGE") ?: "Организация успешно добавлена" ?>',
            closeButton: '<?= Loc::getMessage("FORM_CLOSE_BUTTON") ?: "Закрыть" ?>'
        }
    });
});
</script>

