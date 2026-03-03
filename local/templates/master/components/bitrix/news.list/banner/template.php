<?php 
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arParams
 * @var array $arResult
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @global CDatabase $DB
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var string $componentPath
 * @var CBitrixComponent $component
 */

$this->setFrameMode(true);
?>
<?php if (!empty($arResult["ITEMS"])): ?>
<div class="block-intro">
    <div class="container">
        <div data-swiper="intro" class="swiper swiper-intro">
            <div class="swiper-wrapper">
                <?php foreach ($arResult["ITEMS"] as $arItem): ?>
                    <?php
                    $this->AddEditAction(
                        $arItem['ID'],
                        $arItem['EDIT_LINK'],
                        CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT")
                    );
                    $this->AddDeleteAction(
                        $arItem['ID'],
                        $arItem['DELETE_LINK'],
                        CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
                        ["CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')]
                    );

                    // Получаем свойства элемента
                    $linkButton = '';
                    $textButton = '';

                    if (!empty($arItem["PROPERTIES"]["LINK_BUTTON"]["VALUE"])) {
                        $linkButton = $arItem["PROPERTIES"]["LINK_BUTTON"]["VALUE"];
                    }

                    if (!empty($arItem["PROPERTIES"]["TEXT_BUTTON"]["VALUE"])) {
                        $textButton = $arItem["PROPERTIES"]["TEXT_BUTTON"]["VALUE"];
                    }
                    ?>
                    <div class="swiper-slide" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                        <div class="card-intro">
                            <?php if ($arItem["DETAIL_PICTURE"]["SRC"]): ?>
                                <div class="card-intro__bg">
                                    <div class="card-intro__bg-inner">
                                        <img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" alt="">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($arParams["DISPLAY_PICTURE"] !== "N" && is_array($arItem["PREVIEW_PICTURE"])): ?>
                                <div class="card-intro__photo">
                                    <div class="card-intro__photo-inner">
                                        <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= $arItem["PREVIEW_PICTURE"]["ALT"] ?>">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($arParams["DISPLAY_PREVIEW_TEXT"] !== "N" && $arItem["PREVIEW_TEXT"]): ?>
                                <div class="card-intro__title"><?= $arItem["PREVIEW_TEXT"] ?></div>
                            <?php endif; ?>

                            <?php if ($arItem["DETAIL_TEXT"]): ?>
                                <div class="card-intro__text"><?= $arItem["DETAIL_TEXT"] ?></div>
                            <?php endif; ?>

                            <?php if ($linkButton && $textButton): ?>
                                <a class="btn btn_primary" target="_blank" href="<?= $linkButton ?>">
                                    <span><?= $textButton ?></span>
                                    <svg width="12" height="12" aria-hidden="true">
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-navs">
                <button class="swiper-nav swiper-nav_prev" type="button">
                    <svg aria-hidden="true" width="14" height="24">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#chevron1"></use>
                    </svg>
                    <span class="v-h">Назад</span>
                </button>
                <button class="swiper-nav swiper-nav_next" type="button">
                    <svg aria-hidden="true" width="14" height="24">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#chevron1"></use>
                    </svg>
                    <span class="v-h">Вперед</span>
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
