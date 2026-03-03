<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Settings\TemplateSettings;

$settings = TemplateSettings::getInstance();
$logo = $settings->getSettingValue('siteLogo');
$name = $settings->getSettingValue('siteName');

if (empty($logo)) {
    return;
}
?>
<a href="/">
    <img src="<?= $logo ?>" alt="<?= htmlspecialcharsbx($name ?? '') ?>">
</a>