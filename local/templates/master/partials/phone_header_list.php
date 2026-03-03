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

foreach ($phoneList as $phone):
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
