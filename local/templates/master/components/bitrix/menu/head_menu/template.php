<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$menuItems = array_values(array_filter($arResult, fn($i) => is_array($i) && isset($i['DEPTH_LEVEL'])));
if (empty($menuItems)) {
    return;
}
?>

<ul class="menu">
    <?php
    $idx = 0;
    while ($idx < count($menuItems)):
        $item = $menuItems[$idx];
        if (($item['DEPTH_LEVEL'] ?? 0) != 1) {
            $idx++;
            continue;
        }
        $children = [];
        for ($j = $idx + 1; $j < count($menuItems) && ($menuItems[$j]['DEPTH_LEVEL'] ?? 1) > 1; $j++) {
            $children[] = $menuItems[$j];
        }
        $hasChildren = !empty($children);
    ?>
    <li>
        <?php if ($hasChildren): ?>
            <a href="<?= $item['LINK'] ?>">
                <span><?= htmlspecialcharsbx($item['TEXT']) ?></span>
                <svg width="10" height="6" aria-hidden="true">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#chevron1"></use>
                </svg>
            </a>
            <div class="menu__sub">
                <ul>
                    <?php foreach ($children as $subItem): ?>
                    <li><a href="<?= $subItem['LINK'] ?>"><?= htmlspecialcharsbx($subItem['TEXT']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <a href="<?= $item['LINK'] ?>"><span><?= htmlspecialcharsbx($item['TEXT']) ?></span></a>
        <?php endif; ?>
    </li>
    <?php $idx += 1 + count($children); endwhile; ?>
</ul>