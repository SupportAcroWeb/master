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
?>

<div class="grid1__inner1">
    <div class="title3">Категории</div>
    <?php
    // Кнопка "Назад": родитель или "Продукция" для первого уровня
    $backLink = $parentItem ? $parentItem['LINK'] : '/produktsiya/';
    $backText = $parentItem ? $parentItem['TEXT'] : 'Продукция';
    ?>
    <a class="btn-back" href="<?= htmlspecialcharsbx($backLink) ?>">
        <svg aria-hidden="true" width="9" height="14">
            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#chevron4"></use>
        </svg>
        <span><?= htmlspecialcharsbx($backText) ?></span>
    </a>
    
    <ul class="menu1 menu1_expandable">
        <li class="active">
            <a href="<?= htmlspecialcharsbx($currentItem['LINK']) ?>">
                <?= htmlspecialcharsbx($currentItem['TEXT']) ?>
            </a>
        </li>
        
        <?php if (!empty($children)): 
            $itemCount = 0;
            $maxVisible = 4;
            
            foreach ($children as $childItem): 
                $itemCount++;
                $hiddenClass = $itemCount > $maxVisible ? ' menu1__hidden' : '';
        ?>
            <li<?= $hiddenClass ?>>
                <a href="<?= htmlspecialcharsbx($childItem['LINK']) ?>">
                    <?= htmlspecialcharsbx($childItem['TEXT']) ?>
                </a>
            </li>
        <?php endforeach; ?>
            
        <?php if ($itemCount > $maxVisible): ?>
            <li class="menu1__expander">
                <button data-action="expandList2" class="btn-text btn-text_3" type="button">Посмотреть все</button>
                <button data-action="collapseList2" class="btn-text btn-text_3" type="button">Свернуть</button>
            </li>
        <?php endif; ?>
        <?php endif; ?>
    </ul>
</div>