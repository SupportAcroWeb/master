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
?>

<?php if ($arParams["DISPLAY_TOP_PAGER"]): ?>
    <?= $arResult["NAV_STRING"] ?>
<?php endif; ?>

<div class="container container_bordered1">
    <div data-swiper="brands" class="swiper swiper-brands">
        <div class="swiper-wrapper">
            <?php foreach ($arResult["ITEMS"] as $arItem): ?>
                <?php
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                ?>
                <div class="swiper-slide" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                    <?php if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($arItem["PREVIEW_PICTURE"])): ?>
                        <?php if (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])): ?>
                            <!--                            <a class="card-brand" href="--><?php //= $arItem["DETAIL_PAGE_URL"] ?><!--">-->
                            <span class="card-brand">
                                <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>"
                                     loading="lazy"
                                     alt="<?= htmlspecialcharsex($arItem["PREVIEW_PICTURE"]["ALT"] ?: $arItem["NAME"]) ?>"
                                     title="<?= htmlspecialcharsex($arItem["PREVIEW_PICTURE"]["TITLE"] ?: $arItem["NAME"]) ?>">
                                <!--                            </a>-->
                            </span>
                        <?php else: ?>
                            <div class="card-brand">
                                <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>"
                                     loading="lazy"
                                     alt="<?= htmlspecialcharsex($arItem["PREVIEW_PICTURE"]["ALT"] ?: $arItem["NAME"]) ?>"
                                     title="<?= htmlspecialcharsex($arItem["PREVIEW_PICTURE"]["TITLE"] ?: $arItem["NAME"]) ?>">
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])): ?>
                            <a class="card-brand" href="<?= $arItem["DETAIL_PAGE_URL"] ?>">
                                <span><?= htmlspecialcharsex($arItem["NAME"]) ?></span>
                            </a>
                        <?php else: ?>
                            <div class="card-brand">
                                <span><?= htmlspecialcharsex($arItem["NAME"]) ?></span>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
    <?= $arResult["NAV_STRING"] ?>
<?php endif; ?>
