<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Шаблон меню каталога
 * 
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$currentItem = $arResult['CURRENT_ITEM'] ?? null;
$parentItem = $arResult['PARENT_ITEM'] ?? null;
$children = $arResult['CHILDREN'] ?? [];

if (!$currentItem) {
    return;
}

$backLink = $parentItem ? $parentItem['LINK'] : '/produktsiya/';
$backText = $parentItem ? $parentItem['TEXT'] : 'Продукция';
$maxVisible = 4;
$itemCount = 0;
?>
<a class="catalog-nav__back" href="<?= htmlspecialcharsbx($backLink) ?>">
    <svg width="16" height="16" aria-hidden="true">
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow3"></use>
    </svg>
    <?= htmlspecialcharsbx($backText) ?>
</a>
<ul class="catalog-nav__menu">
    <li class="active">
        <a href="<?= htmlspecialcharsbx($currentItem['LINK']) ?>"><?= htmlspecialcharsbx($currentItem['TEXT']) ?></a>
    </li>
    <?php foreach ($children as $childItem):
        $itemCount++;
        $hiddenClass = $itemCount > $maxVisible ? ' catalog-nav__item_hidden' : '';
    ?>
    <li class="<?= $hiddenClass ?>">
        <a href="<?= htmlspecialcharsbx($childItem['LINK']) ?>"><?= htmlspecialcharsbx($childItem['TEXT']) ?></a>
    </li>
    <?php endforeach; ?>
</ul>
<?php if ($itemCount > $maxVisible): ?>
<label for="catalog-toggle" class="btn-text btn-text_primary btn-text_und catalog-nav__btn">Смотреть все</label>
<?php endif; ?>