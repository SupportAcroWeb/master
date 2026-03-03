<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

if (empty($arResult['STRUCTURED_MENU'])) {
    return;
}

$structuredMenu = $arResult['STRUCTURED_MENU'];
$categoryIndex = 0;
?>

<div class="menu-catalog__left">
    <ul class="menu-catalog__nav1">
        <?php foreach ($structuredMenu as $categoryLink => $categoryData): ?>
            <?php
            $category = $categoryData['ITEM'];
            $categoryIndex++;
            $dataCategory = str_pad((string)$categoryIndex, 2, '0', STR_PAD_LEFT);
            $isActive = ($categoryIndex === 1);
            ?>
            <li>
                <a<?= $isActive ? ' class="active"' : '' ?> href="<?= $category['LINK'] ?>" data-category="<?= $dataCategory ?>">
                    <?= htmlspecialcharsbx($category['TEXT']) ?>
                    <svg width="16" height="16" aria-hidden="true">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow3"></use>
                    </svg>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="menu-catalog__right">
    <?php
    $categoryIndex = 0;
    foreach ($structuredMenu as $categoryLink => $categoryData):
        $category = $categoryData['ITEM'];
        $categoryIndex++;
        $dataCategory = str_pad((string)$categoryIndex, 2, '0', STR_PAD_LEFT);
        $isActive = ($categoryIndex === 1) ? ' active' : '';
    ?>
    <div data-category="<?= $dataCategory ?>" class="menu-catalog__category<?= $isActive ?>">
        <ul class="menu-catalog__nav2">
            <?php if (!empty($categoryData['CHILDREN'])): ?>
                <?php foreach ($categoryData['CHILDREN'] as $subCategoryLink => $subCategoryData): ?>
                    <?php $subCategory = $subCategoryData['ITEM']; ?>
                    <li><a href="<?= $subCategory['LINK'] ?>"><?= htmlspecialcharsbx($subCategory['TEXT']) ?></a></li>
                    <?php if (!empty($subCategoryData['CHILDREN'])): ?>
                        <?php foreach ($subCategoryData['CHILDREN'] as $item): ?>
                            <li><a href="<?= $item['LINK'] ?>"><?= htmlspecialcharsbx($item['TEXT']) ?></a></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <li><a href="<?= $category['LINK'] ?>"><?= htmlspecialcharsbx($category['TEXT']) ?></a></li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endforeach; ?>
</div>