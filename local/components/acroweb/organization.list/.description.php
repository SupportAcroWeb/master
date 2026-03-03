<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    'NAME' => 'Список организаций',
    'DESCRIPTION' => 'Компонент для управления организациями пользователя',
    'ICON' => '/images/icon.gif',
    'SORT' => 10,
    'PATH' => [
        'ID' => 'acroweb',
        'NAME' => 'Acroweb',
        'CHILD' => [
            'ID' => 'organization',
            'NAME' => 'Организации',
        ],
    ],
];

