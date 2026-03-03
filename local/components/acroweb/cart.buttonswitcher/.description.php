<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$arComponentDescription = [
    'NAME' => 'Переключатель кнопок корзины',
    'DESCRIPTION' => 'Автоматически меняет кнопки "Купить" на "В корзине" для товаров, уже добавленных в корзину',
    'ICON' => '/images/icon.gif',
    'SORT' => 10,
    'PATH' => [
        'ID' => 'acroweb',
        'NAME' => 'Acroweb',
        'CHILD' => [
            'ID' => 'cart',
            'NAME' => 'Корзина',
        ],
    ],
];

