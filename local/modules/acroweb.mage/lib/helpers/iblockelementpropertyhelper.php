<?php

namespace Acroweb\Mage\Helpers;

use Bitrix\Iblock;
use Bitrix\Iblock\PropertyTable;
use CIBlockElement;

class IblockElementPropertyHelper
{
    public static function prepareFileFields(&$arItems, $arPropCodes = []): void
    {
        $arCheck = !empty($arPropCodes) ? array_fill_keys($arPropCodes, 1) : [];
        $bCheck = !empty($arPropCodes);

        $arFileIds = $arFiles = [];

        foreach ($arItems as &$arItem) {
            if (empty($arItem['PROPERTIES'])) {
                continue;
            }

            foreach ($arItem['PROPERTIES'] as $prpCode => &$arProperty) {
                if (
                    ($bCheck && !array_key_exists($prpCode, $arCheck))
                    || $arProperty['PROPERTY_TYPE'] != PropertyTable::TYPE_FILE
                    || empty($arProperty['VALUE'])
                ) {
                    continue;
                }

                if ($arProperty['MULTIPLE'] == 'N') {
                    $arProperty['VALUE'] = [$arProperty['VALUE']];
                }

                $arFileIds = array_merge(
                    $arFileIds,
                    is_array($arProperty['VALUE']) ? $arProperty['VALUE'] : []
                );
            }
        }

        if (!empty($arFileIds)) {
            $arFiles = FileHelper::getFilesByIds($arFileIds);
        }

        if (empty($arFiles)) {
            return;
        }

        foreach ($arItems as &$arItem) {
            if (empty($arItem['PROPERTIES'])) {
                continue;
            }

            foreach ($arItem['PROPERTIES'] as $prpCode => &$arProperty) {
                if (
                    ($bCheck && !array_key_exists($prpCode, $arCheck))
                    || $arProperty['PROPERTY_TYPE'] != PropertyTable::TYPE_FILE
                    || empty($arProperty['VALUE'])
                ) {
                    continue;
                }

                $arProperty['FILE_VALUE'] = [];

                foreach ($arProperty['VALUE'] as $v) {
                    $fileId = (int)$v;
                    if (array_key_exists($fileId, $arFiles)) {
                        $arProperty['FILE_VALUE'][$fileId] = $arFiles[$fileId];
                    }
                }
            }
        }

        unset($arItem, $arProperty);
    }

    public static function getListProperties(&$arItems = [], $arCodes = null): void
    {
        if (empty($arItems)) {
            return;
        }

        $usePropertyFeatures = Iblock\Model\PropertyFeature::isEnabledFeatures();
        $bAll = is_array($arCodes);

        $arTmpItems = [];

        foreach ($arItems as &$arItem) {
            if (empty($arItem['IBLOCK_ID'])) {
                continue;
            }

            $arItem['PROPERTIES'] = [];
            unset($arItem['DISPLAY_PROPERTIES']);
            $arTmpItems[$arItem['IBLOCK_ID']][$arItem['ID']] = &$arItem;
        }

        if (isset($arItem)) {
            unset($arItem);
        }

        foreach ($arTmpItems as $iblockId => &$arIblockItems) {
            if (is_null($arCodes) && $usePropertyFeatures) {
                $arCodes = Iblock\Model\PropertyFeature::getListPageShowPropertyCodes(
                    $iblockId,
                    ['CODE' => 'Y']
                );
            }

            if (!is_array($arCodes)) {
                $arCodes = [];
            }

            if (empty($arCodes) && !$bAll) {
                continue;
            }

            CIBlockElement::GetPropertyValuesArray(
                $arIblockItems,
                $iblockId,
                [
                    'ID' => array_keys($arIblockItems),
                    'IBLOCK_ID' => $iblockId,
                ],
                array_merge(
                    [
                        'ACTIVE' => 'Y',
                        'IBLOCK_ID' => $iblockId,
                    ],
                    !empty($arCodes) ? ['CODE' => $arCodes] : []
                )
            );
        }
    }
}