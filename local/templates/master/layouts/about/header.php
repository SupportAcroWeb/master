<?php

global $APPLICATION;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Helpers\TemplateHelper;

?>
<div class="block-intro-about block1">
    <div class="block-intro-about__photo">
        <div class="block-intro-about__photo-inner">
            <img src="<?= SITE_TEMPLATE_PATH ?>/img/about.webp" alt="" class="">
        </div>
    </div>
    <div class="container">
        <div class="block-intro-about__content">
            <div class="block-breadcrumbs">
                <?
                TemplateHelper::includePartial('breadcrumbs'); ?>
            </div>
            <div class="block-intro-about__titles">
                <h1 class="block-intro-about__title">
                    <? $APPLICATION->IncludeFile(SITE_DIR . 'include/about/title.php') ?>
                </h1>
                <div class="block-intro-about__text">
                    <? $APPLICATION->IncludeFile(SITE_DIR . 'include/about/description.php') ?>
                </div>
            </div>
        </div>
    </div>
    <div class="container container_bordered1">
        <div class="block-intro-about__advantage">
            <div class="card-advantage">
                <? $APPLICATION->IncludeFile(SITE_DIR . 'include/about/info_src_img_1.php') ?>
                <div class="card-advantage__title"><? $APPLICATION->IncludeFile(SITE_DIR . 'include/about/info_title_img_1.php') ?></div>
            </div>
            <div class="card-advantage">
                <? $APPLICATION->IncludeFile(SITE_DIR . 'include/about/info_src_img_2.php') ?>
                <div class="card-advantage__title"><? $APPLICATION->IncludeFile(SITE_DIR . 'include/about/info_title_img_2.php') ?></div>
            </div>
            <div class="card-advantage">
                <? $APPLICATION->IncludeFile(SITE_DIR . 'include/about/info_src_img_3.php') ?>
                <div class="card-advantage__title"><? $APPLICATION->IncludeFile(SITE_DIR . 'include/about/info_title_img_3.php') ?></div>
            </div>
            <div class="card-advantage">
                <? $APPLICATION->IncludeFile(SITE_DIR . 'include/about/info_src_img_4.php') ?>
                <div class="card-advantage__title"><? $APPLICATION->IncludeFile(SITE_DIR . 'include/about/info_title_img_4.php') ?></div>
            </div>
        </div>
    </div>
</div>
