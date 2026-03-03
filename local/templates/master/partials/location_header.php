<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Settings\TemplateSettings;

$settings = TemplateSettings::getInstance();
$location = $settings->getSettingValue('shopAdr');

if (empty($location)) {
    return;
}

$locationDisplay = is_array($location) ? ($location[0] ?? '') : $location;
$locationDisplay = trim((string)$locationDisplay);
if ($locationDisplay === '') {
    return;
}
?>
<div class="header__cell-location">
    <button class="btn-location" type="button" title="<?= htmlspecialcharsbx($locationDisplay) ?>">
        <svg width="15" height="18" aria-hidden="true">
            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#placemark1"></use>
        </svg>
        <span class="btn-text btn-text_dotted"><?= htmlspecialcharsbx($locationDisplay) ?></span>
    </button>
</div>
