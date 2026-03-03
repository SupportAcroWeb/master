<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
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

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

$this->setFrameMode(true);

if (!empty($arResult['NAV_RESULT'])) {
    $navParams = [
        'NavPageCount' => $arResult['NAV_RESULT']->NavPageCount,
        'NavPageNomer' => $arResult['NAV_RESULT']->NavPageNomer,
        'NavNum' => $arResult['NAV_RESULT']->NavNum,
    ];
} else {
    $navParams = [
        'NavPageCount' => 1,
        'NavPageNomer' => 1,
        'NavNum' => $this->randString(),
    ];
}

$showBottomPager = false;
$showLazyLoad = false;

if ($arParams['PAGE_RESULT_COUNT'] > 0 && $navParams['NavPageCount'] > 1) {
    $showBottomPager = $arParams['DISPLAY_BOTTOM_PAGER'] !== 'N';
    $showLazyLoad = $arParams['LAZY_LOAD'] === 'Y' && $navParams['NavPageNomer'] != $navParams['NavPageCount'];
}

$templateData = [
    'NAV_PARAMS' => $navParams,
    'USE_PAGINATION_CONTAINER' => true,
];

$containerName = 'container-' . $navParams['NavNum'];
$obName = 'ob' . preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($navParams['NavNum']));
?>

<div class="block1 block-search">
    <div class="container">
        <form action="<?=$arResult["FORM_ACTION"]?>" class="input-group1">
            <svg aria-hidden="true" width="16" height="16">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#search1"></use>
            </svg>
            <input class="input-group1__field field-input1" type="text" name="q" value="<?=$arResult["REQUEST"]["QUERY"]?>" placeholder="Поиск по сайту">
            <button class="input-group1__btn btn btn_primary" type="submit"><?=GetMessage("SEARCH_GO")?></button>
        </form>

        <?if(isset($arResult["REQUEST"]["ORIGINAL_QUERY"])):?>
            <div class="search-language-guess">
                <?echo GetMessage("CT_BSP_KEYBOARD_WARNING", array("#query#"=>'<a href="'.$arResult["ORIGINAL_QUERY_URL"].'">'.$arResult["REQUEST"]["ORIGINAL_QUERY"].'</a>'))?>
            </div>
        <?endif;?>

        <?if($arResult["REQUEST"]["QUERY"] === false && $arResult["REQUEST"]["TAGS"] === false):?>
        <?elseif($arResult["ERROR_CODE"]!=0):?>
            <p><?=GetMessage("SEARCH_ERROR")?></p>
            <?ShowError($arResult["ERROR_TEXT"]);?>
        <?elseif(count($arResult["SEARCH"])>0):?>
            <div class="block-search__title">Результаты поиска «<?=$arResult["REQUEST"]["QUERY"]?>»</div>
            <div class="block-search__results">Найдено <?=$arResult["NAV_RESULT"]->SelectedRowsCount()?> результатов</div>
            <div class="items-list2" data-entity="<?= $containerName ?>">
                <!-- items-container -->
                <?foreach($arResult["SEARCH"] as $arItem): ?>
                    <div class="card-search">
                        <div class="card-search__title">
                            <a href="<?echo $arItem["URL"]?>"><?echo $arItem["TITLE_FORMATED"]?></a>
                        </div>
                        <div class="card-search__text textblock"><?echo $arItem["BODY_FORMATED"]?></div>
                        <?if($arItem["CHAIN_PATH"]):?>
                            <div class="card-search__path"><?=GetMessage("SEARCH_PATH")?> <?=$arItem["CHAIN_PATH"]?></div>
                        <?endif;?>
                        <span class="arrow1">
                            <svg aria-hidden="true" width="16" height="14">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                            </svg>
                        </span>
                    </div>
                <?endforeach;?>
                <!-- items-container -->
            </div>
            <!-- pagination-container -->
            <?if($showBottomPager):?>
                <div class="bottom-nav1" data-use="show-more-<?= $navParams['NavNum'] ?>">
                    <button class="btn btn_primary btn-load" type="button">
                        <span><?= Loc::getMessage('LOAD_MORE') ?></span>
                        <span class="spinner" role="status">
                            <span class="v-h"><?= Loc::getMessage('LOADING') ?></span>
                            <svg>
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#spinner1"></use>
                            </svg>
                        </span>
                    </button>
                </div>
            <?endif;?>
            <!-- pagination-container -->
        <?else:?>
            <?ShowNote(GetMessage("SEARCH_NOTHING_TO_FOUND"));?>
        <?endif;?>
    </div>
</div>

<?php
if ($showBottomPager): ?>
    <script>
<?
        $jsParams = [
            'siteId' => SITE_ID,
            'ajaxId' => $arParams['AJAX_ID'],
            'loadOnScroll' => ($arParams['LOAD_ON_SCROLL'] === 'Y'),
            'parameters' => $arParams,
            'container' => $containerName,
            'componentTemplatePath' => $templateFolder,
            'navParams' => $navParams
        ];
            ?>

        const <?= $obName ?> = new NewsComponent(<?= Json::encode($jsParams) ?>);

        BX.addCustomEvent(window, 'onChangeNewsFilter', (event) => {
            <?= $obName ?>.reload(event?.data?.url);
        });
    </script>
<?php endif; ?>
<!-- component-end -->
