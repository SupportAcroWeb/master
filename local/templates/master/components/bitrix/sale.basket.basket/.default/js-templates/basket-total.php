<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 */
?>
<script id="basket-total-template" type="text/html">
	<div class="details-block-cart__sticky" data-entity="basket-checkout-aligner">
        <div class="details-block-cart__list">
            <div class="details-block-cart__title">
                <?= Loc::getMessage('SBB_YOUR_BASKET') ?>
            </div>
            <div class="details-block-cart__item">
                <div class="details-block-cart__product">
                    {{ITEMS_TEXT}}
                </div>
                <div class="details-block-cart__cost" data-entity="basket-total-price-without-discount">
                    {{{PRICE_WITHOUT_DISCOUNT_FORMATED}}}
                </div>
            </div>
            {{#DISCOUNT_PRICE_FORMATED}}
                <div class="details-block-cart__item sale">
                    <div class="details-block-cart__product"><?= Loc::getMessage('SBB_DISCOUNT') ?></div>
                    <div class="details-block-cart__cost">-{{{DISCOUNT_PRICE_FORMATED}}}</div>
                </div>
            {{/DISCOUNT_PRICE_FORMATED}}
        </div>

        <?
        if ($arParams['HIDE_COUPON'] !== 'Y')
        {
            ?>
            <div class="details-block-cart__list basket-coupon-section">
                <div class="details-block-cart__title">
                    <?= Loc::getMessage('SBB_COUPON') ?>
                </div>
                <div class="details-block-cart__inputs basket-coupon-block-field">
                    <input class="field-input1" type="text" placeholder="<?= Loc::getMessage('SBB_COUPON_ENTER_MSGVER_1') ?>" data-entity="basket-coupon-input">
                    <button type="button" class="btn">
                        <svg aria-hidden="true" width="11" height="18">
                            <use xlink:href="<?=SITE_TEMPLATE_PATH?>/img/sprite.svg#chevron3"></use>
                        </svg>
                    </button>
                    <span class="basket-coupon-block-coupon-btn" data-entity="basket-coupon-block-coupon-btn" style="position: absolute; left: -9999px;"></span>
                </div>
            </div>

            <div class="basket-coupon-alert-section">
                <div class="basket-coupon-alert-inner">
                    {{#COUPON_LIST}}
                        <div class="basket-coupon-alert text-{{CLASS}}">
                            <span class="basket-coupon-text">
                                <strong>{{COUPON}}</strong> - <?=Loc::getMessage('SBB_COUPON')?> {{JS_CHECK_CODE}}
                                {{#DISCOUNT_NAME}}({{DISCOUNT_NAME}}){{/DISCOUNT_NAME}}
                            </span>
                            <span class="close-link" data-entity="basket-coupon-delete" data-coupon="{{COUPON}}">
                                <?=Loc::getMessage('SBB_DELETE')?>
                            </span>
                        </div>
                    {{/COUPON_LIST}}
                </div>
            </div>
            <?
        }
        ?>

        <div class="details-block-cart__totals">
            <div class="details-block-cart__title"><?= Loc::getMessage('SBB_TOTAL') ?></div>
            <div class="details-block-cart__summ" data-entity="basket-total-price">
                {{{PRICE_FORMATED}}}
            </div>
            {{#WEIGHT_FORMATED}}
                <div class="details-block-cart__weight">
                    <?= Loc::getMessage('SBB_WEIGHT_MSGVER_1', ['#WEIGHT_FORMATED#' => '{{{WEIGHT_FORMATED}}}']) ?>
                </div>
            {{/WEIGHT_FORMATED}}
            {{#SHOW_VAT}}
                <div class="details-block-cart__vat">
                    <?= Loc::getMessage('SBB_VAT_MSGVER_1', ['#VAT_SUM_FORMATED#' => '{{{VAT_SUM_FORMATED}}}']) ?>
                </div>
            {{/SHOW_VAT}}
        </div>

        <button type="button" class="btn btn_primary{{#DISABLE_CHECKOUT}} disabled{{/DISABLE_CHECKOUT}}" data-entity="basket-checkout-button">
            <?=Loc::getMessage('SBB_ORDER')?>
        </button>
	</div>
</script>
