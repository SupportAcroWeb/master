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
	<tr class="basket-item{{#SHOW_RESTORE}} basket-item-restore-mode{{/SHOW_RESTORE}}{{#NOT_AVAILABLE}} basket-item-not-available{{/NOT_AVAILABLE}}"
		id="basket-item-{{ID}}" data-entity="basket-item" data-id="{{ID}}">
		{{#SHOW_RESTORE}}
            <td class="cart-table__cell1"></td>
			<td class="basket-items-list-item-notification cart-table__cell2" id="basket-item-height-aligner-{{ID}}">
				<div class="basket-items-list-item-removed-container">
					<div>
						<?= Loc::getMessage('SBB_BASKET_ITEM_DELETED_MSGVER_1', ['#NAME#' => '<strong>{{NAME}}</strong>']) ?>
					</div>
				</div>
			</td>
            <td class="cart-table__cell3"></td>
            <td class="cart-table__cell4"></td>
            <td class="cart-table__cell5">
                <div class="basket-items-list-item-removed-block">
                    <a href="javascript:void(0)" data-entity="basket-item-restore-button">
                        <?=Loc::getMessage('SBB_BASKET_ITEM_RESTORE')?>
                    </a>
                    <span class="basket-items-list-item-clear-btn" data-entity="basket-item-close-restore-button"></span>
                </div>
            </td>
		{{/SHOW_RESTORE}}
		{{^SHOW_RESTORE}}
			<td class="cart-table__cell1">
				<?
				if (in_array('PREVIEW_PICTURE', $arParams['COLUMNS_LIST']))
				{
					?>
					<div class="cart-table__preview">
						{{#SHOW_LABEL}}
							<div class="cart-table__badges">
								{{#LABEL_VALUES}}
									<span class="badge1 {{CLASS}}" title="{{NAME}}">{{NAME}}</span>
								{{/LABEL_VALUES}}
							</div>
						{{/SHOW_LABEL}}
						{{#DETAIL_PAGE_URL}}
							<a href="{{DETAIL_PAGE_URL}}">
						{{/DETAIL_PAGE_URL}}
						<img src="{{{IMAGE_URL}}}{{^IMAGE_URL}}<?=$templateFolder?>/images/no_photo.png{{/IMAGE_URL}}" alt="{{NAME}}">
						{{#DETAIL_PAGE_URL}}
							</a>
						{{/DETAIL_PAGE_URL}}
					</div>
					<?
				}
				?>
			</td>

			<td class="cart-table__cell2">
				<div class="cart-table__name">
					{{#DETAIL_PAGE_URL}}
						<a href="{{DETAIL_PAGE_URL}}" data-entity="basket-item-name">
					{{/DETAIL_PAGE_URL}}
					{{NAME}}
					{{#DETAIL_PAGE_URL}}
						</a>
					{{/DETAIL_PAGE_URL}}
				</div>

				{{#PREVIEW_TEXT}}
					<div class="cart-table__article">{{PREVIEW_TEXT}}</div>
				{{/PREVIEW_TEXT}}

				<div class="cart-table__parameters">
					<div class="cart-table__specs">
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
												<div
													class="cart-table__spec"
													data-entity="basket-item-property-value"
													data-property-code="{{CODE}}"
												>
													{{{NAME}}}<span>{{{VALUE}}}</span>
												</div>
											{{/PROPS}}
											<?
										}
										break;

									case 'sku':
										 /* ?>
										{{#SKU_BLOCK_LIST}}
											<div class="basket-item-property-sku" data-entity="basket-item-sku-block">
												<span>{{NAME}}</span>
												<span>
													<ul class="basket-item-scu-list">
														{{#SKU_VALUES_LIST}}
															<li
																class="basket-item-scu-item{{#SELECTED}} selected{{/SELECTED}}{{#NOT_AVAILABLE_OFFER}} not-available{{/NOT_AVAILABLE_OFFER}}"
																title="{{NAME}}"
																data-entity="basket-item-sku-field"
																data-initial="{{#SELECTED}}true{{/SELECTED}}{{^SELECTED}}false{{/SELECTED}}"
																data-value-id="{{VALUE_ID}}"
																data-sku-name="{{NAME}}"
																data-property="{{PROP_CODE}}"
															>
																{{#IS_IMAGE}}
																	<span class="basket-item-scu-item-inner" style="background-image: url({{PICT}});"></span>
																{{/IS_IMAGE}}
																{{^IS_IMAGE}}
																	<span class="basket-item-scu-item-inner">{{NAME}}</span>
																{{/IS_IMAGE}}
															</li>
														{{/SKU_VALUES_LIST}}
													</ul>
												</span>
											</div>
										{{/SKU_BLOCK_LIST}} */?>

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
											{{#IS_TEXT}}
												<div class="cart-table__spec" data-entity="basket-item-property">
													{{NAME}}<span data-column-property-code="{{CODE}}" data-entity="basket-item-property-column-value">{{VALUE}}</span>
												</div>
											{{/IS_TEXT}}
											{{#IS_HTML}}
												<div class="cart-table__spec" data-entity="basket-item-property">
													{{NAME}}<span data-column-property-code="{{CODE}}" data-entity="basket-item-property-column-value">{{{VALUE}}}</span>
												</div>
											{{/IS_HTML}}
											{{#IS_LINK}}
												<div class="cart-table__spec" data-entity="basket-item-property">
													{{NAME}}
													<span data-column-property-code="{{CODE}}" data-entity="basket-item-property-column-value">
														{{#VALUE}}{{{LINK}}}{{^IS_LAST}}<br>{{/IS_LAST}}{{/VALUE}}
													</span>
												</div>
											{{/IS_LINK}}
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
										{{/COLUMN_LIST}}
										<?
										break;
								}
							}
						}
						?>
					</div>
				</div>

				<div class="cart-table__status {{#NOT_AVAILABLE}}outofstock{{/NOT_AVAILABLE}}{{^NOT_AVAILABLE}}instock{{/NOT_AVAILABLE}}">
					{{#NOT_AVAILABLE}}
						<?=Loc::getMessage('SBB_BASKET_ITEM_NOT_AVAILABLE')?>
					{{/NOT_AVAILABLE}}
					{{^NOT_AVAILABLE}}
						<?=Loc::getMessage('SBB_BASKET_ITEM_AVAILABLE')?>
					{{/NOT_AVAILABLE}}
				</div>

				<div class="cart-table__controls">
					<button data-action="showSpecs" class="card-product3__btn-info" type="button">
						<svg aria-hidden="true" width="6" height="15">
							<use xlink:href="<?=SITE_TEMPLATE_PATH?>/img/sprite.svg#info1"></use>
						</svg>
						<span class="v-h"><?=Loc::getMessage('SBB_CHARACTERISTICS')?></span>
					</button>
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
			</td>

			<td class="cart-table__cell3">
				<div class="cart-table__quantity">
					<div class="stepcounter{{#NOT_AVAILABLE}} disabled{{/NOT_AVAILABLE}}" data-entity="basket-item-quantity-block">
						<button class="stepcounter__btn" type="button" data-entity="basket-item-quantity-minus"{{#NOT_AVAILABLE}} disabled{{/NOT_AVAILABLE}}>
							<svg aria-hidden="true" width="14" height="2">
								<use xlink:href="<?=SITE_TEMPLATE_PATH?>/img/sprite.svg#minus2"></use>
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
								<use xlink:href="<?=SITE_TEMPLATE_PATH?>/img/sprite.svg#plus2"></use>
							</svg>
							<span class="v-h"><?=Loc::getMessage('SBB_BASKET_ITEM_INCREASE')?></span>
						</button>
					</div>
					<?
					if ($usePriceInAdditionalColumn)
					{
						?>
						{{#IS_PRICE_ZERO}}
							<div class="cart-table__price-per-unit no_price">Цена по запросу</div>
						{{/IS_PRICE_ZERO}}
						{{^IS_PRICE_ZERO}}
							<div class="cart-table__price-per-unit">
								<span id="basket-item-price-{{ID}}">{{{BASE_PRICE_FORMATED}}}</span>
								(1 {{MEASURE_TEXT}})
							</div>
						{{/IS_PRICE_ZERO}}
						<?
					}
					?>
				</div>
			</td>

			<td class="cart-table__cell4">
				<?
				if ($useSumColumn)
				{
					?>
					{{#IS_PRICE_ZERO}}
						<div class="cart-table__price2 no_price">Цена по запросу</div>
					{{/IS_PRICE_ZERO}}
					{{^IS_PRICE_ZERO}}
                        {{#SHOW_DISCOUNT_PRICE}}
                        <div class="cart-table__price1">{{{BASE_FULL_PRICE_FORMATED}}}</div>
                        {{/SHOW_DISCOUNT_PRICE}}
						<div class="cart-table__price2" id="basket-item-sum-price-{{ID}}">{{{SUM_PRICE_FORMATED}}}</div>

					{{/IS_PRICE_ZERO}}
					<?
				}
				?>
			</td>

			<?
			if ($useActionColumn)
			{
				?>
				<td class="cart-table__cell5">
					<button class="cart-table__btn" type="button" data-entity="basket-item-delete">
						<svg width="18" height="20" aria-hidden="true">
							<use xlink:href="<?=SITE_TEMPLATE_PATH?>/img/sprite.svg#bin1"></use>
						</svg>
					</button>
				</td>
				<?
			}
			?>

		{{/SHOW_RESTORE}}
	</tr>
</script>
