<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 */
?>
<script id="basket-total-template" type="text/html">
	<div class="details-block-cart__sticky" data-entity="basket-checkout-aligner">
        <div class="order-summary">
            <h2 class="order-summary__title"><?= Loc::getMessage('SBB_YOUR_BASKET') ?></h2>
            <div class="order-summary__inner">
                <div class="order-summary__row">
                    <div class="order-summary__info order-summary__row_prise">
                        <span class="order-summary__label">{{ITEMS_TEXT}}</span>
                        <span class="order-summary__value" data-entity="basket-total-price-without-discount">
                            {{{PRICE_WITHOUT_DISCOUNT_FORMATED}}}
                        </span>
                    </div>
                    {{#DISCOUNT_PRICE_FORMATED}}
                        <div class="order-summary__info order-summary__row_old-prise">
                            <span class="order-summary__label"><?= Loc::getMessage('SBB_DISCOUNT') ?></span>
                            <span class="order-summary__value order-summary__value_discount">
                                -{{{DISCOUNT_PRICE_FORMATED}}}
                            </span>
                        </div>
                    {{/DISCOUNT_PRICE_FORMATED}}
                </div>

                <div class="order-summary__total">
                    <span class="order-summary__total-label"><?= Loc::getMessage('SBB_TOTAL') ?></span>
                    <span class="order-summary__total-value" data-entity="basket-total-price">
                        {{{PRICE_FORMATED}}}
                    </span>
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

                <?
                if ($arParams['HIDE_COUPON'] !== 'Y')
                {
                    ?>
                    <div class="order-summary__promo basket-coupon-section">
                        <label class="order-summary__promo-wrapper basket-coupon-block-field">
                            <input
                                class="order-summary__promo-input field-input1"
                                type="text"
                                placeholder="<?= Loc::getMessage('SBB_COUPON_ENTER_MSGVER_1') ?>"
                                data-entity="basket-coupon-input"
                            >
                            <button class="order-summary__promo-btn" type="button">
                                <svg aria-hidden="true" width="20" height="20">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow2"></use>
                                </svg>
                            </button>
                            <span
                                class="basket-coupon-block-coupon-btn"
                                data-entity="basket-coupon-block-coupon-btn"
                                style="position: absolute; left: -9999px;"
                            ></span>
                        </label>
                    </div>

                    <div class="basket-coupon-alert-section">
                        <div class="basket-coupon-alert-inner">
                            {{#COUPON_LIST}}
                                <div class="basket-coupon-alert text-{{CLASS}}">
                                    <span class="basket-coupon-text">
                                        <strong>{{COUPON}}</strong> - <?= Loc::getMessage('SBB_COUPON') ?> {{JS_CHECK_CODE}}
                                        {{#DISCOUNT_NAME}}({{DISCOUNT_NAME}}){{/DISCOUNT_NAME}}
                                    </span>
                                    <span class="close-link" data-entity="basket-coupon-delete" data-coupon="{{COUPON}}">
                                        <?= Loc::getMessage('SBB_DELETE') ?>
                                    </span>
                                </div>
                            {{/COUPON_LIST}}
                        </div>
                    </div>
                    <?
                }
                ?>
            </div>

            <div class="order-summary__btns">
                <button
                    type="button"
                    class="btn btn_small btn_black btn_wide{{#DISABLE_CHECKOUT}} disabled{{/DISABLE_CHECKOUT}}"
                    data-entity="basket-checkout-button"
                >
                    <?= Loc::getMessage('SBB_ORDER') ?>
                </button>
            </div>
        </div>
	</div>
</script>
