<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Settings\TemplateSettings;

$settings = TemplateSettings::getInstance();
$shopOfName = $settings->getSettingValue('shopOfName');
$shopUrAdr = $settings->getSettingValue('shopUrAdr');
$shopINN = $settings->getSettingValue('shopINN');
$shopKPP = $settings->getSettingValue('shopKPP');
$shopNS = $settings->getSettingValue('shopNS');
$shopBANK = $settings->getSettingValue('shopBANK');
$shopBIK = $settings->getSettingValue('shopBIK');
$shopKS = $settings->getSettingValue('shopKS');
?>

<div class="contact-details">
    <div class="container">
        <div class="company-details">
            <h2 class="company-details__title">Реквизиты</h2>
            <div class="company-details__list">
                <?php if (!empty($shopOfName)): ?>
                <div class="card-info1">
                    <p class="card-info1__name">Наименование</p>
                    <p class="card-info1__desc"><?= htmlspecialcharsbx($shopOfName) ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($shopUrAdr)): ?>
                <div class="card-info1">
                    <p class="card-info1__name">Юридический адрес</p>
                    <p class="card-info1__desc"><?= nl2br(htmlspecialcharsbx($shopUrAdr)) ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($shopKPP)): ?>
                <div class="card-info1">
                    <p class="card-info1__name">КПП</p>
                    <p class="card-info1__desc"><?= htmlspecialcharsbx($shopKPP) ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($shopINN)): ?>
                <div class="card-info1">
                    <p class="card-info1__name">ИНН</p>
                    <p class="card-info1__desc"><?= htmlspecialcharsbx($shopINN) ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($shopBANK)): ?>
                <div class="card-info1">
                    <p class="card-info1__name">Банк</p>
                    <p class="card-info1__desc"><?= htmlspecialcharsbx($shopBANK) ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($shopNS)): ?>
                <div class="card-info1">
                    <p class="card-info1__name">Расчетный счет</p>
                    <p class="card-info1__desc"><?= htmlspecialcharsbx($shopNS) ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($shopKS)): ?>
                <div class="card-info1">
                    <p class="card-info1__name">Корреспондентский счет</p>
                    <p class="card-info1__desc"><?= htmlspecialcharsbx($shopKS) ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($shopBIK)): ?>
                <div class="card-info1">
                    <p class="card-info1__name">БИК</p>
                    <p class="card-info1__desc"><?= htmlspecialcharsbx($shopBIK) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
