<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

global $APPLICATION;
?>

<div class="hystmodal" id="modalCallback" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window" role="dialog" aria-modal="true">
            <div class="hystmodal__inner">
                <h2 class="hystmodal__title">Заказать звонок</h2>
                <div class="hystmodal__description">Оставьте заявку и наш менеджер свяжется с Вами в ближайшее время!</div>
                <?
                $APPLICATION->IncludeComponent(
                    "acroweb:universal.form",
                    "contact_us",
                    [
                        "FORM_SID" => "acroweb_callback_s1",
                        "AJAX" => "Y",
                    ]
                );
                ?>
            </div>
            <button data-hystclose class="hystmodal__close">
                <svg aria-hidden="true" width="20" height="20">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#close1"></use>
                </svg>
                <span class="v-h">Закрыть</span>
            </button>
        </div>
    </div>
</div>
