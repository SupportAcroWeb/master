<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
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

if (empty($arResult['ITEMS'])) {
    return;
}
?>
<div data-swiper="products" class="swiper swiper-products">
    <div class="swiper-wrapper">
        <?php foreach ($arResult['ITEMS'] as $arItem): ?>
            <?php
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), ['CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')]);
            $galleryValue = $arItem["PROPERTIES"]["GALLERY"]["VALUE"] ?? [];
            $galleryCount = is_array($galleryValue) ? count($galleryValue) : ($galleryValue ? 1 : 0);
            $imgSrc = !empty($arItem["PREVIEW_PICTURE"]["SRC"]) ? $arItem["PREVIEW_PICTURE"]["SRC"] : SITE_TEMPLATE_PATH . '/img/portfolio1.webp';
            $detailUrl = $arItem["DETAIL_PAGE_URL"] ?? '';
            $elementName = $arItem["NAME"] ?? '';
            ?>
            <div class="swiper-slide" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
                <div class="card-portfolio">
                    <img loading="lazy" class="card-portfolio__photo" src="<?= htmlspecialcharsbx($imgSrc) ?>" alt="<?= htmlspecialcharsbx($elementName) ?>">
                    <?php if ($galleryCount > 0): ?>
                        <span class="card-portfolio__count badge1 badge1_white"><?= $galleryCount ?> фото</span>
                    <?php endif; ?>
                    <div class="card-portfolio__name">
                        <?php if ($detailUrl): ?>
                            <a href="<?= htmlspecialcharsbx($detailUrl) ?>"><?= htmlspecialcharsbx($elementName) ?></a>
                        <?php else: ?>
                            <?= htmlspecialcharsbx($elementName) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
