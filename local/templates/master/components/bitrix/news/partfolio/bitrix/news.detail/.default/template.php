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
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$galleryFiles = $arResult["GALLERY_FILES"] ?? [];
$elementName = $arResult["NAME"] ?? '';
$detailText = $arResult["DETAIL_TEXT"] ?? '';
if (empty($detailText)) {
    $detailText = $arResult["PREVIEW_TEXT"] ?? '';
}
?>
<div class="portfolio-detail">
    <div class="portfolio-detail__top">
        <div class="portfolio-detail__descr">
            <?php if ($elementName): ?>
                <h1 class="title6"><?= htmlspecialcharsbx($elementName) ?></h1>
            <?php endif; ?>
            <?php if ($detailText): ?>
                <div class="textblock1"><?= $detailText ?></div>
            <?php endif; ?>
        </div>
        <?php if (!empty($galleryFiles)): ?>
            <div class="portfolio-detail__slider">
                <div data-swiper="gallery" class="swiper swiper-gallery">
                    <div class="swiper-wrapper">
                        <?php foreach ($galleryFiles as $file): ?>
                            <a href="<?= htmlspecialcharsbx($file["SRC"]) ?>" data-fancybox="photo-big" class="swiper-slide">
                                <img src="<?= htmlspecialcharsbx($file["SRC"]) ?>" alt="<?= htmlspecialcharsbx($file["ORIGINAL_NAME"] ?? $elementName) ?>">
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <button class="swiper-nav swiper-nav_prev" type="button">
                        <svg width="16" height="16" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow2"></use>
                        </svg>
                    </button>
                    <button class="swiper-nav swiper-nav_next" type="button">
                        <svg width="16" height="16" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow2"></use>
                        </svg>
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php if (!empty($galleryFiles)): ?>
        <div class="grid1">
            <?php foreach ($galleryFiles as $file): ?>
                <a class="card-photo1" href="<?= htmlspecialcharsbx($file["SRC"]) ?>" data-fancybox="photo-grid">
                    <img src="<?= htmlspecialcharsbx($file["SRC"]) ?>" alt="<?= htmlspecialcharsbx($file["ORIGINAL_NAME"] ?? $elementName) ?>">
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
