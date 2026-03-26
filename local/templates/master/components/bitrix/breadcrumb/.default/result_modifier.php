<?php
declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @global CMain $APPLICATION
 *
 * @var array $arResult
 * @var array $arParams
 */

global $APPLICATION;

if (!is_array($arResult) || $arResult === []) {
    return;
}

$siteRoot = rtrim((string)SITE_DIR, '/');
$curDir = (string)$APPLICATION->GetCurDir();

$pathsToExclude = [];
if (strpos($curDir, $siteRoot . '/personal/order/make') === 0) {
    $pathsToExclude[] = ($siteRoot !== '' ? $siteRoot : '') . '/personal';
    $pathsToExclude[] = ($siteRoot !== '' ? $siteRoot : '') . '/personal/order';
} elseif (strpos($curDir, $siteRoot . '/personal/basket') === 0) {
    $pathsToExclude[] = ($siteRoot !== '' ? $siteRoot : '') . '/personal';
} else {
    return;
}

$normalizePath = static function (string $link): string {
    $path = parse_url($link, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        $path = $link;
    }

    return rtrim(str_replace('\\', '/', $path), '/') ?: '/';
};

$excludeSet = [];
foreach ($pathsToExclude as $path) {
    $excludeSet[$normalizePath($path)] = true;
}

$arResult = array_values(array_filter(
    $arResult,
    static function (array $item) use ($normalizePath, $excludeSet): bool {
        $link = isset($item['LINK']) ? (string)$item['LINK'] : '';
        if ($link === '') {
            return true;
        }

        return !isset($excludeSet[$normalizePath($link)]);
    }
));
