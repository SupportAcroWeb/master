<?php

declare(strict_types=1);

namespace Acroweb\Mage\Organization;

use Acroweb\Mage\Config;
use CIBlockElement;

/**
 * Репозиторий для работы с организациями
 * 
 * Использует CIBlockElement API для работы с инфоблоками
 */
class Repository
{
    /**
     * Получить список организаций пользователя
     * 
     * @param int $userId ID пользователя-владельца
     * @return array
     */
    public static function getListByUser(int $userId): array
    {
        $result = [];
        $iblockId = Config::getParams()['IBLOCKS']['organization']['ID'] ?? 0;
        
        if (!$iblockId) {
            return $result;
        }
        
        $res = CIBlockElement::GetList(
            ['ID' => 'DESC'],
            [
                'IBLOCK_ID' => $iblockId,
                'ACTIVE' => 'Y',
                'PROPERTY_USER_ID' => $userId,
            ],
            false,
            false,
            ['ID', 'NAME', 'IBLOCK_ID', 'ACTIVE', 'DATE_CREATE', 'TIMESTAMP_X']
        );

        while ($item = $res->Fetch()) {
            $result[] = $item;
        }

        return $result;
    }

    /**
     * Получить организацию по ID
     * 
     * @param int $id ID организации
     * @return array|null
     */
    public static function getById(int $id): ?array
    {
        $iblockId = Config::getParams()['IBLOCKS']['organization']['ID'] ?? 0;
        
        if (!$iblockId) {
            return null;
        }
        
        $res = CIBlockElement::GetList(
            [],
            [
                'ID' => $id,
                'IBLOCK_ID' => $iblockId,
            ],
            false,
            ['nTopCount' => 1],
            ['ID', 'NAME', 'IBLOCK_ID', 'ACTIVE', 'DATE_CREATE', 'TIMESTAMP_X']
        );

        return $res->Fetch() ?: null;
    }
}

