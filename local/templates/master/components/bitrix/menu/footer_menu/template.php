<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

if (empty($arResult)) {
    return;
}
?>

<ul class="footer__menu">
    <?php foreach ($arResult as $arItem): ?>
        <li>
            <a href="<?= $arItem["LINK"] ?>"><?= $arItem["TEXT"] ?></a>
        </li>
    <?php endforeach; ?>
</ul>
