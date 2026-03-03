<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @global array $arParams
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global string $cartId
 */

$compositeStub = (isset($arResult['COMPOSITE_STUB']) && $arResult['COMPOSITE_STUB'] === 'Y');
$basketCount = 0;
if (!$compositeStub && !empty($arResult['NUM_PRODUCTS'])) {
    $basketCount = (int)$arResult['NUM_PRODUCTS'];
}
?>

<a href="<?= $arParams['PATH_TO_BASKET'] ?>" class="btn-circle">
    <span class="v-h">Перейти в корзину</span>
    <svg class="desk" width="24" height="25" aria-hidden="true">
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#cart1"></use>
    </svg>
    <svg class="mob" width="13" height="14" aria-hidden="true">
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#cart2"></use>
    </svg>
    <?php if ($basketCount > 0): ?>
    <span class="btn-circle__count"><?= $basketCount ?></span>
    <?php endif; ?>
</a>