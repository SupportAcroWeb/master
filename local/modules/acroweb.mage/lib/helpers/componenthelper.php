<?php

namespace Acroweb\Mage\Helpers;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use CIBlockElement;
use Bitrix\Iblock\PropertyTable;

Loader::includeModule("highloadblock");
Loader::includeModule('iblock');
Loc::loadMessages(__FILE__);

class ComponentHelper
{
    /**
     * @param array $arKey
     * @param array $arData
     * @return bool
     */
    public static function validArray(array $arKey, array $arData): bool
    {
        foreach ($arKey as $key) {
            if (!array_key_exists($key, $arData)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int|string $iblockId
     * @param string $propertyCode
     * @return false|int
     */
    public static function getPropertyIdByCode(int|string $iblockId, string $propertyCode): false|int
    {
        $property = PropertyTable::getList([
            'filter' => [
                'IBLOCK_ID' => $iblockId,
                'CODE' => $propertyCode,
            ],
            'select' => ['ID'],
        ])->fetch();

        return $property ? (int)$property['ID'] : false;
    }

    /**
     * @param array $array
     * @return array
     */
    public static function sortArrayByValues(array $array): array
    {
        uasort($array, function ($a, $b) {
            return $a <=> $b;
        });

        return $array;
    }

    /**
     * @param array $incoming
     * @return array|false
     */
    public static function formatArrayFile(array $incoming): array|false
    {
        $result = [];
        foreach ($incoming as $key => $item) {
            foreach ($incoming[$key]["name"] as $keyFile => $file) {
                if (!$incoming[$key]["name"][$keyFile]) {
                    return false;
                }
                $result[$key][$keyFile]["name"] = $file;
                $result[$key][$keyFile]["type"] = $incoming[$key]["type"][$keyFile];
                $result[$key][$keyFile]["error"] = $incoming[$key]["error"][$keyFile];
                $result[$key][$keyFile]["tmp_name"] = $incoming[$key]["tmp_name"][$keyFile];
                $result[$key][$keyFile]["size"] = $incoming[$key]["size"][$keyFile];
            }
        }

        return $result;
    }

    public static function idPropList($xmlId, $propCode, $iblock = false)
    {
        $filter = ["XML_ID" => $xmlId, "CODE" => $propCode];

        if ($iblock) {
            $filter["IBLOCK_ID"] = $iblock;
        }

        $UserField = \CIBlockPropertyEnum::GetList([], $filter);

        if ($UserFieldAr = $UserField->GetNext()) {
            return $UserFieldAr["ID"];
        } else {
            return false;
        }
    }

    public static function idPropList4Field($field, $value, $propCode, $iblock = false)
    {
        $filter = [$field => $value, "CODE" => $propCode];

        if ($iblock) {
            $filter["IBLOCK_ID"] = $iblock;
        }

        $UserField = \CIBlockPropertyEnum::GetList([], $filter);

        if ($UserFieldAr = $UserField->GetNext()) {
            return $UserFieldAr["ID"];
        } else {
            return false;
        }
    }

    public static function customMultiSort($array, $field)
    {
        $sortArr = [];
        foreach ($array as $key => $val) {
            $sortArr[$key] = $val[$field];
        }

        array_multisort($sortArr, $array);

        return $array;
    }

    /**
     * @param string|int $iblockId
     * @param string|int $elementId
     * @param array $arFields
     * @param array $arPropCode
     * @return array|false
     */
    public static function getInfoElement4Id(
        string|int $iblockId,
        string|int $elementId,
        array $arFields = [],
        array $arPropCode = []
    ): array|false {
        $arFields[] = "ID";

        $result = CIBlockElement::GetList([],
            ["IBLOCK_ID" => $iblockId, "ID" => $elementId],
            false,
            false,
            $arFields)->fetch();
        if (!$result["ID"]) {
            return false;
        }

        $tmpProp = [];
        CIBlockElement::GetPropertyValuesArray(
            $tmpProp,
            $iblockId,
            [
                'ID' => $elementId,
                'IBLOCK_ID' => $iblockId,
            ],
            array_merge(
                [
                    'ACTIVE' => 'Y',
                    'IBLOCK_ID' => $iblockId,
                ],
                !empty($arPropCode) ? ['CODE' => $arPropCode] : []
            )
        );
        $result["PROPERTIES"] = $tmpProp[$elementId];

        return $result;
    }

    /**
     * @param string|int $iblockId
     * @param string|int $competitionsId
     * @param string $arPropCode
     * @return array|string|int|bool
     */
    public static function getPropertyValueElement4Code(
        string|int $iblockId,
        string|int $elementId,
        string $arPropCode
    ): array|string|int|bool|null {
        return self::getInfoElement4Id($iblockId, $elementId, [], [$arPropCode])['PROPERTIES'][$arPropCode]['VALUE'];
    }

    /**
     * @param $property
     * @param $default
     * @return void
     */
    public static function getPageProperty($property = '', $default = ''): void
    {
        global $APPLICATION;
        $APPLICATION->AddBufferContent(function () use ($APPLICATION, $property, $default) {
            if (empty($property)) {
                return '';
            }
            return $APPLICATION->GetPageProperty($property, $default);
        });
    }

    /**
     * @param string $phone
     * @return string
     */
    public static function formatPhone(string $phone): string
    {
        $result = str_replace(')', '', str_replace('(', '', trim(str_replace('-', '', $phone))));
        $result = str_replace('+', '', str_replace(' ', '', $result));
        $result = mb_substr($result, 1);

        return '+7' . $result;
    }

    /**
     * @param string|int $formId
     * @param string $fieldCode
     * @return array
     */
    public static function getFilesInputNames(string|int $formId, string $fieldCode = 'FILES'): array
    {
        $res = [];
        if ($question = \CFormField::GetBySID($fieldCode, $formId)->Fetch()) {
            $by = 's_id';
            $order = 'asc';
            $filter = false;
            if (intval($question['ID'])) {
                $rsAnswers = \CFormAnswer::GetList($question['ID'], $by, $order, ["FIELD_TYPE" => 'file'], $filter);
                while ($arAnswer = $rsAnswers->Fetch()) {
                    $res[] = 'form_file_' . $arAnswer['ID'];
                }
            }
        }
        return $res;
    }

    /**
     * @param string|int $WEB_FORM_ID
     * @param array $arFields
     * @param array $arrVALUES
     * @return true
     */
    public static function loadMultiple(string|int $WEB_FORM_ID, array &$arFields, array &$arrVALUES): bool
    {
        global $_FILES;
        if ($_FILES['files']) {
            $files = [];
            if (is_array($_FILES['files']['name'])) {
                foreach ($_FILES['files'] as $key => $val) {
                    foreach ($val as $k => $v) {
                        $files[$k][$key] = $v;
                    }
                }
            } else {
                $files = [$_FILES['files']];
            }
            unset($_FILES['files']);

            $err = [];
            if ($inputsName = self::getFilesInputNames($WEB_FORM_ID)) {
                foreach ($files as $f) {
                    if ($inputName = array_shift($inputsName)) {
                        $_FILES[$inputName] = $f;
                    } else {
                        $err[] = $f;
                    }
                }

                if ($err) {
                    $firelds = [
                        'TITLE' => Loc::getMessage('NO_FIELD_FOR_INCLUDE_FILE') . __FUNCTION__,
                        'MESSAGE' => print_r($err, true),
                    ];
                    \CEvent::Send('DEBUG_SEND', SITE_ID, $firelds);
                }
            }
        }
        return true;
    }

    /**
     * @param string|int $WEB_FORM_ID
     * @param array $arFields
     * @param array $arrVALUES
     * @return void
     */
    public static function webFormMultiple(string|int $WEB_FORM_ID, array &$arFields, array &$arrVALUES): void
    {
        self::loadMultiple($WEB_FORM_ID, $arFields, $arrVALUES);
    }

}