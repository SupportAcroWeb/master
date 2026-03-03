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

// Проверяем, есть ли разделы для отображения
if (empty($arResult['arSectionList'])) {
    return;
}
?>
<div class="container container_bordered1">
    <div class="block-categories__grid">
        <?php foreach ($arResult['arSectionList'] as $item): ?>
            <?php
            $this->AddEditAction($item['ID'], $item['EDIT_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($item['ID'], $item['DELETE_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));

            $img = '';
            if ($item["PICTURE"]) {
                $img = CFile::GetPath($item["PICTURE"]);
            }
            ?>
            <div class="card-category" id="<?= $this->GetEditAreaId($item['ID']); ?>">
                <div class="card-category__title">
                    <a href="<?= $item["SECTION_PAGE_URL"] ?>"><?= htmlspecialcharsex($item["NAME"]) ?></a>
                </div>
                <?php if ($img): ?>
                    <img class="card-category__pic"
                         src="<?= $img ?>"
                         loading="lazy"
                         alt="<?= htmlspecialcharsex($item["NAME"]) ?>">
                <?php endif; ?>
                <span class="btn-arrow1">
                    <svg aria-hidden="true" width="23" height="23">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow2"></use>
                    </svg>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
</div>