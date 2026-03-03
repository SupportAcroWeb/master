<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$arComponentDescription = [
    'NAME' => 'Быстрый поиск по каталогу',
    'DESCRIPTION' => 'Компонент живого поиска с выпадающими подсказками товаров и разделов',
    'ICON' => '/images/icon.gif',
    'SORT' => 10,
    'PATH' => [
        'ID' => 'acroweb',
        'NAME' => 'Acroweb',
        'CHILD' => [
            'ID' => 'catalog',
            'NAME' => 'Каталог',
            'SORT' => 30
        ]
    ]
];

