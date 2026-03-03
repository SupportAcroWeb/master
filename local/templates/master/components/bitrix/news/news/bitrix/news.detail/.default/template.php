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

// Определяем текст кнопки "назад" в зависимости от раздела
$backText = 'назад к статьям';
if ($arParams['IBLOCK_URL'] === '/news/') {
    $backText = 'назад к новостям';
}
?>
<div class="block-news-detail block1">
    <div class="container container_bordered1">
        <div class="block-news-detail__content">
            <div class="block-news-detail__right">
                <div class="block-news-detail__sticky">
                    <a href="<?= htmlspecialchars($arParams['IBLOCK_URL']) ?>" class="back">
                        <svg aria-hidden="true" width="16" height="16">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                        </svg>
                        <span><?= htmlspecialchars($backText) ?></span>
                    </a>
                    <div class="block-news-detail__data">
                        <?php if ($arParams['DISPLAY_DATE'] !== 'N' && $arResult['DISPLAY_ACTIVE_FROM']): ?>
                            <div class="badge1 badge1_black"><?= $arResult['DISPLAY_ACTIVE_FROM'] ?></div>
                        <?php endif; ?>
                        <div class="share-wrapper">
                            <div class="share" id="share-button">
                                <svg aria-hidden="true" width="16" height="16">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#share"></use>
                                </svg>
                                <span>Поделиться</span>
                            </div>
                            <div class="ya-share2" id="ya-share-widget" data-services="vkontakte,odnoklassniki,telegram,whatsapp"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block-news-detail__left">
                <div class="descr">
                    <?php if ($arParams['DISPLAY_NAME'] !== 'N' && $arResult['NAME']): ?>
                        <h1 class="title2"><?= htmlspecialchars($arResult['NAME']) ?></h1>
                    <?php endif; ?>

                    <?php if ($arParams['DISPLAY_PICTURE'] !== 'N' && is_array($arResult['DETAIL_PICTURE'])): ?>
                        <img 
                            loading="lazy" 
                            class="block-news-detail__photo" 
                            src="<?= $arResult['DETAIL_PICTURE']['SRC'] ?>" 
                            alt="<?= htmlspecialchars($arResult['DETAIL_PICTURE']['ALT']) ?>"
                        >
                    <?php endif; ?>

                    <?php if ($arResult['NAV_RESULT']): ?>
                        <?php if ($arParams['DISPLAY_TOP_PAGER']): ?>
                            <?= $arResult['NAV_STRING'] ?>
                        <?php endif; ?>
                        <?= $arResult['NAV_TEXT'] ?>
                        <?php if ($arParams['DISPLAY_BOTTOM_PAGER']): ?>
                            <?= $arResult['NAV_STRING'] ?>
                        <?php endif; ?>
                    <?php elseif ($arResult['DETAIL_TEXT'] !== ''): ?>
                        <?= $arResult['DETAIL_TEXT'] ?>
                    <?php else: ?>
                        <?= $arResult['PREVIEW_TEXT'] ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>