<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Settings\TemplateSettings;

$settings = TemplateSettings::getInstance();
$logo = $settings->getSettingValue('siteLogoRetina');
$name = $settings->getSettingValue('siteNameFooter');

if (empty($logo) || empty($name)) {
    return;
}

$fileId = null;
if (is_numeric($logo)) {
    $fileId = (int)$logo;
} elseif (is_array($logo)) {
    $fileId = (int)($logo['ID'] ?? $logo['id'] ?? $logo[0] ?? 0);
}
$logoPath = $fileId > 0 ? CFile::GetPath($fileId) : (is_string($logo) ? $logo : '');

if (empty($logoPath)) {
    return;
}
?>
<a class="footer__logo" href="/">
    <img src="<?= $logoPath ?>" alt="">
</a>