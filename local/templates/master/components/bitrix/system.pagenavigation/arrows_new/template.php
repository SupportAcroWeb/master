<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$ClientID = 'navigation_' . $arResult['NavNum'];
$this->setFrameMode(true);

if (!$arResult["NavShowAlways"]) {
    if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false)) {
        return;
    }
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"] . "&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?" . $arResult["NavQueryString"] : "");
$arResult["nStartPage"] = 1;
$arResult["nEndPage"] = $arResult["NavPageCount"];

$sPrevHref = '';
if ($arResult["NavPageNomer"] > 1) {
    $bPrevDisabled = false;
    if ($arResult["bSavePage"] || $arResult["NavPageNomer"] > 2) {
        $sPrevHref = $arResult["sUrlPath"] . '?' . $strNavQueryString . 'PAGEN_' . $arResult["NavNum"] . '=' . ($arResult["NavPageNomer"] - 1);
    } else {
        $sPrevHref = $arResult["sUrlPath"] . $strNavQueryStringFull;
    }
} else {
    $bPrevDisabled = true;
}

$sNextHref = '';
if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]) {
    $bNextDisabled = false;
    $sNextHref = $arResult["sUrlPath"] . '?' . $strNavQueryString . 'PAGEN_' . $arResult["NavNum"] . '=' . ($arResult["NavPageNomer"] + 1);
} else {
    $bNextDisabled = true;
}
?>
<div class="catalog-bottom nav-bottom">
    <?php if ($arResult["NavPageCount"] > $arResult["NavPageNomer"]): ?>
        <button type="button" class="btn-text btn-text_primary btn-load" data-next-page="<?= $arResult['NavPageNomer'] + 1 ?>" data-nav-num="<?= $arResult["NavNum"] ?>">
            <svg class="btn-text__icon" aria-hidden="true" width="24" height="24">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#plus1"></use>
            </svg>
            <span>Показать еще</span>
        </button>
    <?php endif; ?>
    <nav aria-label="Постраничная навигация">
        <ul class="pagination">
            <li>
                <?php if ($bPrevDisabled): ?>
                    <span class="pagination__item pagination__item_nav pagination__item_nav-prev pagination__item_disabled">
                        <svg width="16" height="16" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow3"></use>
                        </svg>
                    </span>
                <?php else: ?>
                    <a class="pagination__item pagination__item_nav pagination__item_nav-prev" href="<?= $sPrevHref ?>" id="<?= $ClientID ?>_previous_page">
                        <svg width="16" height="16" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow3"></use>
                        </svg>
                    </a>
                <?php endif; ?>
            </li>
            <?php
            $bPoints = false;
            do {
                if ($arResult["nStartPage"] <= 2 || $arResult["NavPageCount"] - $arResult["nStartPage"] <= 1 || abs($arResult['nStartPage'] - $arResult["NavPageNomer"]) <= 2) {
                    if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
            ?>
                <li>
                    <span class="pagination__item pagination__item_active" aria-current="page"><?= $arResult["nStartPage"] ?></span>
                </li>
            <?php
                    elseif ($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):
            ?>
                <li><a class="pagination__item" href="<?= $arResult["sUrlPath"] ?><?= $strNavQueryStringFull ?>"><?= $arResult["nStartPage"] ?></a></li>
            <?php
                    else:
            ?>
                <li><a class="pagination__item" href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["nStartPage"] ?>"><span class="v-h">Страница </span><?= $arResult["nStartPage"] ?></a></li>
            <?php
                    endif;
                    $bPoints = true;
                } else {
                    if ($bPoints) {
            ?>
                <li><a class="pagination__item pagination__item_divider" href="#">...</a></li>
            <?php
                        $bPoints = false;
                    }
                }
                $arResult["nStartPage"]++;
            } while ($arResult["nStartPage"] <= $arResult["nEndPage"]);
            ?>
            <li>
                <?php if ($bNextDisabled): ?>
                    <span class="pagination__item pagination__item_nav pagination__item_nav-next pagination__item_disabled">
                        <svg width="16" height="16" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow3"></use>
                        </svg>
                    </span>
                <?php else: ?>
                    <a class="pagination__item pagination__item_nav pagination__item_nav-next" href="<?= $sNextHref ?>" id="<?= $ClientID ?>_next_page">
                        <svg width="16" height="16" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow3"></use>
                        </svg>
                    </a>
                <?php endif; ?>
            </li>
        </ul>
    </nav>
</div>
<script>
    $(document).ready(function(){
        $(document).on('click', '.btn-load', function(){
            const page = $(this).attr('data-next-page');
            const nav = $(this).attr('data-nav-num');
            var targetContainer = $('[data-entity="items-row"]');
            var urlParams = new URLSearchParams(window.location.search);
            urlParams.set('PAGEN_' + nav, page);
            urlParams.set('ajax', 'Y');
            var url = window.location.pathname + '?' + urlParams.toString();
            if (url !== undefined) {
                $.ajax({
                    type: 'GET',
                    url: url,
                    dataType: 'html',
                    success: function(data){
                        $('.nav-bottom nav').remove();
                        $('.nav-bottom .btn-load').remove();
                        var elements = $(data).find('[data-entity="item"]');
                        var navBlock = $(data).find('.nav-bottom nav');
                        var newBtn = $(data).find('.btn-load');
                        targetContainer.append(elements);
                        $('.nav-bottom').append(navBlock).prepend(newBtn);
                    }
                });
            }
        });
    });
</script>
