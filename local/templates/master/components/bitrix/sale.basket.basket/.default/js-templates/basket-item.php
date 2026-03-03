<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $mobileColumns
 * @var array $arParams
 * @var string $templateFolder
 */

$usePriceInAdditionalColumn = in_array('PRICE', $arParams['COLUMNS_LIST']) && $arParams['PRICE_DISPLAY_MODE'] === 'Y';
$useSumColumn = in_array('SUM', $arParams['COLUMNS_LIST']);
$useActionColumn = in_array('DELETE', $arParams['COLUMNS_LIST']);

$positionClassMap = array(
	'left' => 'basket-item-label-left',
	'center' => 'basket-item-label-center',
	'right' => 'basket-item-label-right',
	'bottom' => 'basket-item-label-bottom',
	'middle' => 'basket-item-label-middle',
	'top' => 'basket-item-label-top'
);

$discountPositionClass = '';
if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION']))
{
	foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos)
	{
		$discountPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}

$labelPositionClass = '';
if (!empty($arParams['LABEL_PROP_POSITION']))
{
	foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos)
	{
		$labelPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}

?>
<script id="basket-item-template" type="text/html">
	<div class="card-product3 card-product4{{#SHOW_RESTORE}} basket-item-restore-mode{{/SHOW_RESTORE}}{{#NOT_AVAILABLE}} basket-item-not-available{{/NOT_AVAILABLE}}"
		id="basket-item-{{ID}}" data-entity="basket-item" data-id="{{ID}}">
		{{#SHOW_RESTORE}}
			<div class="basket-items-list-item-notification" id="basket-item-height-aligner-{{ID}}">
				{{#SHOW_LOADING}}
					<div class="basket-items-list-item-overlay"></div>
				{{/SHOW_LOADING}}
				<div class="basket-items-list-item-removed-container">
					<div>
						<?= Loc::getMessage('SBB_BASKET_ITEM_DELETED_MSGVER_1', ['#NAME#' => '<strong>{{NAME}}</strong>']) ?>
					</div>
					<div class="basket-items-list-item-removed-block">
						<a href="javascript:void(0)" data-entity="basket-item-restore-button">
							<?=Loc::getMessage('SBB_BASKET_ITEM_RESTORE')?>
						</a>
						<span class="basket-items-list-item-clear-btn" data-entity="basket-item-close-restore-button"></span>
					</div>
				</div>
			</div>
		{{/SHOW_RESTORE}}
		{{^SHOW_RESTORE}}
			<?
			if (in_array('PREVIEW_PICTURE', $arParams['COLUMNS_LIST']))
			{
				?>
				<div class="card-product3__col-photo">
					{{#DETAIL_PAGE_URL}}
						<a href="{{DETAIL_PAGE_URL}}">
					{{/DETAIL_PAGE_URL}}

					<img src="{{{IMAGE_URL}}}{{^IMAGE_URL}}<?=$templateFolder?>/images/no_photo.png{{/IMAGE_URL}}" alt="{{NAME}}">

					{{#DETAIL_PAGE_URL}}
						</a>
					{{/DETAIL_PAGE_URL}}

					{{#SHOW_LABEL}}
						<div class="card-product3__badges">
							{{#LABEL_VALUES}}
								<span class="badge1 {{CLASS}}" title="{{NAME}}">{{NAME}}</span>
							{{/LABEL_VALUES}}
						</div>
					{{/SHOW_LABEL}}
				</div>
				<?
			}
			?>

			<div class="card-product3__col-data">
				<div class="card-product3__col-specs">
					<div class="card-product3__label2"><?=Loc::getMessage('SBB_CHARACTERISTICS')?></div>
					<div class="card-product3__specs">
						<?
						if (!empty($arParams['PRODUCT_BLOCKS_ORDER']))
						{
							foreach ($arParams['PRODUCT_BLOCKS_ORDER'] as $blockName)
							{
								switch (trim((string)$blockName))
								{
									case 'props':
										if (in_array('PROPS', $arParams['COLUMNS_LIST']))
										{
											?>
											{{#PROPS}}
												<div data-entity="basket-item-property-value" data-property-code="{{CODE}}">
													<span>{{{NAME}}}</span>
													<span>{{{VALUE}}}</span>
												</div>
											{{/PROPS}}
											<?
										}
										break;

									case 'sku':
										?>
										{{#SKU_BLOCK_LIST}}
									 		{{#IS_IMAGE}}
												<div class="basket-item-property-sku" data-entity="basket-item-sku-block">
													<span>{{NAME}}</span>
													<span>
														<ul class="basket-item-scu-list">
															{{#SKU_VALUES_LIST}}
																<li class="basket-item-scu-item{{#SELECTED}} selected{{/SELECTED}}{{#NOT_AVAILABLE_OFFER}} not-available{{/NOT_AVAILABLE_OFFER}}"
																	title="{{NAME}}"
								 									data-entity="basket-item-sku-field"
																	data-initial="{{#SELECTED}}true{{/SELECTED}}{{^SELECTED}}false{{/SELECTED}}"
																	data-value-id="{{VALUE_ID}}"
																	data-sku-name="{{NAME}}"
																	data-property="{{PROP_CODE}}">
																	<span class="basket-item-scu-item-inner" style="background-image: url({{PICT}});"></span>
																</li>
															{{/SKU_VALUES_LIST}}
														</ul>
													</span>
												</div>
											{{/IS_IMAGE}}

											{{^IS_IMAGE}}
												<div class="basket-item-property-sku" data-entity="basket-item-sku-block">
													<span>{{NAME}}</span>
													<span>
														<ul class="basket-item-scu-list">
															{{#SKU_VALUES_LIST}}
																<li class="basket-item-scu-item{{#SELECTED}} selected{{/SELECTED}}{{#NOT_AVAILABLE_OFFER}} not-available{{/NOT_AVAILABLE_OFFER}}"
																	title="{{NAME}}"
																	data-entity="basket-item-sku-field"
																	data-initial="{{#SELECTED}}true{{/SELECTED}}{{^SELECTED}}false{{/SELECTED}}"
																	data-value-id="{{VALUE_ID}}"
																	data-sku-name="{{NAME}}"
																	data-property="{{PROP_CODE}}">
																	<span class="basket-item-scu-item-inner">{{NAME}}</span>
																</li>
															{{/SKU_VALUES_LIST}}
														</ul>
													</span>
												</div>
											{{/IS_IMAGE}}
										{{/SKU_BLOCK_LIST}}

										{{#HAS_SIMILAR_ITEMS}}
											<div class="basket-items-list-item-double" data-entity="basket-item-sku-notification">
												<div class="alert alert-info alert-dismissable text-center">
													{{#USE_FILTER}}
														<a href="javascript:void(0)" class="basket-items-list-item-double-anchor" data-entity="basket-item-show-similar-link">
													{{/USE_FILTER}}
													<?=Loc::getMessage('SBB_BASKET_ITEM_SIMILAR_P1')?>{{#USE_FILTER}}</a>{{/USE_FILTER}}
													<?=Loc::getMessage('SBB_BASKET_ITEM_SIMILAR_P2')?>
													{{SIMILAR_ITEMS_QUANTITY}} {{MEASURE_TEXT}}
													<br>
													<a href="javascript:void(0)" class="basket-items-list-item-double-anchor" data-entity="basket-item-merge-sku-link">
														<?=Loc::getMessage('SBB_BASKET_ITEM_SIMILAR_P3')?>
														{{TOTAL_SIMILAR_ITEMS_QUANTITY}} {{MEASURE_TEXT}}?
													</a>
												</div>
											</div>
										{{/HAS_SIMILAR_ITEMS}}
										<?
										break;

									case 'columns':
										?>
										{{#COLUMN_LIST}}
											{{#IS_IMAGE}}
												<div class="basket-item-property-custom" data-entity="basket-item-property">
													<span>{{NAME}}</span>
													<span>
														{{#VALUE}}
															<img class="basket-item-custom-block-photo-item" src="{{{IMAGE_SRC}}}" data-image-index="{{INDEX}}" data-column-property-code="{{CODE}}">
														{{/VALUE}}
													</span>
												</div>
											{{/IS_IMAGE}}

											{{#IS_TEXT}}
												<div class="basket-item-property-custom" data-entity="basket-item-property">
													<span>{{NAME}}</span>
													<span data-column-property-code="{{CODE}}" data-entity="basket-item-property-column-value">{{VALUE}}</span>
												</div>
											{{/IS_TEXT}}

											{{#IS_HTML}}
												<div class="basket-item-property-custom" data-entity="basket-item-property">
													<span>{{NAME}}</span>
													<span data-column-property-code="{{CODE}}" data-entity="basket-item-property-column-value">{{{VALUE}}}</span>
												</div>
											{{/IS_HTML}}

											{{#IS_LINK}}
												<div class="basket-item-property-custom" data-entity="basket-item-property">
													<span>{{NAME}}</span>
													<span data-column-property-code="{{CODE}}" data-entity="basket-item-property-column-value">
														{{#VALUE}}{{{LINK}}}{{^IS_LAST}}<br>{{/IS_LAST}}{{/VALUE}}
													</span>
												</div>
											{{/IS_LINK}}
										{{/COLUMN_LIST}}
										<?
										break;
								}
							}
						}
						?>
					</div>
				</div>

				<div class="card-product3__name">
					{{#DETAIL_PAGE_URL}}
						<a href="{{DETAIL_PAGE_URL}}" data-entity="basket-item-name">
					{{/DETAIL_PAGE_URL}}
					{{NAME}}
					{{#DETAIL_PAGE_URL}}
						</a>
					{{/DETAIL_PAGE_URL}}
				</div>

				{{#PREVIEW_TEXT}}
					<div class="card-product3__description">{{PREVIEW_TEXT}}</div>
				{{/PREVIEW_TEXT}}

				<div class="card-product3__status {{#NOT_AVAILABLE}}status_outofstock{{/NOT_AVAILABLE}}{{^NOT_AVAILABLE}}status_instock{{/NOT_AVAILABLE}}">
					{{#NOT_AVAILABLE}}
						<?=Loc::getMessage('SBB_BASKET_ITEM_NOT_AVAILABLE')?>
					{{/NOT_AVAILABLE}}
					{{^NOT_AVAILABLE}}
						<?=Loc::getMessage('SBB_BASKET_ITEM_AVAILABLE')?>
					{{/NOT_AVAILABLE}}
				</div>

				{{#DELAYED}}
					<div class="basket-items-list-item-warning-container">
						<div class="alert alert-warning text-center">
							<?=Loc::getMessage('SBB_BASKET_ITEM_DELAYED')?>.
							<a href="javascript:void(0)" data-entity="basket-item-remove-delayed">
								<?=Loc::getMessage('SBB_BASKET_ITEM_REMOVE_DELAYED')?>
							</a>
						</div>
					</div>
				{{/DELAYED}}

				{{#WARNINGS.length}}
					<div class="basket-items-list-item-warning-container">
						<div class="alert alert-warning alert-dismissable" data-entity="basket-item-warning-node">
							<span class="close" data-entity="basket-item-warning-close">&times;</span>
							{{#WARNINGS}}
								<div data-entity="basket-item-warning-text">{{{.}}}</div>
							{{/WARNINGS}}
						</div>
					</div>
				{{/WARNINGS.length}}
			</div>

			<?
			if ($usePriceInAdditionalColumn)
			{
				?>
				<div class="card-product3__col1"{{#IS_PRICE_ZERO}} style="display: none;"{{/IS_PRICE_ZERO}}>
					<div>
						<div class="card-product3__label1">
							<?= Loc::getMessage('SBB_BASKET_ITEM_PRICE_FOR_MSGVER_1', ['#MEASURE_RATIO#' => '1', '#MEASURE_TEXT#' => '{{MEASURE_TEXT}}']) ?>
						</div>
						<span class="card-product3__price1" id="basket-item-price-{{ID}}">{{{BASE_PRICE_FORMATED}}}</span>
						{{#SHOW_DISCOUNT_PRICE}}
							<span class="card-product3__price2">{{{BASE_FULL_PRICE_FORMATED}}}</span>
						{{/SHOW_DISCOUNT_PRICE}}
					</div>
					{{#SHOW_LOADING}}
						<div class="basket-items-list-item-overlay"></div>
					{{/SHOW_LOADING}}
				</div>
				{{#IS_PRICE_ZERO}}
				<div class="card-product3__col1">
					<div>
						<div class="card-product3__label1">
							<?= Loc::getMessage('SBB_BASKET_ITEM_PRICE_FOR_MSGVER_1', ['#MEASURE_RATIO#' => '1', '#MEASURE_TEXT#' => '{{MEASURE_TEXT}}']) ?>
						</div>
						<span class="card-product3__price1 no_price">Цена по запросу</span>
					</div>
				</div>
				{{/IS_PRICE_ZERO}}
				<?
			}
			?>

			<div class="card-product3__col3">
				<div class="stepcounter{{#NOT_AVAILABLE}} disabled{{/NOT_AVAILABLE}}" data-entity="basket-item-quantity-block">
					<button class="stepcounter__btn" type="button" data-entity="basket-item-quantity-minus"{{#NOT_AVAILABLE}} disabled{{/NOT_AVAILABLE}}>
						<svg aria-hidden="true" width="14" height="2">
							<use xlink:href="<?=SITE_TEMPLATE_PATH?>/img/sprite.svg#minus1"></use>
						</svg>
						<span class="v-h"><?=Loc::getMessage('SBB_BASKET_ITEM_DECREASE')?></span>
					</button>
					<input class="stepcounter__input" type="number" value="{{QUANTITY}}"
						{{#NOT_AVAILABLE}} disabled="disabled"{{/NOT_AVAILABLE}}
						data-value="{{QUANTITY}}" 
						data-entity="basket-item-quantity-field"
						id="basket-item-quantity-{{ID}}" 
						readonly>
					<button class="stepcounter__btn" type="button" data-entity="basket-item-quantity-plus"{{#NOT_AVAILABLE}} disabled{{/NOT_AVAILABLE}}>
						<svg aria-hidden="true" width="14" height="14">
							<use xlink:href="<?=SITE_TEMPLATE_PATH?>/img/sprite.svg#plus1"></use>
						</svg>
						<span class="v-h"><?=Loc::getMessage('SBB_BASKET_ITEM_INCREASE')?></span>
					</button>
				</div>
				{{#SHOW_LOADING}}
					<div class="basket-items-list-item-overlay"></div>
				{{/SHOW_LOADING}}
			</div>

			<?
			if ($useSumColumn)
			{
				?>
				<div class="card-product3__col2"{{#IS_PRICE_ZERO}} style="display: none;"{{/IS_PRICE_ZERO}}>
					<div>
						<span><?=Loc::getMessage('SBB_BASKET_ITEM_SUM')?></span>
						<span class="card-product3__price3" id="basket-item-sum-price-{{ID}}">{{{SUM_PRICE_FORMATED}}}</span>
					</div>
					{{#SHOW_LOADING}}
						<div class="basket-items-list-item-overlay"></div>
					{{/SHOW_LOADING}}
				</div>
				{{#IS_PRICE_ZERO}}
				<div class="card-product3__col2">
					<div>
						<span class="card-product3__price3 no_price">Цена по запросу</span>
					</div>
				</div>
				{{/IS_PRICE_ZERO}}
				<?
			}
			?>

			<div class="card-product3__col-btn">
				<button data-action="showSpecs" class="card-product3__btn-info" type="button">
					<svg aria-hidden="true" width="6" height="15">
						<use xlink:href="<?=SITE_TEMPLATE_PATH?>/img/sprite.svg#info1"></use>
					</svg>
					<span class="v-h"><?=Loc::getMessage('SBB_CHARACTERISTICS')?></span>
				</button>
			</div>

			<?
			if ($useActionColumn)
			{
				?>
				<button type="button" class="card-product3__delete" data-entity="basket-item-delete">
					<img src="<?=SITE_TEMPLATE_PATH?>/img/delete.svg" alt="">
				</button>
				<?
			}
			?>

			{{#SHOW_LOADING}}
				<div class="basket-items-list-item-overlay"></div>
			{{/SHOW_LOADING}}
		{{/SHOW_RESTORE}}
	</div>
</script>
