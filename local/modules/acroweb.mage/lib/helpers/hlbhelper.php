<?php

namespace Acroweb\Mage\Helpers;


use Bitrix\Main;
use Acroweb\Mage\CacheTrait;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\SystemException;
use CUserFieldEnum;
use Throwable;

class HLBHelper
{
    protected const ENTITY_ID_PREFIX = 'HLBLOCK_';

    use CacheTrait;

    public static function getHLBlockEntity($hlb)
    {
        try {
            if (!Main\Loader::includeModule('highloadblock')) {
                return false;
            }

            $cacheKey = ['HLBLOCK_ENTITY', $hlb];

            if ($entity = self::getCache($cacheKey)) {
                return $entity;
            }

            $hlBlock = self::resolve($hlb);

            if (!$hlBlock) {
                return false;
            }

            $entity = HL\HighloadBlockTable::compileEntity($hlBlock);

            self::setCache($cacheKey, $entity);

            return $entity;
        } catch (Throwable) {
        }

        return false;
    }

    public static function getHLBDataClass($hlb): DataManager|string|bool
    {
        try {
            if ($entity = self::getHLBlockEntity($hlb)) {
                return $entity->getDataClass();
            }
        } catch (Throwable) {
        }

        return false;
    }

    public static function getHLBlockIdByEntityId($entityId): bool|string
    {
        if (preg_match('#^' . self::ENTITY_ID_PREFIX . '(\d+)$#', $entityId, $r)) {
            return $r[1];
        }

        return false;
    }

    public static function getEnumIdByValue($field, $value, $bAdd = false)
    {
        if (!($hlbId = self::getHLBlockIdByEntityId($field['ENTITY_ID']))) {
            return false;
        }

        $value  = trim($value);
        $search = mb_strtoupper($value);
        $result = null;

        if (strlen($value) <= 0) {
            return false;
        }

        $cacheKey = ['HLBLOCK', $hlbId, 'ENUM_BY_FIELD'];

        if ($enum = self::getCache(array_merge($cacheKey, [$field['ID'], 'BY_VALUE', $search]))) {
            return $enum;
        }

        $arFilter = ['USER_FIELD_ID' => $field['ID'], 'VALUE' => $value];

        $obEnum = new CUserFieldEnum();
        $dbEnum = $obEnum->GetList([], $arFilter);

        while ($arEnum = $dbEnum->Fetch()) {
            self::setCache(array_merge($cacheKey, [$field['ID'], 'ITEMS', $arEnum['ID']]), $arEnum);
            self::setCache(array_merge($cacheKey, [$field['ID'], 'BY_VALUE', mb_strtoupper($arEnum['VALUE'])]), $arEnum['ID']);
            if ($search == mb_strtoupper($arEnum['VALUE'])) {
                $result = $arEnum['ID'];
            }
        }

        if (!$result && $bAdd) {
            $obEnum->SetEnumValues($field['ID'], ['n0' => ['VALUE' => $value]]);
            $result = self::getEnumIdByValue($field, $value, false);
        }

        return is_null($result) ? false : $result;
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getFields($hlb, $lang = null)
    {
        if (is_null($lang)) {
            $lang = LANGUAGE_ID;
        }

        if (!($hlBlock = self::resolve($hlb)))
            return false;

        $hlbId= $hlBlock['ID'];
        $userTypeManager = Main\Application::getUserTypeManager();

        $entityFields = $userTypeManager->GetUserFields(self::ENTITY_ID_PREFIX . $hlbId, 0, $lang);

        foreach ($entityFields as &$entityField) {
            $entityField['IS_REQUIRED'] = $entityField['MANDATORY'];
            unset($entityField['MANDATORY']);
            $entityField['NAME'] = $entityField['EDIT_FORM_LABEL'];

            if ($entityField['USER_TYPE_ID'] == 'enumeration') {
                $arFilter = ['USER_FIELD_ID' => $entityField['ID']];

                $obEnum = new CUserFieldEnum();
                $dbEnum = $obEnum->GetList([], $arFilter);

                while ($arEnum = $dbEnum->Fetch()) {
                    $entityField['ENUM'][$arEnum['ID']] = $arEnum;
                }
            }
        }

        return $entityFields;
    }

    /**
     * @param $hlb
     * @return bool|array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function resolve($hlb): bool|array
    {
        $hlBlock = false;

        if (is_numeric($hlb) && (int)$hlb > 0) {
            $hlBlock = HL\HighloadBlockTable::getById($hlb)->fetch();
        }
        else if (mb_strlen($hlb))
        {
            $hlBlock = HL\HighloadBlockTable::getList(['filter' => ['TABLE_NAME' => $hlb]])->fetch();
        }

        return $hlBlock;
    }
}