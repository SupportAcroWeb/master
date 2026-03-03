<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<div class="filter1" id="news-year-filter">
    <span class="btn <?= $arResult["CURRENT_YEAR"] == "all" ? "btn_primary btn_hollow btn_inactive" : "btn_grey" ?> btn_xs btn-filter" data-year="all">За все время</span>
    <?php foreach($arResult["YEARS"] as $year): ?>
        <a class="btn <?= $arResult["CURRENT_YEAR"] == $year ? "btn_primary btn_hollow btn_inactive" : "btn_grey" ?> btn_xs btn-filter" href="#" data-year="<?= $year ?>"><?= $year ?></a>
    <?php endforeach; ?>
</div>