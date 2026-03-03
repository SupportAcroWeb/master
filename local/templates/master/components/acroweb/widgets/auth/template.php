<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Шаблон виджета авторизации
 *
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

global $APPLICATION;

switch ($arResult['MODE']) {
    case 'change_password':
        $APPLICATION->IncludeComponent(
            'bitrix:system.auth.changepasswd',
            '.default',
            $arResult['AUTH_PARAMS']
        );
        break;

    case 'forgot_password':
        $APPLICATION->IncludeComponent(
            'bitrix:system.auth.forgotpasswd',
            '.default',
            $arResult['AUTH_PARAMS']
        );
        break;

    case 'register':
        $APPLICATION->IncludeComponent(
            'bitrix:main.register',
            'main',
            array_merge(
                $arResult['REGISTER_PARAMS'],
                ['COMPONENT_TEMPLATE' => 'main']
            ),
            false
        );
        break;

    case 'confirm_registration':
        $APPLICATION->IncludeComponent(
            'bitrix:system.auth.confirmation',
            '.default',
            [
                'USER_ID' => 'confirm_user_id',
                'CONFIRM_CODE' => 'confirm_code',
                'LOGIN' => 'login',
            ]
        );
        break;

    case 'confirm_request':
        ?>
        <div class="container">
            <div class="block-login__top">
                <h1 class="title2">Регистрация организации</h1>
            </div>
        </div>

        <div class="container container_bordered1">
            <div class="form-grid1">
                <div class="form-grid1__row" style="display: flex; justify-content: center; align-content: center;">
                        <?php
                        $APPLICATION->IncludeComponent(
                            'bitrix:main.include',
                            '',
                            [
                                'AREA_FILE_SHOW' => 'file',
                                'PATH' => '/include/confirm_request.php',
                            ],
                            false,
                            ['HIDE_ICONS' => 'N']
                        );
                        ?>
                </div>
            </div>

        </div>
        <?php
        break;

    case 'login':
    default:
        $APPLICATION->IncludeComponent(
            'bitrix:system.auth.authorize',
            '.default',
            $arResult['AUTH_PARAMS']
        );
        break;
}

