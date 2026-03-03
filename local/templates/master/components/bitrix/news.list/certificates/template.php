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

?>
<div data-swiper="certificates" class="swiper swiper-certificates">
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

            $fileUrl = '';
            if (!empty($arItem['PROPERTIES']['FILE']['VALUE'])) {
                $fileUrl = CFile::GetPath($arItem['PROPERTIES']['FILE']['VALUE']);
            }

            $previewUrl = '';
            $previewAlt = '';
            if (is_array($arItem['PREVIEW_PICTURE'])) {
                $previewUrl = $arItem['PREVIEW_PICTURE']['SRC'];
                $previewAlt = htmlspecialchars($arItem['PREVIEW_PICTURE']['ALT'] ?: $arItem['NAME']);
            }
 
            if (empty($previewUrl) && !empty($fileUrl)) {
                $previewUrl = $fileUrl;
            }
 
            if (empty($fileUrl) && !empty($previewUrl)) {
                $fileUrl = $previewUrl;
            }
            ?>
            <?php if (!empty($previewUrl) && !empty($fileUrl)): ?>
                <a 
                    href="<?= $fileUrl ?>" 
                    data-fancybox="gallery1" 
                    class="swiper-slide"
                    id="<?= $this->GetEditAreaId($arItem['ID']) ?>"
                >
                    <img 
                        loading="lazy" 
                        src="<?= $previewUrl ?>" 
                        alt="<?= $previewAlt ?>"
                    >
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
