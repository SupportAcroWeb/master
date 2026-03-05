<?php
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @var CBitrixComponentTemplate $this */

$this->setFrameMode(true);
$emptyImagePath = SITE_TEMPLATE_PATH . '/img/no-image.png';

if (empty($arResult['SECTIONS'])) {
    return;
}
?>
<div class="grid1">
    <?php foreach ($arResult['SECTIONS'] as $i => $arSection): ?>
        <?php
        $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'] ?? '', CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'ELEMENT_EDIT'));
        $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'] ?? '', CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'ELEMENT_DELETE'), ['CONFIRM' => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')]);

        // Строка с широким: 3 блока (2 обычных + 1 широкий). Строка без: 4 обычных.
        $cumulative = 0;
        $row = 0;
        $posInRow = $i;
        for ($r = 0; $cumulative <= $i; $r++) {
            $rowSize = ($r % 2 === 0) ? 3 : 4;
            if ($i < $cumulative + $rowSize) {
                $row = $r;
                $posInRow = $i - $cumulative;
                break;
            }
            $cumulative += $rowSize;
        }
        $isWide = ($row % 2 === 0 && $posInRow === 2);

        $cardClass = 'card-category';
        if ($isWide) {
            $cardClass .= ' card-category_wide';
        }

        $imgSrc = $arSection['PICTURE_SRC'] ?? '';
        if (empty($imgSrc) && !empty($arSection['PICTURE'])) {
            $imgSrc = CFile::GetPath($arSection['PICTURE']);
        }
        if (empty($imgSrc)) {
            $imgSrc = $emptyImagePath;
        }

        $subsections = $arSection['ITEMS'] ?? [];
        $subsCount = count($subsections);
        ?>
        <div class="<?= htmlspecialcharsbx($cardClass) ?>" id="<?= $this->GetEditAreaId($arSection['ID']) ?>">
            <img loading="lazy" class="card-category__photo" src="<?= htmlspecialcharsbx($imgSrc) ?>" alt="<?= htmlspecialcharsbx($arSection['NAME']) ?>">
            <div class="card-category__inner">
                <div class="card-category__name">
                    <a href="<?= htmlspecialcharsbx($arSection['SECTION_PAGE_URL']) ?>"><?= htmlspecialcharsbx($arSection['NAME']) ?></a>
                </div>
                <div class="card-category__inner1">
                    <?php if (!empty($subsections)): ?>
                        <ul class="card-category__list">
                            <?php foreach ($subsections as $sub): ?>
                                <li>
                                    <a href="<?= htmlspecialcharsbx($sub['SECTION_PAGE_URL']) ?>"><?= htmlspecialcharsbx($sub['NAME']) ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if ($subsCount > 7): ?>
                            <a class="card-category__link" href="<?= htmlspecialcharsbx($arSection['SECTION_PAGE_URL']) ?>">Смотреть</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <a class="btn-arrow1" href="<?= htmlspecialcharsbx($arSection['SECTION_PAGE_URL']) ?>">
                <svg width="14" height="14" aria-hidden="true">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                </svg>
                <span class="v-h"><?= htmlspecialcharsbx($arSection['NAME']) ?></span>
            </a>
        </div>
    <?php endforeach; ?>
</div>
<?php if (!empty($arResult['IBLOCK_DESCRIPTION'])): ?>
    <div class="textblock1">
        <?= $arResult['IBLOCK_DESCRIPTION'] ?>
    </div>
<?php endif; ?>
