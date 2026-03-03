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

<?php if ($arParams['DISPLAY_TOP_PAGER']): ?>
    <?= $arResult['NAV_STRING'] ?>
<?php endif; ?>

<div class="block-catalogs1__cards" data-entity="items-row">
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

        // Получаем файл из свойства FILE
        $fileUrl = '';
        $fileExtension = '';
        if (!empty($arItem['PROPERTIES']['FILE']['VALUE'])) {
            $fileArray = CFile::GetFileArray($arItem['PROPERTIES']['FILE']['VALUE']);
            if ($fileArray) {
                $fileUrl = CFile::GetPath($arItem['PROPERTIES']['FILE']['VALUE']);
                $fileExtension = mb_strtoupper(pathinfo($fileArray['FILE_NAME'], PATHINFO_EXTENSION));
            }
        }

        // Получаем превью изображение
        $previewUrl = '';
        $previewAlt = '';
        if (is_array($arItem['PREVIEW_PICTURE'])) {
            $previewUrl = $arItem['PREVIEW_PICTURE']['SRC'];
            $previewAlt = htmlspecialchars($arItem['PREVIEW_PICTURE']['ALT'] ?: $arItem['NAME']);
        }

        // Получаем дату
        $dateFormatted = '';
        if (!empty($arItem['DISPLAY_ACTIVE_FROM'])) {
            $dateFormatted = $arItem['DISPLAY_ACTIVE_FROM'];
        }
        ?>

        <?php if (!empty($fileUrl) && !empty($previewUrl)): ?>
            <div class="block-catalogs1__card" id="<?= $this->GetEditAreaId($arItem['ID']) ?>" data-entity="item">
                <div class="block-catalogs1__titles">
                    <div class="block-catalogs1__top">
                        <?php if (!empty($dateFormatted)): ?>
                            <div class="block-catalogs1__data badge1 badge1_black">
                                <?= htmlspecialchars($dateFormatted) ?>
                            </div>
                        <?php endif; ?>
                        
                        <a 
                            href="<?= htmlspecialchars($fileUrl) ?>" 
                            download 
                            class="block-catalogs1__download"
                        >
                            <img 
                                src="<?= SITE_TEMPLATE_PATH ?>/img/download.svg" 
                                alt="<?= htmlspecialchars(GetMessage('CT_BNL_DOWNLOAD')) ?>"
                            >
                            <?php if (!empty($fileExtension)): ?>
                                <span>.<?= htmlspecialchars($fileExtension) ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                    
                    <div class="block-catalogs1__title">
                        <?= htmlspecialchars($arItem['NAME']) ?>
                    </div>
                </div>
                
                <a href="<?= htmlspecialchars($fileUrl) ?>" target="_blank">
                    <img 
                        src="<?= htmlspecialchars($previewUrl) ?>" 
                        alt="<?= $previewAlt ?>" 
                        class="block-catalogs1__pic"
                    >
                </a>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<?php if ($arParams['DISPLAY_BOTTOM_PAGER']): ?>
    <?= $arResult['NAV_STRING'] ?>
<?php endif; ?>
