<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

if (empty($arResult)) {
    return;
}

if (!function_exists('renderMenuItem')) {
    function renderMenuItem($item, $isParent)
    {
        $hasSubClass = $isParent ? ' class="menu__has-sub"' : '';
        $html = "<li{$hasSubClass}>";
        $html .= "<a href='{$item['LINK']}'>{$item['TEXT']}</a>";

        if ($isParent) {
            $html .= '<svg class="menu__chevron" aria-hidden="true" width="10" height="6">';
            $html .= '<use xlink:href="' . SITE_TEMPLATE_PATH . '/img/sprite.svg#chevron1"></use>';
            $html .= '</svg>';
            $html .= '<div class="menu__sub">';
        }

        return $html;
    }
}

$menuTree = '<ul class="menu">';
$prevLevel = 0;

foreach ($arResult as $item) {
    if ($item['DEPTH_LEVEL'] < $prevLevel) {
        $menuTree .= str_repeat('</li></ul></div>', $prevLevel - $item['DEPTH_LEVEL']);
    } elseif ($item['DEPTH_LEVEL'] > $prevLevel) {
        // Добавляем новый <ul> только если это не первый уровень
        if ($prevLevel > 0) {
            $menuTree .= '<ul>';
        }
    } elseif ($item['DEPTH_LEVEL'] > 1) {
        $menuTree .= '</li>';
    }

    $menuTree .= renderMenuItem($item, $item['IS_PARENT']);

    $prevLevel = $item['DEPTH_LEVEL'];
}

// Закрываем все оставшиеся открытые теги
if ($prevLevel > 0) {
    $menuTree .= str_repeat('</li></ul></div>', $prevLevel - 1);
    $menuTree .= '</li>';
}
$menuTree .= '</ul>'; // Закрываем главный ul class="menu"

echo $menuTree;