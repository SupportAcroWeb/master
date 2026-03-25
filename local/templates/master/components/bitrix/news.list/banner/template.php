<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arParams
 * @var array $arResult
 * @global CMain $APPLICATION
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$arItem = $arResult["ITEMS"][0] ?? null;
if (empty($arItem)) {
    return;
}

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

$linkButton = $arItem["PROPERTIES"]["LINK_BUTTON"]["VALUE"] ?? '';
$textButton = $arItem["PROPERTIES"]["TEXT_BUTTON"]["VALUE"] ?? 'Каталог продукции';
$previewPicture = $arItem["PREVIEW_PICTURE"] ?? null;
$previewPictureSrc = is_array($previewPicture) ? $previewPicture["SRC"] : '';
?>
<section class="block-intro" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
    <div class="container">
        <div class="heading-cols2">
            <div class="heading-cols2__col">
                <?php if ($arParams["DISPLAY_PREVIEW_TEXT"] !== "N" && !empty($arItem["PREVIEW_TEXT"])): ?>
                <h1 class="title1"><?= $arItem["PREVIEW_TEXT"] ?></h1>
                <?php endif; ?>
            </div>
            <div class="heading-cols2__col">
                <?php if (!empty($arItem["DETAIL_TEXT"])): ?>
                <div class="block-intro__text">
                    <?= ($arItem["DETAIL_TEXT_TYPE"] ?? 'html') === 'html' ? $arItem["DETAIL_TEXT"] : nl2br(htmlspecialcharsbx($arItem["DETAIL_TEXT"])) ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($linkButton)): ?>
                <a class="btn btn_arr btn_primary" href="<?= htmlspecialcharsbx($linkButton) ?>">
                    <span><?= htmlspecialcharsbx($textButton) ?></span>
                    <svg width="14" height="14" aria-hidden="true">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                    </svg>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($arParams["DISPLAY_PICTURE"] !== "N" && !empty($previewPictureSrc)): ?>
        <img class="block-intro__photo" src="<?= htmlspecialcharsbx($previewPictureSrc) ?>" alt="<?= htmlspecialcharsbx($previewPicture["ALT"] ?? '') ?>">
        <?php endif; ?>
    </div>
</section>
<?php
$values = $arItem["PROPERTIES"]["ADVANTAGES"]["VALUE"] ?? [];
$descriptions = $arItem["PROPERTIES"]["ADVANTAGES"]["DESCRIPTION"] ?? [];
$advantages = [];
if (is_array($values)) {
    foreach ($values as $i => $name) {
        $name = trim((string)$name);
        if ($name === '') {
            continue;
        }
        $desc = isset($descriptions[$i]) ? trim((string)$descriptions[$i]) : '';
        $advantages[] = ['name' => $name, 'desc' => $desc];
    }
}
?>
<?php if (!empty($advantages)): ?>
<section class="block-advantages">
    <div class="container">
        <div class="grid1">
            <?php foreach ($advantages as $idx => $adv): ?>
            <div class="card-advantage">
                <div class="card-advantage__top">
                    <span class="card-advantage__index"><?= str_pad((string)($idx + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <span class="card-advantage__name"><?= htmlspecialcharsbx($adv['name']) ?></span>
                </div>
                <?php if ($adv['desc'] !== ''): ?>
                <div class="card-advantage__text"><?= htmlspecialcharsbx($adv['desc']) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>