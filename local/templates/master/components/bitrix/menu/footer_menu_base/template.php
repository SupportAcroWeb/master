<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

if (empty($arResult)) {
    return;
}
?>
<?php foreach ($arResult as $arItem): ?>
    <p class="footer__title">
        <a href="<?= $arItem["LINK"] ?>"><?= $arItem["TEXT"] ?></a>
    </p>
<?php endforeach; ?>
