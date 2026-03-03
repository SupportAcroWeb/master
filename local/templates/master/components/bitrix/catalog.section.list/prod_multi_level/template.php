<?php

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(true);
$emptyImagePath = SITE_TEMPLATE_PATH . '/img/no-photo.svg';

// Проверяем, есть ли разделы для отображения
if (empty($arResult['SECTIONS'])) {
    return;
}
?>
<div class="grid1__aside">
    <div class="grid1__inner1">
        <div class="title3">Категории</div>
        <ul class="menu1">
            <?php foreach ($arResult['SECTIONS'] as $arParentSection): ?>
                <li>
                    <a href="<?= $arParentSection['SECTION_PAGE_URL'] ?>">
                        <?= htmlspecialcharsex($arParentSection['NAME']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<div class="grid1__content">
    <div class="categories-list categories-list_1">
        <?php foreach ($arResult['SECTIONS'] as $arParentSection): ?>
            <?php
            $this->AddEditAction($arParentSection['ID'], $arParentSection['EDIT_LINK'], CIBlock::GetArrayByID($arParentSection["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arParentSection['ID'], $arParentSection['DELETE_LINK'], CIBlock::GetArrayByID($arParentSection["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));

            $img = $arParentSection['PICTURE']['SRC'] ?: $emptyImagePath;
            ?>
            <div class="card-category1" id="<?= $this->GetEditAreaId($arParentSection['ID']); ?>">
                <a href="<?= $arParentSection['SECTION_PAGE_URL'] ?>" class="card-category1__pic">
                    <img src="<?= $img ?>" alt="<?= htmlspecialcharsex($arParentSection['NAME']) ?>">
                </a>
                <div class="card-category1__data">
                    <div class="card-category1__title">
                        <a href="<?= $arParentSection['SECTION_PAGE_URL'] ?>">
                            <?= htmlspecialcharsex($arParentSection['NAME']) ?>
                        </a>
                    </div>
                    <?php if (!empty($arParentSection['ITEMS'])): ?>
                        <ul class="card-category1__list">
                            <?php foreach ($arParentSection['ITEMS'] as $arSection): ?>
                                <li>
                                    <a href="<?= $arSection['SECTION_PAGE_URL'] ?>">
                                        <?= htmlspecialcharsex($arSection['NAME']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if (!empty($arResult['IBLOCK_DESCRIPTION'])): ?>
        <div class="grid1__inner2">
            <div class="textblock1">
                <?= $arResult['IBLOCK_DESCRIPTION'] ?>
            </div>
        </div>
    <?php endif; ?>
</div>
