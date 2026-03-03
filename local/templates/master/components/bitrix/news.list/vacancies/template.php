<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arParams
 * @var array $arResult
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @global CDatabase $DB
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var string $componentPath
 * @var CBitrixComponent $component
 */

$this->setFrameMode(true);

?>
<div class="container container_bordered1">
    <div data-spollers class="spollers" data-entity="items-row">
        <?php foreach ($arResult['ITEMS'] as $arItem): ?>
            <?php
            $this->AddEditAction(
                $arItem['ID'],
                $arItem['EDIT_LINK'],
                CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT')
            );
            $this->AddDeleteAction(
                $arItem['ID'],
                $arItem['DELETE_LINK'],
                CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'),
                ['CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')]
            );

            // Получаем свойства
            $description = $arItem['PROPERTIES']['DESCRIPTION']['~VALUE']['TEXT'] ?? '';
            $cash = $arItem['PROPERTIES']['CASH']['VALUE'] ?? '';
            $terms = $arItem['PROPERTIES']['TERMS']['~VALUE']['TEXT'] ?? '';
            
            // Обязанности из превью текста
            $duties = $arItem['PREVIEW_TEXT'] ?? '';
            
            // Требования из детального описания
            $requirements = $arItem['DETAIL_TEXT'] ?? '';
            
            // Зарплата
            $salary = !empty($cash) ? $cash : 'по договоренности';
            ?>
            <div class="spollers__item" id="<?= $this->GetEditAreaId($arItem['ID']) ?>" data-entity="item">
                <div data-spoller class="spollers__titles">
                    <div class="spollers__content">
                        <div class="spollers__left">
                            <div class="spollers__title">
                                <?= htmlspecialchars($arItem['NAME']) ?>
                            </div>
                            <?php if (!empty($description)): ?>
                                <div class="spollers__subtitle">
                                    <?= $description ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="spollers__price">
                            <?= htmlspecialchars($salary) ?>
                        </div>
                    </div>
                    <div class="spollers__icon">
                        <svg aria-hidden="true" width="26" height="26">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#plus1"></use>
                        </svg>
                    </div>
                </div>
                <div class="spollers__body" hidden>
                    <div class="descr">
                        <?php if (!empty($duties)): ?>
                            <div>
                                <h4 class="title3">Обязанности:</h4>
                                <?= $duties ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($requirements)): ?>
                            <div>
                                <h4 class="title3">Требования:</h4>
                                <?= $requirements ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($terms)): ?>
                            <div>
                                <h4 class="title3">Условия:</h4>
                                <?= $terms ?>
                            </div>
                        <?php endif; ?>
                    </div> 
					<a href="#" data-hystmodal="#modalVacancies" data-vacancy-name="<?= htmlspecialchars($arItem['NAME']) ?>" class="btn btn_primary">
						Откликнуться на вакансию
					</a> 
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($arParams['DISPLAY_BOTTOM_PAGER']): ?>
    <?= $arResult['NAV_STRING'] ?>
<?php endif; ?>
