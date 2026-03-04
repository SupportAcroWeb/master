<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\ProductTable;
use Bitrix\Catalog\SubscribeTable;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(true);
$currencyList = '';

if (!empty($arResult['CURRENCIES']))
{
	$templateLibrary[] = 'currency';
	$currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

$haveOffers = !empty($arResult['OFFERS']);

$templateData = [
	'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
	'TEMPLATE_LIBRARY' => $templateLibrary,
	'CURRENCIES' => $currencyList,
	'ITEM' => [
		'ID' => $arResult['ID'],
		'IBLOCK_ID' => $arResult['IBLOCK_ID'],
	],
];
if ($haveOffers)
{
	$templateData['ITEM']['OFFERS_SELECTED'] = $arResult['OFFERS_SELECTED'];
	$templateData['ITEM']['JS_OFFERS'] = $arResult['JS_OFFERS'];
}
unset($currencyList, $templateLibrary);

$mainId = $this->GetEditAreaId($arResult['ID']);
$itemIds = array(
	'ID' => $mainId,
	'DISCOUNT_PERCENT_ID' => $mainId.'_dsc_pict',
	'STICKER_ID' => $mainId.'_sticker',
	'BIG_SLIDER_ID' => $mainId.'_big_slider',
	'BIG_IMG_CONT_ID' => $mainId.'_bigimg_cont',
	'SLIDER_CONT_ID' => $mainId.'_slider_cont',
	'OLD_PRICE_ID' => $mainId.'_old_price',
	'PRICE_ID' => $mainId.'_price',
	'DESCRIPTION_ID' => $mainId.'_description',
	'DISCOUNT_PRICE_ID' => $mainId.'_price_discount',
	'PRICE_TOTAL' => $mainId.'_price_total',
	'SLIDER_CONT_OF_ID' => $mainId.'_slider_cont_',
	'QUANTITY_ID' => $mainId.'_quantity',
	'QUANTITY_DOWN_ID' => $mainId.'_quant_down',
	'QUANTITY_UP_ID' => $mainId.'_quant_up',
	'QUANTITY_MEASURE' => $mainId.'_quant_measure',
	'QUANTITY_LIMIT' => $mainId.'_quant_limit',
	'BUY_LINK' => $mainId.'_buy_link',
	'ADD_BASKET_LINK' => $mainId.'_add_basket_link',
	'BASKET_ACTIONS_ID' => $mainId.'_basket_actions',
	'NOT_AVAILABLE_MESS' => $mainId.'_not_avail',
	'COMPARE_LINK' => $mainId.'_compare_link',
	'TREE_ID' => $mainId.'_skudiv',
	'DISPLAY_PROP_DIV' => $mainId.'_sku_prop',
	'DISPLAY_MAIN_PROP_DIV' => $mainId.'_main_sku_prop',
	'OFFER_GROUP' => $mainId.'_set_group_',
	'BASKET_PROP_DIV' => $mainId.'_basket_prop',
	'SUBSCRIBE_LINK' => $mainId.'_subscribe',
	'BUY_BUTTONS_WRAP_ID' => $mainId.'_buy_wrap',
	'TABS_ID' => $mainId.'_tabs',
	'TAB_CONTAINERS_ID' => $mainId.'_tab_containers',
	'SMALL_CARD_PANEL_ID' => $mainId.'_small_card_panel',
	'TABS_PANEL_ID' => $mainId.'_tabs_panel'
);
$obName = $templateData['JS_OBJ'] = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);
$name = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
	: $arResult['NAME'];
$title = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
	: $arResult['NAME'];
$alt = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
	: $arResult['NAME'];

if ($haveOffers)
{
	$actualItem = $arResult['OFFERS'][$arResult['OFFERS_SELECTED']] ?? reset($arResult['OFFERS']);
	$showSliderControls = false;

	foreach ($arResult['OFFERS'] as $offer)
	{
		if ($offer['MORE_PHOTO_COUNT'] > 1)
		{
			$showSliderControls = true;
			break;
		}
	}
}
else
{
	$actualItem = $arResult;
	$showSliderControls = $arResult['MORE_PHOTO_COUNT'] > 1;
}

$skuProps = array();
$price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
$measureRatio = $actualItem['ITEM_MEASURE_RATIOS'][$actualItem['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
$showDiscount = $price['PERCENT'] > 0;

if ($arParams['SHOW_SKU_DESCRIPTION'] === 'Y')
{
	$skuDescription = false;
	foreach ($arResult['OFFERS'] as $offer)
	{
		if ($offer['DETAIL_TEXT'] != '' || $offer['PREVIEW_TEXT'] != '')
		{
			$skuDescription = true;
			break;
		}
	}
	$showDescription = $skuDescription || !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
}
else
{
	$showDescription = !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
}

$showBuyBtn = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']);
$buyButtonClassName = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showAddBtn = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']);
$showButtonClassName = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($arResult['PRODUCT']['SUBSCRIBE'] === 'Y' || $haveOffers);

if ($haveOffers && $showSubscribe && Loader::includeModule('catalog') && !empty($arResult['OFFERS']))
{
	global $USER, $DB;
	$offerIds = array_map('intval', array_column($arResult['OFFERS'], 'ID'));
	$offerIds = array_filter($offerIds);
	if (!empty($offerIds))
	{
		$userId = (is_object($USER) && $USER->IsAuthorized()) ? (int)$USER->GetID() : 0;
		$filter = [
			'@ITEM_ID' => $offerIds,
			'=SITE_ID' => SITE_ID,
			[
				'LOGIC' => 'OR',
				['=DATE_TO' => null],
				['>DATE_TO' => date($DB->dateFormatToPHP(\CLang::getDateFormat('FULL')), time())],
			],
		];
		if ($userId > 0)
			$filter['USER_ID'] = $userId;
		elseif (!empty($_SESSION['SUBSCRIBE_PRODUCT']['TOKEN']) && !empty($_SESSION['SUBSCRIBE_PRODUCT']['USER_CONTACT']))
		{
			$filter['=Bitrix\Catalog\SubscribeAccessTable:SUBSCRIBE.TOKEN'] = $_SESSION['SUBSCRIBE_PRODUCT']['TOKEN'];
			$filter['=Bitrix\Catalog\SubscribeAccessTable:SUBSCRIBE.USER_CONTACT'] = $_SESSION['SUBSCRIBE_PRODUCT']['USER_CONTACT'];
		}
		if (isset($filter['USER_ID']) || isset($filter['=Bitrix\Catalog\SubscribeAccessTable:SUBSCRIBE.TOKEN']))
		{
			$res = SubscribeTable::getList(['select' => ['ITEM_ID'], 'filter' => $filter]);
			if (!isset($_SESSION['SUBSCRIBE_PRODUCT']['LIST_PRODUCT_ID']) || !is_array($_SESSION['SUBSCRIBE_PRODUCT']['LIST_PRODUCT_ID']))
				$_SESSION['SUBSCRIBE_PRODUCT']['LIST_PRODUCT_ID'] = [];
			while ($row = $res->fetch())
				$_SESSION['SUBSCRIBE_PRODUCT']['LIST_PRODUCT_ID'][(int)$row['ITEM_ID']] = true;
		}
	}
}

$arParams['MESS_BTN_BUY'] = $arParams['MESS_BTN_BUY'] ?: Loc::getMessage('CT_BCE_CATALOG_BUY');
$arParams['MESS_BTN_ADD_TO_BASKET'] = $arParams['MESS_BTN_ADD_TO_BASKET'] ?: Loc::getMessage('CT_BCE_CATALOG_ADD');

if ($arResult['MODULES']['catalog'] && $arResult['PRODUCT']['TYPE'] === ProductTable::TYPE_SERVICE)
{
	$arParams['~MESS_NOT_AVAILABLE_SERVICE'] ??= '';
	$arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE_SERVICE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE')
	;

	$arParams['MESS_NOT_AVAILABLE_SERVICE'] ??= '';
	$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE_SERVICE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE')
	;
}
else
{
	$arParams['~MESS_NOT_AVAILABLE'] ??= '';
	$arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE')
	;

	$arParams['MESS_NOT_AVAILABLE'] ??= '';
	$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE')
	;
}

$arParams['MESS_BTN_COMPARE'] = $arParams['MESS_BTN_COMPARE'] ?: Loc::getMessage('CT_BCE_CATALOG_COMPARE');
$arParams['MESS_PRICE_RANGES_TITLE'] = $arParams['MESS_PRICE_RANGES_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_PRICE_RANGES_TITLE');
$arParams['MESS_DESCRIPTION_TAB'] = $arParams['MESS_DESCRIPTION_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_DESCRIPTION_TAB');
$arParams['MESS_PROPERTIES_TAB'] = $arParams['MESS_PROPERTIES_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_PROPERTIES_TAB');
$arParams['MESS_COMMENTS_TAB'] = $arParams['MESS_COMMENTS_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_COMMENTS_TAB');
$arParams['MESS_SHOW_MAX_QUANTITY'] = $arParams['MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCE_CATALOG_SHOW_MAX_QUANTITY');
$arParams['MESS_RELATIVE_QUANTITY_MANY'] = $arParams['MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['MESS_RELATIVE_QUANTITY_FEW'] = $arParams['MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_FEW');

$positionClassMap = array(
	'left' => 'product-item-label-left',
	'center' => 'product-item-label-center',
	'right' => 'product-item-label-right',
	'bottom' => 'product-item-label-bottom',
	'middle' => 'product-item-label-middle',
	'top' => 'product-item-label-top'
);

$discountPositionClass = 'product-item-label-big';
if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION']))
{
	foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos)
	{
		$discountPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}

$labelPositionClass = 'product-item-label-big';
if (!empty($arParams['LABEL_PROP_POSITION']))
{
	foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos)
	{
		$labelPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}

// Код товара для блока «Код товара»
$productCode = '';
if (!empty($arResult['DISPLAY_PROPERTIES']['ARTICLE']['DISPLAY_VALUE']))
	$productCode = $arResult['DISPLAY_PROPERTIES']['ARTICLE']['DISPLAY_VALUE'];
elseif (!empty($arResult['DISPLAY_PROPERTIES']['ARTNUMBER']['DISPLAY_VALUE']))
	$productCode = $arResult['DISPLAY_PROPERTIES']['ARTNUMBER']['DISPLAY_VALUE'];
else
	$productCode = (string)$arResult['ID'];

$spritePath = SITE_TEMPLATE_PATH . '/img/sprite.svg';
?>
<div class="catalog-detail-grid block4" id="<?=$itemIds['ID']?>"
	itemscope itemtype="http://schema.org/Product">
	<?php // Скрытый блок для скрипта Bitrix — галереей управляет только наш Swiper (как в макете) ?>
	<div id="<?=$itemIds['BIG_SLIDER_ID']?>" class="product-item-detail-slider-container" style="display: none; position: absolute; left: -9999px;" data-entity="images-slider-block" aria-hidden="true">
		<div data-entity="images-container">
			<div data-entity="image" data-id="0"></div>
		</div>
	</div>
	<div class="catalog-detail-grid__main catalog-detail-main">
		<div class="catalog-detail-main__top">
			<h1 class="title3"><?=$name?></h1>
			<?php if ($arParams['BRAND_USE'] === 'Y'): ?>
				<?php $APPLICATION->IncludeComponent('bitrix:catalog.brandblock', '.default', [
					'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
					'IBLOCK_ID' => $arParams['IBLOCK_ID'],
					'ELEMENT_ID' => $arResult['ID'],
					'ELEMENT_CODE' => '',
					'PROP_CODE' => $arParams['BRAND_PROP_CODE'],
					'CACHE_TYPE' => $arParams['CACHE_TYPE'],
					'CACHE_TIME' => $arParams['CACHE_TIME'],
					'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
					'WIDTH' => '', 'HEIGHT' => ''
				], $component, ['HIDE_ICONS' => 'Y']); ?>
			<?php else: ?>
				<a class="catalog-detail-main__logo" href="#"></a>
			<?php endif; ?>
		</div>
		<div class="catalog-detail-main__bottom">
			<div class="catalog-detail-main__left">
				<?php if ($arParams['USE_VOTE_RATING'] === 'Y'): ?>
					<div class="rating-wrapper">
						<?php $APPLICATION->IncludeComponent('bitrix:iblock.vote', 'stars', [
							'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
							'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
							'IBLOCK_ID' => $arParams['IBLOCK_ID'],
							'ELEMENT_ID' => $arResult['ID'],
							'ELEMENT_CODE' => '', 'MAX_VOTE' => '5', 'VOTE_NAMES' => ['1','2','3','4','5'],
							'SET_STATUS_404' => 'N', 'DISPLAY_AS_RATING' => $arParams['VOTE_DISPLAY_AS_RATING'],
							'CACHE_TYPE' => $arParams['CACHE_TYPE'], 'CACHE_TIME' => $arParams['CACHE_TIME']
						], $component, ['HIDE_ICONS' => 'Y']); ?>
					</div>
				<?php endif; ?>
				<a class="lnk-share" href="#" data-action="share">
					<svg aria-hidden="true" width="16" height="16"><use xlink:href="<?= $spritePath ?>#share1"></use></svg>
					Поделиться
				</a>
			</div>
			<div class="catalog-detail-main__code">Код товара: <?= htmlspecialcharsbx($productCode) ?></div>
		</div>
	</div>
	<div class="catalog-detail-grid__photo">
		<div class="catalog-photo">
			<div class="catalog-photo__big catalog-photo-big">
				<div class="catalog-photo-big__labels">
					<?php
					if ($arResult['LABEL'] && !empty($arResult['LABEL_ARRAY_VALUE'])) {
						foreach ($arResult['LABEL_ARRAY_VALUE'] as $code => $value) {
							$labelClass = 'badge badge_primary';
							if ((string)$code !== '') {
								$labelClass .= ' ' . preg_replace('/[^a-z0-9_-]/i', '-', trim((string)$code));
							}
							?><span class="<?= htmlspecialcharsbx($labelClass) ?>"><?= htmlspecialcharsbx($value) ?></span><?php
						}
					}
					if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !$haveOffers && $price['DISCOUNT'] > 0) {
						?><span class="badge badge_orange"><?= -$price['PERCENT'] ?>%</span><?php
					}
					if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && $haveOffers) {
						?><span class="badge badge_orange product-item-label-ring" id="<?=$itemIds['DISCOUNT_PERCENT_ID']?>" style="display: none;"></span><?php
					}
					?>
				</div>
				<div data-swiper="photoBig" class="swiper swiper-photo-big">
					<div class="swiper-wrapper">
						<?php
						if (!empty($actualItem['MORE_PHOTO'])) {
							foreach ($actualItem['MORE_PHOTO'] as $key => $photo) {
								$alt = !empty($photo['ALT']) ? $photo['ALT'] : $actualItem['NAME'];
								$title = !empty($photo['TITLE']) ? $photo['TITLE'] : $actualItem['NAME'];
								?><div class="swiper-slide swiper-photo-big__item">
									<a data-fancybox="catalogDetail" class="swiper-photo-big__link" href="<?= htmlspecialcharsbx($photo['SRC']) ?>">
										<img class="swiper-photo-big__photo" src="<?=$photo['SRC']?>" alt="<?= htmlspecialcharsbx($alt) ?>" title="<?= htmlspecialcharsbx($title) ?>"<?=($key === 0 ? ' itemprop="image"' : '')?>>
									</a>
								</div><?php
							}
						}
						?>
					</div>
					<button type="button" data-swiper-nav="next" class="btn-swiper-nav btn-swiper-nav_next" aria-label="Следующий">
						<svg aria-hidden="true" width="16" height="14"><use xlink:href="<?= $spritePath ?>#arrow1"></use></svg>
					</button>
					<button type="button" data-swiper-nav="prev" class="btn-swiper-nav btn-swiper-nav_prev" aria-label="Предыдущий">
						<svg aria-hidden="true" width="16" height="14"><use xlink:href="<?= $spritePath ?>#arrow1"></use></svg>
					</button>
				</div>
			</div>
			<div class="catalog-photo__previews">
				<div data-swiper="photoPreview" class="swiper swiper-photo-preview">
					<?php if ($showSliderControls && !empty($actualItem['MORE_PHOTO'])): ?>
					<div class="swiper-wrapper">
						<?php
						foreach ($actualItem['MORE_PHOTO'] as $key => $photo) {
							?><div class="swiper-slide swiper-photo-preview__item<?=($key === 0 ? ' swiper-photo-preview__item_active' : '')?>">
								<img class="swiper-photo-preview__photo" src="<?=$photo['SRC']?>" alt="">
							</div><?php
						}
						?>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="catalog-detail-grid__order">
		<div class="row">
			<div class="col-sm-6">
				<div class="product-item-detail-info-section">
							<?php
							foreach ($arParams['PRODUCT_INFO_BLOCK_ORDER'] as $blockName)
							{
								switch ($blockName)
								{
									case 'sku':
										// Офферы (Цвет, Размер и т.д.) выводятся в блоке «Характеристики» ниже
										if ($haveOffers && !empty($arResult['OFFERS_PROP']))
										{
											foreach ($arResult['SKU_PROPS'] as $skuProperty)
											{
												if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']]))
													continue;
												$skuProps[] = array(
													'ID' => $skuProperty['ID'],
													'SHOW_MODE' => $skuProperty['SHOW_MODE'],
													'VALUES' => $skuProperty['VALUES'],
													'VALUES_COUNT' => $skuProperty['VALUES_COUNT']
												);
											}
										}
										break;

									case 'props':
										// Свойства (Производитель, Материал, Артикул и т.д.) выводятся только в блоке «Характеристики» ниже
										break;
								}
							}
							?>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="rblock2 catalog-detail-order product-item-detail-pay-block">
							<?php
							foreach ($arParams['PRODUCT_PAY_BLOCK_ORDER'] as $blockName)
							{
								switch ($blockName)
								{
									case 'rating':
										if ($arParams['USE_VOTE_RATING'] === 'Y')
										{
											?>
											<div class="product-item-detail-info-container">
												<?php
												$APPLICATION->IncludeComponent(
													'bitrix:iblock.vote',
													'stars',
													array(
														'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
														'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
														'IBLOCK_ID' => $arParams['IBLOCK_ID'],
														'ELEMENT_ID' => $arResult['ID'],
														'ELEMENT_CODE' => '',
														'MAX_VOTE' => '5',
														'VOTE_NAMES' => array('1', '2', '3', '4', '5'),
														'SET_STATUS_404' => 'N',
														'DISPLAY_AS_RATING' => $arParams['VOTE_DISPLAY_AS_RATING'],
														'CACHE_TYPE' => $arParams['CACHE_TYPE'],
														'CACHE_TIME' => $arParams['CACHE_TIME']
													),
													$component,
													array('HIDE_ICONS' => 'Y')
												);
												?>
											</div>
											<?php
										}

										break;

									case 'price':
										?>
										<div class="catalog-detail-order__prices">
											<?php if ($arParams['SHOW_OLD_PRICE'] === 'Y'): ?>
												<span class="catalog-detail-order__price-old product-item-detail-price-old" id="<?=$itemIds['OLD_PRICE_ID']?>" style="display: <?=($showDiscount ? '' : 'none')?>;"><?=($showDiscount ? $price['PRINT_RATIO_BASE_PRICE'] : '')?></span>
											<?php endif; ?>
											<span class="catalog-detail-order__price product-item-detail-price-current" id="<?=$itemIds['PRICE_ID']?>"><?=$price['PRINT_RATIO_PRICE']?></span>
											<?php if ($arParams['SHOW_OLD_PRICE'] === 'Y'): ?>
												<div class="item_economy_price" id="<?=$itemIds['DISCOUNT_PRICE_ID']?>" style="display: <?=($showDiscount ? '' : 'none')?>;"><?= $showDiscount ? Loc::getMessage('CT_BCE_CATALOG_ECONOMY_INFO2', array('#ECONOMY#' => $price['PRINT_RATIO_DISCOUNT'])) : '' ?></div>
											<?php endif; ?>
										</div>
										<p class="catalog-detail-order__status text-status" style="display: <?=($actualItem['CAN_BUY'] ? '' : 'none')?>;">
											<svg aria-hidden="true" width="16" height="16"><use xlink:href="<?= $spritePath ?>#status1"></use></svg>
											В наличии в <a href="#">магазинах</a>
										</p>
										<?php
										break;

									case 'priceRanges':
										if ($arParams['USE_PRICE_COUNT'])
										{
											$showRanges = !$haveOffers && count($actualItem['ITEM_QUANTITY_RANGES']) > 1;
											$useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';
											?>
											<div class="product-item-detail-info-container"
												<?=$showRanges ? '' : 'style="display: none;"'?>
												data-entity="price-ranges-block">
												<div class="product-item-detail-info-container-title">
													<?=$arParams['MESS_PRICE_RANGES_TITLE']?>
													<span data-entity="price-ranges-ratio-header">
														(<?=(Loc::getMessage(
															'CT_BCE_CATALOG_RATIO_PRICE',
															array('#RATIO#' => ($useRatio ? $measureRatio : '1').' '.$actualItem['ITEM_MEASURE']['TITLE'])
														))?>)
													</span>
												</div>
												<dl class="product-item-detail-properties" data-entity="price-ranges-body">
													<?php
													if ($showRanges)
													{
														foreach ($actualItem['ITEM_QUANTITY_RANGES'] as $range)
														{
															if ($range['HASH'] !== 'ZERO-INF')
															{
																$itemPrice = false;

																foreach ($arResult['ITEM_PRICES'] as $itemPrice)
																{
																	if ($itemPrice['QUANTITY_HASH'] === $range['HASH'])
																	{
																		break;
																	}
																}

																if ($itemPrice)
																{
																	?>
																	<dt>
																		<?php
																		echo Loc::getMessage(
																				'CT_BCE_CATALOG_RANGE_FROM',
																				array('#FROM#' => $range['SORT_FROM'].' '.$actualItem['ITEM_MEASURE']['TITLE'])
																			).' ';

																		if (is_infinite($range['SORT_TO']))
																		{
																			echo Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
																		}
																		else
																		{
																			echo Loc::getMessage(
																				'CT_BCE_CATALOG_RANGE_TO',
																				array('#TO#' => $range['SORT_TO'].' '.$actualItem['ITEM_MEASURE']['TITLE'])
																			);
																		}
																		?>
																	</dt>
																	<dd><?=($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE'])?></dd>
																	<?php
																}
															}
														}
													}
													?>
												</dl>
											</div>
											<?php
											unset($showRanges, $useRatio, $itemPrice, $range);
										}

										break;

									case 'quantityLimit':
										if ($arParams['SHOW_MAX_QUANTITY'] !== 'N')
										{
											if ($haveOffers)
											{
												?>
												<div class="product-item-detail-info-container" id="<?=$itemIds['QUANTITY_LIMIT']?>" style="display: none;">
													<div class="product-item-detail-info-container-title">
														<?=$arParams['MESS_SHOW_MAX_QUANTITY']?>:
														<span class="product-item-quantity" data-entity="quantity-limit-value"></span>
													</div>
												</div>
												<?php
											}
											else
											{
												if (
													$measureRatio
													&& (float)$actualItem['PRODUCT']['QUANTITY'] > 0
													&& $actualItem['CHECK_QUANTITY']
												)
												{
													?>
													<div class="product-item-detail-info-container" id="<?=$itemIds['QUANTITY_LIMIT']?>">
														<div class="product-item-detail-info-container-title">
															<?=$arParams['MESS_SHOW_MAX_QUANTITY']?>:
															<span class="product-item-quantity" data-entity="quantity-limit-value">
																<?php
																if ($arParams['SHOW_MAX_QUANTITY'] === 'M')
																{
																	if ((float)$actualItem['PRODUCT']['QUANTITY'] / $measureRatio >= $arParams['RELATIVE_QUANTITY_FACTOR'])
																	{
																		echo $arParams['MESS_RELATIVE_QUANTITY_MANY'];
																	}
																	else
																	{
																		echo $arParams['MESS_RELATIVE_QUANTITY_FEW'];
																	}
																}
																else
																{
																	echo $actualItem['PRODUCT']['QUANTITY'].' '.$actualItem['ITEM_MEASURE']['TITLE'];
																}
																?>
															</span>
														</div>
													</div>
													<?php
												}
											}
										}

										break;

									case 'quantity':
										if ($arParams['USE_PRODUCT_QUANTITY'])
										{
											?>
											<div class="product-item-detail-info-container" style="<?=(!$actualItem['CAN_BUY'] ? 'display: none;' : '')?>"
												data-entity="quantity-block">
												<div class="product-item-detail-info-container-title"><?=Loc::getMessage('CATALOG_QUANTITY')?></div>
												<div class="product-item-amount">
													<div class="product-item-amount-field-container">
														<span class="product-item-amount-field-btn-minus no-select" id="<?=$itemIds['QUANTITY_DOWN_ID']?>"></span>
														<input class="product-item-amount-field" id="<?=$itemIds['QUANTITY_ID']?>" type="number"
															value="<?=$price['MIN_QUANTITY']?>">
														<span class="product-item-amount-field-btn-plus no-select" id="<?=$itemIds['QUANTITY_UP_ID']?>"></span>
														<span class="product-item-amount-description-container">
															<span id="<?=$itemIds['QUANTITY_MEASURE']?>">
																<?=$actualItem['ITEM_MEASURE']['TITLE']?>
															</span>
															<span id="<?=$itemIds['PRICE_TOTAL']?>"></span>
														</span>
													</div>
												</div>
											</div>
											<?php
										}

										break;

									case 'buttons':
										$showBothOfferButtons = $haveOffers && $showSubscribe;
										?>
										<div data-entity="main-button-container">
											<div class="catalog-detail-order__btns" id="<?=$itemIds['BASKET_ACTIONS_ID']?>" style="display: <?=($actualItem['CAN_BUY'] || ($showSubscribe && !$actualItem['CAN_BUY']) ? '' : 'none')?>;">
												<?php if ($actualItem['CAN_BUY'] || $showBothOfferButtons): ?>
													<span id="<?=$itemIds['BUY_BUTTONS_WRAP_ID']?>" style="display: <?=($actualItem['CAN_BUY'] ? '' : 'none')?>;">
														<?php if ($showAddBtn): ?>
															<a class="btn btn_primary btn_wide" id="<?=$itemIds['ADD_BASKET_LINK']?>" href="javascript:void(0);"><?=$arParams['MESS_BTN_ADD_TO_BASKET']?></a>
														<?php endif; ?>
														<?php if ($showBuyBtn): ?>
															<a class="btn btn_primary btn_wide" id="<?=$itemIds['BUY_LINK']?>" href="javascript:void(0);"><?=$arParams['MESS_BTN_BUY']?></a>
														<?php endif; ?>
														<button data-action="popupBuyByOneClick" class="btn btn_black btn_hollow btn_wide" type="button">Купить в 1 клик</button>
													</span>
												<?php endif; ?>
												<?php if ($showSubscribe && (!$actualItem['CAN_BUY'] || $showBothOfferButtons)): ?>
													<?php
													$APPLICATION->IncludeComponent(
														'bitrix:catalog.product.subscribe',
														'',
														array(
															'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
															'PRODUCT_ID' => $actualItem['ID'],
															'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
															'BUTTON_CLASS' => 'btn btn_primary btn_wide',
															'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
															'FORCE_OUTPUT' => true,
															'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
															'CACHE_TYPE' => 'N',
															'CACHE_TIME' => 0,
														),
														$component,
														array('HIDE_ICONS' => 'Y')
													);
													?>
													<script>BX.message({'CPST_TITLE_ALREADY_SUBSCRIBED': '<?= CUtil::JSEscape(Loc::getMessage('CT_BCE_SUBSCRIBE_ALREADY') ?: 'Вы уже подписаны') ?>'});</script>
												<?php endif; ?>
											</div>
											<?php
											$showNotAvailBlock = (!$actualItem['CAN_BUY'] && !$showSubscribe) || $haveOffers;
											if ($showNotAvailBlock):
												$notAvailStyle = ($haveOffers && $showSubscribe) ? 'display: none;' : '';
											?>
											<div class="product-item-detail-info-container" id="<?=$itemIds['NOT_AVAILABLE_MESS']?>_wrap" style="<?=$notAvailStyle?>">
												<a class="btn btn-link product-item-detail-buy-button" id="<?=$itemIds['NOT_AVAILABLE_MESS']?>"
													href="javascript:void(0)"
													rel="nofollow">
													<?=$arParams['MESS_NOT_AVAILABLE']?>
												</a>
											</div>
											<?php endif; ?>
										</div>
										<?php
										break;
								}
							}

							if ($arParams['DISPLAY_COMPARE'])
							{
								?>
								<div class="product-item-detail-compare-container">
									<div class="product-item-detail-compare">
										<div class="checkbox">
											<label id="<?=$itemIds['COMPARE_LINK']?>">
												<input type="checkbox" data-entity="compare-checkbox">
												<span data-entity="compare-title"><?=$arParams['MESS_BTN_COMPARE']?></span>
											</label>
										</div>
									</div>
								</div>
								<?php
							}
							?>
						</div>
					</div>
				</div>
				<div class="rblock2 catalog-detail-order catalog-detail-links">
					<p><button data-action="popupLowPrice" type="button">
						<svg aria-hidden="true" width="16" height="16"><use xlink:href="<?= $spritePath ?>#currency1"></use></svg>
						Нашли дешевле?
					</button></p>
					<p><button data-action="popupShippingCalc" type="button">
						<svg aria-hidden="true" width="16" height="16"><use xlink:href="<?= $spritePath ?>#shipping1"></use></svg>
						Рассчитать доставку
					</button></p>
					<p><button data-action="popupWantAsGift" type="button">
						<svg aria-hidden="true" width="16" height="16"><use xlink:href="<?= $spritePath ?>#gift1"></use></svg>
						Хочу в подарок
					</button></p>
				</div>
			</div>
	<div class="catalog-detail-grid__description">
			<div class="blocks-list3">
				<?php if ($showDescription): ?>
				<div class="catalog-detail-text-wrapper" data-item-expandable="collapsed">
					<div class="catalog-detail-text">
						<?php
						if ($arResult['PREVIEW_TEXT'] !== '' && ($arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'S' || ($arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'E' && $arResult['DETAIL_TEXT'] === '')))
							echo $arResult['PREVIEW_TEXT_TYPE'] === 'html' ? $arResult['PREVIEW_TEXT'] : '<p>' . $arResult['PREVIEW_TEXT'] . '</p>';
						if ($arResult['DETAIL_TEXT'] !== '')
							echo $arResult['DETAIL_TEXT_TYPE'] === 'html' ? $arResult['DETAIL_TEXT'] : '<p>' . $arResult['DETAIL_TEXT'] . '</p>';
						?>
					</div>
					<button data-item-expandable-handle="show" class="btn-text btn-text_1 btn-text_primary">Подробнее</button>
					<button data-item-expandable-handle="hide" class="btn-text btn-text_1 btn-text_primary">Свернуть</button>
				</div>
				<?php endif; ?>
				<?php if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS'] || ($haveOffers && !empty($arResult['OFFERS_PROP']))): ?>
				<?php
				$specCount = 0;
				if (!empty($arResult['DISPLAY_PROPERTIES'])) {
					foreach ($arResult['DISPLAY_PROPERTIES'] as $property) {
						$specCount++;
					}
				}
				if ($arResult['SHOW_OFFERS_PROPS']) $specCount++;
				$showSpecExpand = ($specCount >= 7);
				?>
				<div>
					<?php if ($haveOffers && !empty($arResult['OFFERS_PROP'])): ?>
					<h2 class="title4">Предложения</h2>
					<div id="<?=$itemIds['TREE_ID']?>" class="product-item-detail-sku-block">
						<?php
						foreach ($arResult['SKU_PROPS'] as $skuProperty) {
							if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']]))
								continue;
							$propertyId = $skuProperty['ID'];
							?>
							<div class="product-item-detail-info-container" data-entity="sku-line-block">
								<div class="product-item-detail-info-container-title"><?=htmlspecialcharsEx($skuProperty['NAME'])?></div>
								<div class="product-item-scu-container">
									<div class="product-item-scu-block">
										<div class="product-item-scu-list">
											<ul class="product-item-scu-item-list">
												<?php
												foreach ($skuProperty['VALUES'] as &$value) {
													$value['NAME'] = htmlspecialcharsbx($value['NAME']);
													if ($skuProperty['SHOW_MODE'] === 'PICT') {
														?><li class="product-item-scu-item-color-container" title="<?=$value['NAME']?>"
															data-treevalue="<?=$propertyId?>_<?=$value['ID']?>"
															data-onevalue="<?=$value['ID']?>">
															<div class="product-item-scu-item-color-block">
																<div class="product-item-scu-item-color" title="<?=$value['NAME']?>"
																	style="background-image: url('<?=$value['PICT']['SRC']?>');"></div>
															</div>
														</li><?php
													} else {
														?><li class="product-item-scu-item-text-container" title="<?=$value['NAME']?>"
															data-treevalue="<?=$propertyId?>_<?=$value['ID']?>"
															data-onevalue="<?=$value['ID']?>">
															<div class="product-item-scu-item-text-block">
																<div class="product-item-scu-item-text"><?=$value['NAME']?></div>
															</div>
														</li><?php
													}
												}
												?>
											</ul>
										</div>
									</div>
								</div>
							</div>
							<?php
						}
						?>
					</div>
					<?php endif; ?>
					<?php if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS']): ?>
					<h2 class="title4">Характеристики</h2>
					<div class="speclist1-wrapper"<?= $showSpecExpand ? ' data-item-expandable="collapsed"' : '' ?>>
						<div class="speclist1">
							<?php
							// Вывод значения с декодированием HTML-сущностей (&quot; → кавычки и т.д.)
							$safeSpecValue = function ($v) {
								return htmlspecialcharsbx(html_entity_decode((string)$v, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
							};
							if (!empty($arResult['DISPLAY_PROPERTIES'])) {
								foreach ($arResult['DISPLAY_PROPERTIES'] as $property) {
									$val = is_array($property['DISPLAY_VALUE'])
										? implode(', ', array_map($safeSpecValue, $property['DISPLAY_VALUE']))
										: $safeSpecValue($property['DISPLAY_VALUE']);
									?><span class="speclist1__key"><?= htmlspecialcharsbx($property['NAME']) ?></span>
									<span class="speclist1__value"><?= $val ?></span><?php
								}
							}
							if ($arResult['SHOW_OFFERS_PROPS']) {
								// Два span как у остальных характеристик — JS обновляет textContent при смене оффера
								$selOffer = $arResult['JS_OFFERS'][$arResult['OFFERS_SELECTED']] ?? null;
								$mainPropName = '';
								$mainPropVal = '';
								if ($selOffer && !empty($selOffer['MAIN_PROP_NAME'])) {
									$mainPropName = htmlspecialcharsbx($selOffer['MAIN_PROP_NAME']);
									$mainPropVal = $safeSpecValue($selOffer['MAIN_PROP_VALUE']);
								}
								?><span class="speclist1__key" id="<?= $itemIds['DISPLAY_MAIN_PROP_DIV'] ?>-label"><?= $mainPropName ?></span>
								<span class="speclist1__value" id="<?= $itemIds['DISPLAY_MAIN_PROP_DIV'] ?>"><?= $mainPropVal ?></span><?php
							}
							?>
						</div>
						<?php if ($showSpecExpand): ?>
						<button data-item-expandable-handle="show" class="btn-text btn-text_1 btn-text_primary">Все характеристики</button>
						<button data-item-expandable-handle="hide" class="btn-text btn-text_1 btn-text_primary">Свернуть характеристики</button>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<hr class="hr1 block4">
	<div class="tabs1 block1" data-container>
		<ul class="tabs-nav2" role="tablist" id="<?=$itemIds['TABS_ID']?>">
			<?php if ($showDescription): ?>
			<li class="tabs-nav2__item" role="presentation">
				<button id="catalogDetail1" data-action="tab1" data-tab-btn="1" data-entity="tab" data-value="description" class="btn btn_tab1 btn_active" type="button" role="tab" aria-selected="true"><span><?=$arParams['MESS_DESCRIPTION_TAB']?></span></button>
			</li>
			<?php endif; ?>
			<?php if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS']): ?>
			<li class="tabs-nav2__item" role="presentation">
				<button id="catalogDetail2" data-action="tab1" data-tab-btn="2" data-entity="tab" data-value="properties" class="btn btn_tab1" type="button" role="tab" aria-selected="false"><span><?=$arParams['MESS_PROPERTIES_TAB']?></span></button>
			</li>
			<?php endif; ?>
			<?php if ($arParams['USE_COMMENTS'] === 'Y'): ?>
			<li class="tabs-nav2__item" role="presentation">
				<button id="catalogDetail3" data-action="tab1" data-tab-btn="3" data-entity="tab" data-value="comments" class="btn btn_tab1" type="button" role="tab" aria-selected="false"><span><?=$arParams['MESS_COMMENTS_TAB']?></span></button>
			</li>
			<?php endif; ?>
		</ul>
		<div id="<?=$itemIds['TAB_CONTAINERS_ID']?>">
			<?php if ($showDescription): ?>
				<div role="tabpanel" tabindex="0" aria-labelledby="catalogDetail1" class="tabs1__content tabs1__content_active product-item-detail-tab-content active textblock textblock_small" data-entity="tab-container" data-value="description" data-tab-content="1" itemprop="description" id="<?=$itemIds['DESCRIPTION_ID']?>">
								<?php
								if (
									$arResult['PREVIEW_TEXT'] != ''
									&& (
										$arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'S'
										|| ($arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'E' && $arResult['DETAIL_TEXT'] == '')
									)
								)
								{
									echo $arResult['PREVIEW_TEXT_TYPE'] === 'html' ? $arResult['PREVIEW_TEXT'] : '<p>'.$arResult['PREVIEW_TEXT'].'</p>';
								}

								if ($arResult['DETAIL_TEXT'] != '')
								{
									echo $arResult['DETAIL_TEXT_TYPE'] === 'html' ? $arResult['DETAIL_TEXT'] : '<p>'.$arResult['DETAIL_TEXT'].'</p>';
								}
								?>
				</div>
			<?php endif; ?>
			<?php if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS']): ?>
				<div role="tabpanel" tabindex="0" aria-labelledby="catalogDetail2" class="tabs1__content product-item-detail-tab-content" data-entity="tab-container" data-value="properties" data-tab-content="2">
					<div class="speclist2 columns1">
								<?php
								$safeTabValue = function ($v) {
									return htmlspecialcharsbx(html_entity_decode((string)$v, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
								};
								if (!empty($arResult['DISPLAY_PROPERTIES']))
								{
									foreach ($arResult['DISPLAY_PROPERTIES'] as $property)
									{
										$tabVal = is_array($property['DISPLAY_VALUE'])
											? implode(', ', array_map($safeTabValue, $property['DISPLAY_VALUE']))
											: $safeTabValue($property['DISPLAY_VALUE']);
										?><p class="speclist2__row">
										<span class="speclist2__key"><?= htmlspecialcharsbx($property['NAME']) ?></span>
										<span class="speclist2__value"><?= $tabVal ?></span>
									</p><?php
									}
									unset($property, $tabVal);
								}

								if ($arResult['SHOW_OFFERS_PROPS'])
								{
									?><div id="<?= $itemIds['DISPLAY_PROP_DIV'] ?>"></div><?php
								}
								unset($safeTabValue);
								?>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($arParams['USE_COMMENTS'] === 'Y'): ?>
				<div role="tabpanel" tabindex="0" aria-labelledby="catalogDetail3" class="tabs1__content product-item-detail-tab-content" data-entity="tab-container" data-value="comments" data-tab-content="3" style="display: none;">
								<?php
								$componentCommentsParams = array(
									'ELEMENT_ID' => $arResult['ID'],
									'ELEMENT_CODE' => '',
									'IBLOCK_ID' => $arParams['IBLOCK_ID'],
									'SHOW_DEACTIVATED' => $arParams['SHOW_DEACTIVATED'],
									'URL_TO_COMMENT' => '',
									'WIDTH' => '',
									'COMMENTS_COUNT' => '5',
									'BLOG_USE' => $arParams['BLOG_USE'],
									'FB_USE' => $arParams['FB_USE'],
									'FB_APP_ID' => $arParams['FB_APP_ID'],
									'VK_USE' => $arParams['VK_USE'],
									'VK_API_ID' => $arParams['VK_API_ID'],
									'CACHE_TYPE' => $arParams['CACHE_TYPE'],
									'CACHE_TIME' => $arParams['CACHE_TIME'],
									'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
									'BLOG_TITLE' => '',
									'BLOG_URL' => $arParams['BLOG_URL'],
									'PATH_TO_SMILE' => '',
									'EMAIL_NOTIFY' => $arParams['BLOG_EMAIL_NOTIFY'],
									'AJAX_POST' => 'Y',
									'SHOW_SPAM' => 'Y',
									'SHOW_RATING' => 'N',
									'FB_TITLE' => '',
									'FB_USER_ADMIN_ID' => '',
									'FB_COLORSCHEME' => 'light',
									'FB_ORDER_BY' => 'reverse_time',
									'VK_TITLE' => '',
									'TEMPLATE_THEME' => $arParams['~TEMPLATE_THEME']
								);
								if(isset($arParams["USER_CONSENT"]))
									$componentCommentsParams["USER_CONSENT"] = $arParams["USER_CONSENT"];
								if(isset($arParams["USER_CONSENT_ID"]))
									$componentCommentsParams["USER_CONSENT_ID"] = $arParams["USER_CONSENT_ID"];
								if(isset($arParams["USER_CONSENT_IS_CHECKED"]))
									$componentCommentsParams["USER_CONSENT_IS_CHECKED"] = $arParams["USER_CONSENT_IS_CHECKED"];
								if(isset($arParams["USER_CONSENT_IS_LOADED"]))
									$componentCommentsParams["USER_CONSENT_IS_LOADED"] = $arParams["USER_CONSENT_IS_LOADED"];
								$APPLICATION->IncludeComponent(
									'bitrix:catalog.comments',
									'',
									$componentCommentsParams,
									$component,
									array('HIDE_ICONS' => 'Y')
								);
								?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<meta itemprop="name" content="<?=$name?>" />
	<meta itemprop="category" content="<?=$arResult['CATEGORY_PATH']?>" />
	<?php
	if ($haveOffers)
	{
		foreach ($arResult['JS_OFFERS'] as $offer)
		{
			$currentOffersList = array();

			if (!empty($offer['TREE']) && is_array($offer['TREE']))
			{
				foreach ($offer['TREE'] as $propName => $skuId)
				{
					$propId = (int)mb_substr($propName, 5);

					foreach ($skuProps as $prop)
					{
						if ($prop['ID'] == $propId)
						{
							foreach ($prop['VALUES'] as $propId => $propValue)
							{
								if ($propId == $skuId)
								{
									$currentOffersList[] = $propValue['NAME'];
									break;
								}
							}
						}
					}
				}
			}

			$offerPrice = $offer['ITEM_PRICES'][$offer['ITEM_PRICE_SELECTED']];
			?>
			<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<meta itemprop="sku" content="<?=htmlspecialcharsbx(implode('/', $currentOffersList))?>" />
				<meta itemprop="price" content="<?=$offerPrice['RATIO_PRICE']?>" />
				<meta itemprop="priceCurrency" content="<?=$offerPrice['CURRENCY']?>" />
				<link itemprop="availability" href="http://schema.org/<?=($offer['CAN_BUY'] ? 'InStock' : 'OutOfStock')?>" />
			</span>
			<?php
		}

		unset($offerPrice, $currentOffersList);
	}
	else
	{
		?>
		<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<meta itemprop="price" content="<?=$price['RATIO_PRICE']?>" />
			<meta itemprop="priceCurrency" content="<?=$price['CURRENCY']?>" />
			<link itemprop="availability" href="http://schema.org/<?=($actualItem['CAN_BUY'] ? 'InStock' : 'OutOfStock')?>" />
		</span>
		<?php
	}
	?>
</div>
<?php
if ($haveOffers)
{
	$offerIds = array();
	$offerCodes = array();

	$useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';

	foreach ($arResult['JS_OFFERS'] as $ind => &$jsOffer)
	{
		$offerIds[] = (int)$jsOffer['ID'];
		$offerCodes[] = $jsOffer['CODE'];

		$fullOffer = $arResult['OFFERS'][$ind];
		$measureName = $fullOffer['ITEM_MEASURE']['TITLE'];

		$strAllProps = '';
		$strMainProps = '';
		$strPriceRangesRatio = '';
		$strPriceRanges = '';

		if ($arResult['SHOW_OFFERS_PROPS'])
		{
			$jsOffer['MAIN_PROP_NAME'] = '';
			$jsOffer['MAIN_PROP_VALUE'] = '';
			if (!empty($jsOffer['DISPLAY_PROPERTIES']))
			{
				$safeMainValue = function ($v) {
					return htmlspecialcharsbx(html_entity_decode((string)$v, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
				};
				$mainBlockCodes = isset($arParams['MAIN_BLOCK_OFFERS_PROPERTY_CODE']) && is_array($arParams['MAIN_BLOCK_OFFERS_PROPERTY_CODE'])
					? $arParams['MAIN_BLOCK_OFFERS_PROPERTY_CODE']
					: [];
				$useFirstAsMain = empty($mainBlockCodes);
				foreach ($jsOffer['DISPLAY_PROPERTIES'] as $property)
				{
					$propVal = is_array($property['VALUE'])
						? implode(' / ', array_map($safeMainValue, $property['VALUE']))
						: $safeMainValue($property['VALUE']);
					$current = '<p class="speclist2__row"><span class="speclist2__key">'.htmlspecialcharsbx($property['NAME']).'</span><span class="speclist2__value">'.$propVal.'</span></p>';
					$strAllProps .= $current;

					$isMain = $useFirstAsMain || isset($mainBlockCodes[$property['CODE']]);
					if ($isMain)
					{
						$strMainProps .= $current;
						// Для JS: имя и значение отдельно для обновления при смене оффера
						if ($jsOffer['MAIN_PROP_NAME'] === '')
						{
							$jsOffer['MAIN_PROP_NAME'] = $property['NAME'];
							$jsOffer['MAIN_PROP_VALUE'] = is_array($property['VALUE'])
								? implode(' / ', $property['VALUE'])
								: (string)$property['VALUE'];
							if ($useFirstAsMain)
								break; // только первое свойство как главное при пустом MAIN_BLOCK_OFFERS_PROPERTY_CODE
						}
					}
				}

				unset($current, $propVal, $safeMainValue, $mainBlockCodes, $useFirstAsMain);
			}
		}

		if ($arParams['USE_PRICE_COUNT'] && count($jsOffer['ITEM_QUANTITY_RANGES']) > 1)
		{
			$strPriceRangesRatio = '('.Loc::getMessage(
					'CT_BCE_CATALOG_RATIO_PRICE',
					array('#RATIO#' => ($useRatio
							? $fullOffer['ITEM_MEASURE_RATIOS'][$fullOffer['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']
							: '1'
						).' '.$measureName)
				).')';

			foreach ($jsOffer['ITEM_QUANTITY_RANGES'] as $range)
			{
				if ($range['HASH'] !== 'ZERO-INF')
				{
					$itemPrice = false;

					foreach ($jsOffer['ITEM_PRICES'] as $itemPrice)
					{
						if ($itemPrice['QUANTITY_HASH'] === $range['HASH'])
						{
							break;
						}
					}

					if ($itemPrice)
					{
						$strPriceRanges .= '<dt>'.Loc::getMessage(
								'CT_BCE_CATALOG_RANGE_FROM',
								array('#FROM#' => $range['SORT_FROM'].' '.$measureName)
							).' ';

						if (is_infinite($range['SORT_TO']))
						{
							$strPriceRanges .= Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
						}
						else
						{
							$strPriceRanges .= Loc::getMessage(
								'CT_BCE_CATALOG_RANGE_TO',
								array('#TO#' => $range['SORT_TO'].' '.$measureName)
							);
						}

						$strPriceRanges .= '</dt><dd>'.($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE']).'</dd>';
					}
				}
			}

			unset($range, $itemPrice);
		}

		$jsOffer['DISPLAY_PROPERTIES'] = $strAllProps;
		$jsOffer['DISPLAY_PROPERTIES_MAIN_BLOCK'] = $strMainProps;
		$jsOffer['PRICE_RANGES_RATIO_HTML'] = $strPriceRangesRatio;
		$jsOffer['PRICE_RANGES_HTML'] = $strPriceRanges;
	}

	$templateData['OFFER_IDS'] = $offerIds;
	$templateData['OFFER_CODES'] = $offerCodes;
	unset($jsOffer, $strAllProps, $strMainProps, $strPriceRanges, $strPriceRangesRatio, $useRatio);

	$jsParams = array(
		'CONFIG' => array(
			'USE_CATALOG' => $arResult['CATALOG'],
			'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_PRICE' => true,
			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
			'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
			'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
			'SHOW_SKU_PROPS' => $arResult['SHOW_OFFERS_PROPS'],
			'OFFER_GROUP' => $arResult['OFFER_GROUP'],
			'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
			'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
			'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
			'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
			'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
			'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
			'USE_STICKERS' => true,
			'USE_SUBSCRIBE' => $showSubscribe,
			'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
			'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
			'ALT' => $alt,
			'TITLE' => $title,
			'MAGNIFIER_ZOOM_PERCENT' => 200,
			'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
			'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
			'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
				? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
				: null,
			'SHOW_SKU_DESCRIPTION' => $arParams['SHOW_SKU_DESCRIPTION'],
			'DISPLAY_PREVIEW_TEXT_MODE' => $arParams['DISPLAY_PREVIEW_TEXT_MODE']
		),
		'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
		'VISUAL' => $itemIds,
		'DEFAULT_PICTURE' => array(
			'PREVIEW_PICTURE' => $arResult['DEFAULT_PICTURE'],
			'DETAIL_PICTURE' => $arResult['DEFAULT_PICTURE']
		),
		'PRODUCT' => array(
			'ID' => $arResult['ID'],
			'ACTIVE' => $arResult['ACTIVE'],
			'NAME' => $arResult['~NAME'],
			'CATEGORY' => $arResult['CATEGORY_PATH'],
			'DETAIL_TEXT' => $arResult['DETAIL_TEXT'],
			'DETAIL_TEXT_TYPE' => $arResult['DETAIL_TEXT_TYPE'],
			'PREVIEW_TEXT' => $arResult['PREVIEW_TEXT'],
			'PREVIEW_TEXT_TYPE' => $arResult['PREVIEW_TEXT_TYPE']
		),
		'BASKET' => array(
			'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
			'BASKET_URL' => $arParams['BASKET_URL'],
			'SKU_PROPS' => $arResult['OFFERS_PROP_CODES'],
			'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
			'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
		),
		'OFFERS' => $arResult['JS_OFFERS'],
		'OFFER_SELECTED' => $arResult['OFFERS_SELECTED'],
		'TREE_PROPS' => $skuProps
	);
}
else
{
	$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
	if ($arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !$emptyProductProperties)
	{
		?>
		<div id="<?=$itemIds['BASKET_PROP_DIV']?>" style="display: none;">
			<?php
			if (!empty($arResult['PRODUCT_PROPERTIES_FILL']))
			{
				foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propId => $propInfo)
				{
					?>
					<input type="hidden" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]" value="<?=htmlspecialcharsbx($propInfo['ID'])?>">
					<?php
					unset($arResult['PRODUCT_PROPERTIES'][$propId]);
				}
			}

			$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
			if (!$emptyProductProperties)
			{
				?>
				<table>
					<?php
					foreach ($arResult['PRODUCT_PROPERTIES'] as $propId => $propInfo)
					{
						?>
						<tr>
							<td><?=$arResult['PROPERTIES'][$propId]['NAME']?></td>
							<td>
								<?php
								if (
									$arResult['PROPERTIES'][$propId]['PROPERTY_TYPE'] === 'L'
									&& $arResult['PROPERTIES'][$propId]['LIST_TYPE'] === 'C'
								)
								{
									foreach ($propInfo['VALUES'] as $valueId => $value)
									{
										?>
										<label>
											<input type="radio" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]"
												value="<?=$valueId?>" <?=($valueId == $propInfo['SELECTED'] ? 'checked' : '')?>>
											<?=$value?>
										</label>
										<br>
										<?php
									}
								}
								else
								{
									?>
									<select name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]">
										<?php
										foreach ($propInfo['VALUES'] as $valueId => $value)
										{
											?>
											<option value="<?=$valueId?>" <?=($valueId == $propInfo['SELECTED'] ? 'selected' : '')?>>
												<?=$value?>
											</option>
											<?php
										}
										?>
									</select>
									<?php
								}
								?>
							</td>
						</tr>
						<?php
					}
					?>
				</table>
				<?php
			}
			?>
		</div>
		<?php
	}

	$jsParams = array(
		'CONFIG' => array(
			'USE_CATALOG' => $arResult['CATALOG'],
			'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_PRICE' => !empty($arResult['ITEM_PRICES']),
			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
			'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
			'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
			'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
			'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
			'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
			'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
			'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
			'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
			'USE_STICKERS' => true,
			'USE_SUBSCRIBE' => $showSubscribe,
			'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
			'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
			'ALT' => $alt,
			'TITLE' => $title,
			'MAGNIFIER_ZOOM_PERCENT' => 200,
			'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
			'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
			'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
				? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
				: null
		),
		'VISUAL' => $itemIds,
		'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
		'PRODUCT' => array(
			'ID' => $arResult['ID'],
			'ACTIVE' => $arResult['ACTIVE'],
			'PICT' => reset($arResult['MORE_PHOTO']),
			'NAME' => $arResult['~NAME'],
			'SUBSCRIPTION' => true,
			'ITEM_PRICE_MODE' => $arResult['ITEM_PRICE_MODE'],
			'ITEM_PRICES' => $arResult['ITEM_PRICES'],
			'ITEM_PRICE_SELECTED' => $arResult['ITEM_PRICE_SELECTED'],
			'ITEM_QUANTITY_RANGES' => $arResult['ITEM_QUANTITY_RANGES'],
			'ITEM_QUANTITY_RANGE_SELECTED' => $arResult['ITEM_QUANTITY_RANGE_SELECTED'],
			'ITEM_MEASURE_RATIOS' => $arResult['ITEM_MEASURE_RATIOS'],
			'ITEM_MEASURE_RATIO_SELECTED' => $arResult['ITEM_MEASURE_RATIO_SELECTED'],
			'SLIDER_COUNT' => $arResult['MORE_PHOTO_COUNT'],
			'SLIDER' => $arResult['MORE_PHOTO'],
			'CAN_BUY' => $arResult['CAN_BUY'],
			'CHECK_QUANTITY' => $arResult['CHECK_QUANTITY'],
			'QUANTITY_FLOAT' => is_float($arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']),
			'MAX_QUANTITY' => $arResult['PRODUCT']['QUANTITY'],
			'STEP_QUANTITY' => $arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'],
			'CATEGORY' => $arResult['CATEGORY_PATH']
		),
		'BASKET' => array(
			'ADD_PROPS' => $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y',
			'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
			'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
			'EMPTY_PROPS' => $emptyProductProperties,
			'BASKET_URL' => $arParams['BASKET_URL'],
			'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
			'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
		)
	);
	unset($emptyProductProperties);
}

if ($arParams['DISPLAY_COMPARE'])
{
	$jsParams['COMPARE'] = array(
		'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
		'COMPARE_DELETE_URL_TEMPLATE' => $arResult['~COMPARE_DELETE_URL_TEMPLATE'],
		'COMPARE_PATH' => $arParams['COMPARE_PATH']
	);
}

$jsParams["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"] =
	$arResult["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"]
;

?>
<script>
	BX.message({
		ECONOMY_INFO_MESSAGE: '<?=GetMessageJS('CT_BCE_CATALOG_ECONOMY_INFO2')?>',
		TITLE_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR')?>',
		TITLE_BASKET_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS')?>',
		BASKET_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR')?>',
		BTN_SEND_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS')?>',
		BTN_MESSAGE_DETAIL_BASKET_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_BASKET_REDIRECT')?>',
		BTN_MESSAGE_CLOSE: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE')?>',
		BTN_MESSAGE_DETAIL_CLOSE_POPUP: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE_POPUP')?>',
		TITLE_SUCCESSFUL: '<?=GetMessageJS('CT_BCE_CATALOG_ADD_TO_BASKET_OK')?>',
		COMPARE_MESSAGE_OK: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_OK')?>',
		COMPARE_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_UNKNOWN_ERROR')?>',
		COMPARE_TITLE: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_TITLE')?>',
		BTN_MESSAGE_COMPARE_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT')?>',
		PRODUCT_GIFT_LABEL: '<?=GetMessageJS('CT_BCE_CATALOG_PRODUCT_GIFT_LABEL')?>',
		PRICE_TOTAL_PREFIX: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_PRICE_TOTAL_PREFIX')?>',
		RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY'])?>',
		RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW'])?>',
		SITE_ID: '<?=CUtil::JSEscape($component->getSiteId())?>'
	});

	var <?=$obName?> = new JCCatalogElement(<?=CUtil::PhpToJSObject($jsParams, false, true)?>);
</script>
<?php
unset($actualItem, $itemIds, $jsParams);
