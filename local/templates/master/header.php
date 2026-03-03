<?php

/**
 * Header template file
 *
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Acroweb\Mage\Helpers\TemplateHelper;

include __DIR__ . '/last-modified/init.php';
include __DIR__ . '/template_init.php';

Loc::loadLanguageFile(__FILE__);
?>
    <!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">
    <head>
        <title><?php $APPLICATION->ShowTitle() ?></title>
        <meta charset="utf-8">
        <?php $APPLICATION->ShowHead(); ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="<?= SITE_DIR ?>favicon-96x96.png" sizes="96x96"/>
        <link rel="icon" type="image/svg+xml" href="<?= SITE_DIR ?>favicon.svg"/>
        <link rel="shortcut icon" href="<?= SITE_DIR ?>favicon.ico"/>
        <link rel="apple-touch-icon" sizes="180x180" href="<?= SITE_DIR ?>apple-touch-icon.png"/>
        <meta name="apple-mobile-web-app-title" content="naanit.ru"/>
        <link rel="manifest" href="<?= SITE_DIR ?>site.webmanifest"/>
    </head>
<?php
$APPLICATION->ShowPanel();
?>
<body class="preload<?php TemplateHelper::showDivClass('bodyClass'); ?>">
<div class="main-wrapper-outer">
    <header class="header">
        <div class="container">
            <div class="header__grid1">
                <div class="header__cell-burger">
                    <button data-action="showMenuMob" class="header__burger" type="button">
                        <svg width="30" height="18" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#burger2"></use>
                        </svg>
                    </button>
                </div>
                <div class="header__cell-search-mob">
                    <button class="btn-circle" type="button" data-action="focusSearch">
                        <span class="v-h">Поиск</span>
                        <svg width="16" height="16" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#search2"></use>
                        </svg>
                    </button>
                </div>
                <div class="header__cell-logo">
                    <?php TemplateHelper::includePartial('logo_header'); ?>
                </div>
                <?php TemplateHelper::includePartial('location_header'); ?>
                <nav class="header__cell-menu">
                    <?php $APPLICATION->IncludeComponent(
                            'bitrix:menu',
                            'head_menu',
                            [
                                    'ALLOW_MULTI_SELECT' => 'N',
                                    'CHILD_MENU_TYPE' => 'podmenu',
                                    'DELAY' => 'N',
                                    'MAX_LEVEL' => '3',
                                    'MENU_CACHE_GET_VARS' => [],
                                    'MENU_CACHE_TIME' => '3600',
                                    'MENU_CACHE_TYPE' => 'N',
                                    'MENU_CACHE_USE_GROUPS' => 'Y',
                                    'ROOT_MENU_TYPE' => 'top',
                                    'USE_EXT' => 'N',
                                    'COMPONENT_TEMPLATE' => 'head_menu',
                            ],
                            false
                    ); ?>
                </nav>
                <div class="header__cell-phone">
                    <?php TemplateHelper::includePartial('phone_header'); ?>
                </div>
                <div class="header__cell-callback">
                    <button data-hystmodal="#modalCallback" class="btn-text btn-text_primary btn-text_dotted"
                            type="button">Заказать звонок
                    </button>
                </div>
                <div class="header__cell-catalog">
                    <button data-action="toggleCatalogMenu" class="btn-catalog btn btn_black" type="button">
                            <span class="btn-catalog__lines">
                                <span class="btn-catalog__line"></span>
                                <span class="btn-catalog__line"></span>
                            </span>
                        <span>Продукция</span>
                    </button>
                </div>
                <div class="header__cell-search">
                    <?php $APPLICATION->IncludeComponent(
                            'acroweb:catalog.smartsearch',
                            '.default',
                            [
                                    'IBLOCK_ID' => '3',
                                    'ITEMS_LIMIT' => '4',
                                    'SECTIONS_LIMIT' => '4',
                                    'PRICE_CODE' => 'BASE',
                                    'SHOW_SECTIONS' => 'Y',
                                    'SHOW_ITEMS' => 'Y',
                                    'CACHE_TYPE' => 'A',
                                    'CACHE_TIME' => '30',
                                    'COMPONENT_TEMPLATE' => '.default',
                                    'SEARCH_BY_ARTICLE' => 'N',
                                    'LABEL_PROP' => ['NEWPRODUCT', 'SALELEADER', 'SPECIALOFFER'],
                            ],
                            false
                    ); ?>
                </div>
                <div class="header__cell-toolbar">
                    <?php $APPLICATION->IncludeComponent(
                            'bitrix:sale.basket.basket.line',
                            '.default',
                            [
                                    'PATH_TO_BASKET' => '/personal/basket/',
                                    'PATH_TO_ORDER' => '/personal/order/',
                            ],
                            false
                    ); ?>
                    <?php if ($USER->IsAuthorized()): ?>
                        <ul class="header__btn-user-wrap menu">
                            <li class="btn-circle header__btn-user active" type="button">
                                <span class="v-h">Личный кабинет</span>
                                <svg class="desk" width="19" height="25" aria-hidden="true">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#user1"></use>
                                </svg>
                                <svg class="mob" width="11" height="14" aria-hidden="true">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#user2"></use>
                                </svg>
                                <div class="menu-main__sub menu__sub">
                                    <ul>
                                        <?php $APPLICATION->IncludeComponent(
                                                'bitrix:menu',
                                                'light_menu',
                                                [
                                                        'ALLOW_MULTI_SELECT' => 'N',
                                                        'CHILD_MENU_TYPE' => 'personal',
                                                        'DELAY' => 'N',
                                                        'MAX_LEVEL' => '1',
                                                        'MENU_CACHE_GET_VARS' => [],
                                                        'MENU_CACHE_TIME' => '3600',
                                                        'MENU_CACHE_TYPE' => 'N',
                                                        'MENU_CACHE_USE_GROUPS' => 'Y',
                                                        'ROOT_MENU_TYPE' => 'personal',
                                                        'USE_EXT' => 'N',
                                                        'COMPONENT_TEMPLATE' => 'light_menu',
                                                ],
                                                false
                                        ); ?>
                                        <li>
                                            <a href="<?= $APPLICATION->GetCurPageParam('logout=yes&' . bitrix_sessid_get(), ['logout']) ?>"
                                               class="exit">Выход</a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    <?php else: ?>
                        <a class="btn-circle" href="/auth/">
                            <span class="v-h">Личный кабинет</span>
                            <svg class="desk" width="19" height="25" aria-hidden="true">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#user1"></use>
                            </svg>
                            <svg class="mob" width="11" height="14" aria-hidden="true">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#user2"></use>
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="menu-catalog">
            <div class="menu-catalog__inner2">
                <div class="menu-catalog__title">Навигация</div>
                <div class="menu-catalog__catalog">
                    <a href="/produktsiya/" class="menu-catalog__btn">
                        <svg width="23" height="10" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#burger3"></use>
                        </svg>
                        <span>Продукция</span>
                        <svg width="10" height="6" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#chevron1"></use>
                        </svg>
                    </a>
                    <div class="container">
                        <div class="menu-catalog__grid">
                            <?php $APPLICATION->IncludeComponent(
                                    'bitrix:menu',
                                    'vertical_multilevel',
                                    [
                                            'ROOT_MENU_TYPE' => 'topcatalog',
                                            'MAX_LEVEL' => '3',
                                            'CHILD_MENU_TYPE' => 'topcatalog',
                                            'USE_EXT' => 'Y',
                                            'ALLOW_MULTI_SELECT' => 'Y',
                                    ],
                                    false
                            ); ?>
                        </div>
                    </div>
                </div>
                <div class="menu-catalog__menu">
                    <?php $APPLICATION->IncludeComponent(
                            'bitrix:menu',
                            'head_menu',
                            [
                                    'ALLOW_MULTI_SELECT' => 'N',
                                    'CHILD_MENU_TYPE' => 'podmenu',
                                    'DELAY' => 'N',
                                    'MAX_LEVEL' => '3',
                                    'MENU_CACHE_GET_VARS' => [],
                                    'MENU_CACHE_TIME' => '3600',
                                    'MENU_CACHE_TYPE' => 'N',
                                    'MENU_CACHE_USE_GROUPS' => 'Y',
                                    'ROOT_MENU_TYPE' => 'top',
                                    'USE_EXT' => 'N',
                                    'COMPONENT_TEMPLATE' => 'head_menu_mob',
                            ],
                            false
                    ); ?>
                </div>
                <div class="menu-catalog__bottom">
                    <div class="menu-catalog__inner1 menu-catalog__contacts">
                        <?php TemplateHelper::includePartial('phone_header_list'); ?>
                        <button data-hystmodal="#modalCallback" class="btn-text btn-text_primary btn-text_dotted"
                                type="button">Заказать звонок
                        </button>
                    </div>
                    <div class="menu-catalog__inner1">
                        <div class="menu-catalog__user">
                            <?php if ($USER->IsAuthorized()): ?>
                                <a href="/personal/" class="btn-circle">
                                    <span class="v-h">Личный кабинет</span>
                                    <svg width="11" height="14" aria-hidden="true">
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#user2"></use>
                                    </svg>
                                </a>
                                <div>
                                    <div class="menu-catalog__label1"><?= htmlspecialcharsbx($USER->GetFirstName() . ' ' . $USER->GetLastName()) ?></div>
                                    <a class="menu-catalog__logout"
                                       href="<?= $APPLICATION->GetCurPageParam('logout=yes&' . bitrix_sessid_get(), ['logout']) ?>">
                                        <svg width="13" height="13" aria-hidden="true">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#logout1"></use>
                                        </svg>
                                        <span>Выйти</span>
                                    </a>
                                </div>
                            <?php else: ?>
                                <a href="/auth/" class="btn-circle">
                                    <span class="v-h">Личный кабинет</span>
                                    <svg width="11" height="14" aria-hidden="true">
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#user2"></use>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <button data-action="hideMenuMob" class="menu-catalog__close" type="button">
                <svg width="18" height="18" aria-hidden="true">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#cross1"></use>
                </svg>
            </button>
        </div>
    </header>
    <main class="main<?php TemplateHelper::showDivClass('mainClass'); ?>">
<? TemplateHelper::includeLayout('header');