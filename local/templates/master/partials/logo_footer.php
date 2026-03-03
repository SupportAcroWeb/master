<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Settings\TemplateSettings;

$settings = TemplateSettings::getInstance();
$logo = $settings->getSettingValue('siteLogoRetina');
$name = $settings->getSettingValue('siteNameFooter');

if (empty($logo) || empty($name)) {
    return;
}

?>
<a class="footer__logo" href="/">
    <img src="<?= $logo ?>" alt=""> 
</a> 
<div class="footer__name"><?= $name ?></div>