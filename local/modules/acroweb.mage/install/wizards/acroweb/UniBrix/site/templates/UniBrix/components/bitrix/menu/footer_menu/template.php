<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!empty($arResult)):
    $previousLevel = 0; ?>
    <ul class="footer-nav">
        <?
        foreach ($arResult as $arItem):?>
            <li class="footer-nav__item">
                <a class="footer-nav__link" href="<?= $arItem["LINK"] ?>"><?= $arItem["TEXT"] ?></a>
            </li>
        <?
        endforeach ?>
    </ul>
<?
endif ?>
