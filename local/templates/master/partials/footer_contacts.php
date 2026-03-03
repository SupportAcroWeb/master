<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Settings\TemplateSettings;

$settings = TemplateSettings::getInstance(); 
$location = $settings->getSettingValue('shopAdr');
$phoneList = $settings->getSettingValue('siteTelephone');
$emailList = $settings->getSettingValue('siteEmail'); 
?>

<a href="/kontakty/" class="footer__location">
    <svg width="18" height="21" aria-hidden="true">
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#placemark1"></use>
    </svg>
    <?= $location ?>
</a>
<? if (!empty($phoneList)): ?>
    <?php foreach ($phoneList as $phone): ?>
        <p class="footer__phone">
            <a href="tel:<?= preg_replace('/\D+/', '', $phone) ?>"><?= $phone ?></a>
        </p>
    <?php endforeach; ?>
<? endif; ?>
<? if (!empty($emailList)): ?>
    <?php foreach ($emailList as $email): ?>
        <p class="footer__phone">
            <a href="mailto:<?= preg_replace('/[^\w\d@.-]/', '', $email) ?>"><?= $email ?></a>
        </p>
    <?php endforeach; ?>
<? endif; ?>        