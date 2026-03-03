<?php

namespace Acroweb\Mage\Helpers;

use Bitrix\Iblock\Iblock;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\ORM\ElementEntity;
use Throwable;

class IblockHelper
{
    public static function resolveIblock($iblock): bool|Iblock
    {
        try {
            if ($iblock instanceof Iblock) {
                return $iblock;
            }

            if (is_numeric($iblock)) {
                return IblockTable::getList([
                    'select' => ['ID', 'API_CODE', 'IBLOCK_TYPE_ID'],
                    'filter' => ['ID' => $iblock],
                ])
                    ->fetchObject();
            } elseif (is_string($iblock)) {
                return IblockTable::getList([
                    'select' => ['ID', 'API_CODE', 'IBLOCK_TYPE_ID'],
                    'filter' => ['API_CODE' => $iblock],
                ])
                    ->fetchObject();
            }
        } catch (Throwable) {
        }

        return false;
    }

    public static function compileEntity($iblock): ElementEntity|bool
    {
        try {
            if (!($ob = self::resolveIblock($iblock))) {
                return false;
            }

            if ($ob->getApiCode() == '') {
                $ob->set('API_CODE', 'IB_' . $ob->getId());
            }

            return IblockTable::compileEntity($ob);
        } catch (Throwable) {
            return false;
        }
    }
}