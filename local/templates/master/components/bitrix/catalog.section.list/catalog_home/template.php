<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arParams
 * @var array $arResult
 * @global CMain $APPLICATION
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['arSectionList'])) {
    return;
}
?>
<div class="block-categories">
    <div class="grid1">
    <?php foreach ($arResult['arSectionList'] as $item): ?>
        <?php
        $this->AddEditAction($item['ID'], $item['EDIT_LINK'] ?? '', CIBlock::GetArrayByID($item['IBLOCK_ID'], 'ELEMENT_EDIT'));
        $this->AddDeleteAction($item['ID'], $item['DELETE_LINK'] ?? '', CIBlock::GetArrayByID($item['IBLOCK_ID'], 'ELEMENT_DELETE'), ['CONFIRM' => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')]);

        $imgSrc = $item['PICTURE_SRC'] ?? '';
        if (empty($imgSrc) && !empty($item['PICTURE'])) {
            $imgSrc = CFile::GetPath($item['PICTURE']);
        }
        $cardClass = 'card-category';
        if (!empty($item['UF_BIG_IMG'])) {
            $cardClass .= ' card-category_wide';
        }
        ?>
        <div class="<?= htmlspecialcharsbx($cardClass) ?>" id="<?= $this->GetEditAreaId($item['ID']) ?>">
            <img loading="lazy" class="card-category__photo" src="<?= $imgSrc ? htmlspecialcharsbx($imgSrc) : (SITE_TEMPLATE_PATH . '/img/category1.webp') ?>" alt="<?= htmlspecialcharsbx($item['NAME']) ?>">
            <div class="card-category__inner">
                <div class="card-category__name">
                    <a href="<?= htmlspecialcharsbx($item['SECTION_PAGE_URL']) ?>"><?= htmlspecialcharsbx($item['NAME']) ?></a>
                </div>
                <div class="card-category__inner1">
                    <?php if (!empty($item['SUBSECTIONS'])): ?>
                    <ul class="card-category__list">
                        <?php foreach ($item['SUBSECTIONS'] as $sub): ?>
                        <li>
                            <a href="<?= htmlspecialcharsbx($sub['SECTION_PAGE_URL']) ?>"><?= htmlspecialcharsbx($sub['NAME']) ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    <?php if (count($item['SUBSECTIONS'] ?? []) > 7): ?>
                    <a class="card-category__link" href="<?= htmlspecialcharsbx($item['SECTION_PAGE_URL']) ?>">Смотреть</a>
                    <?php endif; ?>
                </div>
            </div>
            <a class="btn-arrow1" href="<?= htmlspecialcharsbx($item['SECTION_PAGE_URL']) ?>">
                <svg width="14" height="14" aria-hidden="true">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                </svg>
                <span class="v-h"><?= htmlspecialcharsbx($item['NAME']) ?></span>
            </a>
        </div>
    <?php endforeach; ?>
    </div>
</div>
