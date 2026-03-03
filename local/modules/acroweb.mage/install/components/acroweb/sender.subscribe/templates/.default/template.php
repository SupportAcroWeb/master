<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>

<div class="sender-subscribe-form" id="sender-subscribe-form">
    <form action="javascript:void(0);" method="post">
        <div class="input-group1">
            <input aria-labelledby="a11y_footer_subscribe" class="input-group1__field field-input1"
                   type="email" name="email" placeholder="<?= Loc::getMessage('ACROWEB_SENDER_SUBSCRIBE_EMAIL_PLACEHOLDER') ?>" required>
            <button class="input-group1__btn btn btn_primary" type="submit"><?= Loc::getMessage('ACROWEB_SENDER_SUBSCRIBE_BUTTON') ?></button>
        </div>
    </form>
    <div class="sender-subscribe-message" style="display: none;"></div>
</div>