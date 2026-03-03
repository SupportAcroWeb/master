<?php
/**
 * Ajax-шаблон для рендера разделов
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */

if (!empty($arResult['SECTIONS'])): ?>
    <ul class="categories-list1">
        <?php foreach ($arResult['SECTIONS'] as $section): ?>
            <li>
                <a href="<?= htmlspecialcharsbx($section['URL']) ?>">
                    <img src="<?= htmlspecialcharsbx($section['PICTURE']) ?>" alt="">
                    <?= htmlspecialcharsbx($section['NAME']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

