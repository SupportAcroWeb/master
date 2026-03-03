<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Settings\TemplateSettings;

$settings = TemplateSettings::getInstance();
$shopAdr = $settings->getSettingValue('shopAdr');
$phoneList = $settings->getSettingValue('siteTelephone');
$emailList = $settings->getSettingValue('siteEmail');
$socialVk = trim((string)($settings->getSettingValue('socialVk') ?? ''));
$socialTelegram = trim((string)($settings->getSettingValue('socialTelegram') ?? ''));

$shopAdrDisplay = is_array($shopAdr) ? ($shopAdr[0] ?? '') : (string)$shopAdr;
?>
<?php if (!empty($shopAdrDisplay)): ?>
<p class="footer__title">Адрес</p>
<div class="footer__location">
    <svg aria-hidden="true" width="15" height="18">
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#placemark1"></use>
    </svg>
    <a href="/kontakty/"><?= nl2br(htmlspecialcharsbx($shopAdrDisplay)) ?></a>
</div>
<?php endif; ?>
<?php if (!empty($phoneList) || !empty($emailList)): ?>
<p class="footer__title">Контактные данные</p>
<div class="footer__contacts">
    <?php if (!empty($phoneList)): ?>
        <?php foreach ($phoneList as $phone): ?>
            <?php $phoneClean = preg_replace('/\D+/', '', $phone); ?>
    <a class="contact-item" href="tel:<?= $phoneClean ?>">
        <svg aria-hidden="true" width="18" height="18">
            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#phone1"></use>
        </svg>
        <span><?= htmlspecialcharsbx($phone) ?></span>
    </a>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (!empty($emailList)): ?>
        <?php foreach ($emailList as $email): ?>
            <?php $emailClean = preg_replace('/[^\w\d@.-]/', '', $email); ?>
    <a class="contact-item" href="mailto:<?= $emailClean ?>">
        <svg aria-hidden="true" width="18" height="14">
            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mail1"></use>
        </svg>
        <span><?= htmlspecialcharsbx($email) ?></span>
    </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php if ($socialVk !== '' || $socialTelegram !== ''): ?>
<div class="footer__socials">
    <?php if ($socialVk !== ''): ?>
    <a class="social-item" href="<?= htmlspecialcharsbx($socialVk) ?>" target="_blank" rel="noopener noreferrer">
        <span class="v-h">Вконтакте</span>
        <svg aria-hidden="true" width="22" height="12">
            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#vk1"></use>
        </svg>
    </a>
    <?php endif; ?>
    <?php if ($socialTelegram !== ''): ?>
    <a class="social-item" href="<?= htmlspecialcharsbx($socialTelegram) ?>" target="_blank" rel="noopener noreferrer">
        <span class="v-h">Телеграм</span>
        <svg aria-hidden="true" width="18" height="15">
            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#telegram1"></use>
        </svg>
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>
