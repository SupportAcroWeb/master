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
<div class="news block1">
    <?/*
    <div class="line1"></div>
    <div class="line2"></div>
    <div class="line3"></div>
    */?>
    <div class="container">
        <div class="news__body" data-entity="items-row">
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
                ?>
                <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>"
                   class="card-news"
                   id="<?= $this->GetEditAreaId($arItem['ID']) ?>"
                   data-entity="item"
                >
                    <?php if ($arParams['DISPLAY_PICTURE'] !== 'N' && is_array($arItem['PREVIEW_PICTURE'])): ?>
                        <div class="card-news__photo">
                            <img
                                    src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>"
                                    loading="lazy"
                                    alt="<?= htmlspecialchars($arItem['PREVIEW_PICTURE']['ALT']) ?>"
                            >
                        </div>
                    <?php endif; ?>

                    <?php if ($arParams['DISPLAY_DATE'] !== 'N' && $arItem['DISPLAY_ACTIVE_FROM']): ?>
                        <div class="card-news__date badge1 badge1_black">
                            <?= $arItem['DISPLAY_ACTIVE_FROM'] ?>
                        </div>
                    <?php endif; ?>

                    <div class="card-news__bottom">
                        <?php if ($arParams['DISPLAY_NAME'] !== 'N' && $arItem['NAME']): ?>
                            <div class="card-news__title">
                                <?= htmlspecialchars($arItem['NAME']) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($arParams['DISPLAY_PREVIEW_TEXT'] !== 'N' && $arItem['PREVIEW_TEXT']): ?>
                            <div class="card-news__preview">
                                <?= $arItem['PREVIEW_TEXT'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($arParams['DISPLAY_BOTTOM_PAGER']): ?>
        <?= $arResult['NAV_STRING'] ?>
    <?php endif; ?>
</div>
