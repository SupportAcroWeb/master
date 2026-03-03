<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
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

if (!$arResult['ITEMS']) {
    return false;
}
?>
<section class="block-news">
    <div class="container">
        <div class="heading-cols1">
            <h2 class="title2"><?= $arParams['NAME_BLOCK'] ?></h2>
            <? if ($arParams['SHOW_ON_MAIN']): ?>
                <a class="btn-text btn-text_primary" href="/news/">
                    <span>Все новости</span>
                    <svg aria-hidden="true" width="13" height="13">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                    </svg>
                </a>
            <? endif; ?>
        </div>
    </div>
    <div class="container container_bordered1">
        <div data-swiper="products" class="swiper swiper-products">
            <div class="swiper-wrapper">
                <?php foreach ($arResult['ITEMS'] as $arItem): ?>
                    <?php
                    $this->AddEditAction(
                        $arItem['ID'],
                        $arItem['EDIT_LINK'],
                        CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT')
                    );
                    $this->AddDeleteAction(
                        $arItem['ID'],
                        $arItem['DELETE_LINK'],
                        CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'),
                        ['CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')]
                    );
                    ?>
                    <div class="swiper-slide" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
                        <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="card-news">
                            <?php if ($arParams['DISPLAY_PICTURE'] !== 'N' && is_array($arItem['PREVIEW_PICTURE'])): ?>
                                <div class="card-news__photo">
                                    <img
                                            src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>"
                                            loading="lazy"
                                            alt="<?= htmlspecialchars($arItem['PREVIEW_PICTURE']['ALT']) ?>"
                                    >
                                </div>
                            <?php endif; ?>

                            <?php if ($arParams['DISPLAY_DATE'] !== 'N' && $arItem['DISPLAY_ACTIVE_FROM']): ?>
                                <div class="card-news__date badge1 badge1_black">
                                    <?= $arItem['DISPLAY_ACTIVE_FROM'] ?>
                                </div>
                            <?php endif; ?>

                            <div class="card-news__bottom">
                                <?php if ($arParams['DISPLAY_NAME'] !== 'N' && $arItem['NAME']): ?>
                                    <div class="card-news__title">
                                        <?= htmlspecialchars($arItem['NAME']) ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($arParams['DISPLAY_PREVIEW_TEXT'] !== 'N' && $arItem['PREVIEW_TEXT']): ?>
                                    <div class="card-news__preview">
                                        <?= $arItem['PREVIEW_TEXT'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

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
</section>
