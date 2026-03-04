<?php
declare(strict_types=1);

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */

$galleryValue = $arResult["PROPERTIES"]["GALLERY"]["VALUE"] ?? null;
$arResult["GALLERY_FILES"] = [];

if (!empty($galleryValue)) {
    $fileIds = is_array($galleryValue) ? $galleryValue : [$galleryValue];
    foreach ($fileIds as $fileId) {
        $file = CFile::GetFileArray((int)$fileId);
        if ($file) {
            $arResult["GALLERY_FILES"][] = $file;
        }
    }
}
