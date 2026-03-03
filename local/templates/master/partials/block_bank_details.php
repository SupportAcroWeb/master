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

<div class="block-details block1">
    <div class="heading-cols1">
        <div class="container">
            <h2 class="title2">Реквизиты</h2>
        </div>
    </div>
    <div class="container container_bordered1">
        <div class="line"></div>
        <div class="block-details__body">
            <div class="block-details__column">
                <div class="block-details__name">Наименование</div>
                <div class="block-details__value">
                   <?= $shopOfName ?>
                </div>
            </div>
            <div class="block-details__column">
                <div class="block-details__name">Юридический адрес</div>
                <div class="block-details__value">
                    <?= $shopUrAdr ?>
                </div>
            </div>
            <div class="block-details__column">
                <div class="block-details__name">КПП</div>
                <div class="block-details__value">
                    <?= $shopKPP ?>
                </div>
            </div>
            <div class="block-details__column">
                <div class="block-details__name">ИНН</div>
                <div class="block-details__value">
                    <?= $shopINN ?>
                </div>
            </div>
            <div class="block-details__column">
                <div class="block-details__name">Банк</div>
                <div class="block-details__value">
                   <?= $shopBANK ?>
                </div>
            </div>
            <div class="block-details__column">
                <div class="block-details__name">Расчетный счет</div>
                <div class="block-details__value">
                    <?= $shopNS ?>
                </div>
            </div>
            <div class="block-details__column">
                <div class="block-details__name">Корреспондентский счет</div>
                <div class="block-details__value">
                    <?= $shopKS ?>
                </div>
            </div>
            <div class="block-details__column">
                <div class="block-details__name">БИК</div>
                <div class="block-details__value">
                    <?= $shopBIK ?>
                </div>
            </div>
        </div>
    </div>
</div>
