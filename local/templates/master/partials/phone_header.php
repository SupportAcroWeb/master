<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Settings\TemplateSettings;

$settings = TemplateSettings::getInstance();
$phoneList = $settings->getSettingValue('siteTelephone');

if (empty($phoneList)) {
    return;
}

$firstPhone = $phoneList[0];
$remainingPhones = array_slice($phoneList, 1);
$hasMultiplePhones = !empty($remainingPhones);

$firstPhoneClean = preg_replace('/\D+/', '', $firstPhone);
$firstPhoneDisplay = htmlspecialcharsbx($firstPhone);
?>
<div class="phone-selector">
    <a class="contact-item" href="tel:<?= $firstPhoneClean ?>">
        <svg aria-hidden="true" width="18" height="18">
            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#phone1"></use>
        </svg>
        <span><?= $firstPhoneDisplay ?></span>
    </a>
    <?php if ($hasMultiplePhones): ?>
    <div class="phone-selector__dd">
        <?php foreach ($phoneList as $phone): ?>
            <?php
            $phoneClean = preg_replace('/\D+/', '', $phone);
            $phoneDisplay = htmlspecialcharsbx($phone);
            ?>
            <a class="contact-item" href="tel:<?= $phoneClean ?>">
                <svg aria-hidden="true" width="18" height="18">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#phone1"></use>
                </svg>
                <span><?= $phoneDisplay ?></span>
            </a>
        <?php endforeach; ?>
    </div>
    <button data-action="togglePhones" class="btn-chevron" type="button">
        <svg aria-hidden="true" width="10" height="6">
            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#chevron1"></use>
        </svg>
    </button>
    <?php endif; ?>
</div>

