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
<a href="/">
    <img src="<?= $logoPath ?>" alt="<?= htmlspecialcharsbx($name ?? '') ?>">
</a>