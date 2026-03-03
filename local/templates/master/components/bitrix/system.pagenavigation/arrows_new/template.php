<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$ClientID = 'navigation_'.$arResult['NavNum'];

$this->setFrameMode(true);

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}
?>
<div class="nav-bottom">
	<?if ($arResult["NavPageCount"] > $arResult["NavPageNomer"]):?>
		<button class="btn-load" id="load-more" data-next-page="<?=$arResult['NavPageNomer'] + 1?>"
		data-nav-num="<?=$arResult["NavNum"]?>">
            <svg aria-hidden="true" width="24" height="24">
                <use xlink:href="<?=SITE_TEMPLATE_PATH?>/img/sprite.svg#plus2"></use>
            </svg>
            <span class="spinner" role="status">Показать еще</span>
		</button>
	<?endif;?>

	<nav aria-label="Постраничная навигация">
		<ul class="pagination">
		<?
		
		$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
		$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
		$arResult["nStartPage"] = 1;
		$arResult["nEndPage"] = $arResult["NavPageCount"];
		$sPrevHref = '';
		if ($arResult["NavPageNomer"] > 1)
		{
			$bPrevDisabled = false;
			
			if ($arResult["bSavePage"] || $arResult["NavPageNomer"] > 2)
			{
				$sPrevHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]-1);
			}
			else
			{
				$sPrevHref = $arResult["sUrlPath"].$strNavQueryStringFull;
			}
		}
		else
		{
			$bPrevDisabled = true;
		}
	
		$sNextHref = '';
		if ($arResult["NavPageNomer"] < $arResult["NavPageCount"])
		{
			$bNextDisabled = false;
			$sNextHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]+1);
		}
		else
		{
			$bNextDisabled = true;
		}
		?>
			<li>
				<?if ($bPrevDisabled):?>
				<span class="pagination__item pagination__item_nav pagination__item_nav-prev pagination__item_disabled">
					<svg aria-hidden="true" width="12" height="21">
						<use xlink:href="<?=SITE_TEMPLATE_PATH?>/img/sprite.svg#chevron1"></use>
					</svg>
				</span>
				<?else:?>
				<a class="pagination__item pagination__item_nav pagination__item_nav-prev" href="<?=$sPrevHref;?>" id="<?=$ClientID?>_previous_page">
					<svg aria-hidden="true" width="12" height="21">
						<use xlink:href="<?=SITE_TEMPLATE_PATH?>/img/sprite.svg#chevron1"></use>
					</svg>
				</a>
				<?endif;?>
			</li>
			<?
			$bFirst = true;
			$bPoints = false;
			do
			{
				$NavRecordGroupPrint = $arResult["NavPageCount"] - $arResult["nStartPage"] + 1;
				if ($arResult["nStartPage"] <= 2 || $arResult["NavPageCount"]-$arResult["nStartPage"] <= 1 || abs($arResult['nStartPage']-$arResult["NavPageNomer"])<=2)
				{

					if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
			?>
				<li>
					<span class="pagination__item pagination__item_active" aria-current="page"><?=$arResult["nStartPage"]?></a>
				</li>
			<?
					elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):
			?>
					<li><a class="pagination__item" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$arResult["nStartPage"]?></a></li>
			<?
					else:
			?>
					<li class="pagination__item"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li>
			<?
					endif;
					$bFirst = false;
					$bPoints = true;
				}
				else
				{
					if ($bPoints)
					{
			?><span class="pagination__item pagination__item_ellipsis">...</span><?
						$bPoints = false;
					}
				}
				$arResult["nStartPage"]++;
			} while($arResult["nStartPage"] <= $arResult["nEndPage"]);
			?>
			<li>
			<?if ($bNextDisabled):?>
				<span class="pagination__item pagination__item_nav pagination__item_nav-next pagination__item_disabled">
					<svg aria-hidden="true" width="12" height="21">
						<use xlink:href="<?=SITE_TEMPLATE_PATH?>/img/sprite.svg#chevron3"></use>
					</svg>
				</span>
			<?else:?>
				<a class="pagination__item pagination__item_nav pagination__item_nav-next" href="<?=$sNextHref;?>" id="<?=$ClientID?>_next_page">
					<svg aria-hidden="true" width="12" height="21">
						<use xlink:href="<?=SITE_TEMPLATE_PATH?>/img/sprite.svg#chevron3"></use>
					</svg>
				</a>
			<?endif;?>
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
            
            // Сохраняем текущие параметры URL
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
                        $('.pagination').remove();

                        var elements = $(data).find('[data-entity="item"]'),
                            pagination = $(data).find('.pagination'),
                            newBtn = $(data).find('.btn-load');

                        targetContainer.append(elements);
                        $('.btn-load').remove();
                        $('.nav-bottom').append(pagination).prepend(newBtn);

                    }
                });
            }
        });
    });
</script>