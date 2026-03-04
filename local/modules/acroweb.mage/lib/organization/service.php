<?php

declare(strict_types=1);

namespace Acroweb\Mage\Organization;

use Acroweb\Mage\Config;
use Bitrix\Main\ArgumentException;
use CIBlockElement;

/**
 * CRUD-сервис для работы с организациями
 */
class Service
{
    /** Коды свойств инфоблока */
    public const PROP_STATUS = 'STATUS';
    public const PROP_INN = 'INN';
    public const PROP_KPP = 'KPP';
    public const PROP_UR_ADDRESS = 'UR_ADDRESS';
    public const PROP_FILE = 'FILE';
    public const PROP_USER_ID = 'USER_ID';

    /** Значения статусов */
    public const STATUS_APPROVED = 'Y';
    public const STATUS_REJECTED = 'N';
    public const STATUS_PENDING = 'S';

    /**
     * Получить ID инфоблока организаций
     * 
     * @return int
     */
    public static function getIblockId(): int
    {
        return (int)(Config::getParams()['IBLOCKS']['organization']['ID'] ?? 0);
    }

    /**
     * Добавить организацию
     * 
     * @param array $fields Поля элемента и свойства
     * @return int ID созданного элемента
     * @throws ArgumentException
     */
    public static function add(array $fields): int
    {
        try {
            $el = new CIBlockElement();
            $iblockId = self::getIblockId();
            
            if (isset($fields['PROPERTIES'][self::PROP_INN], $fields['NAME'])) {
                self::validateUniqueInnAndName(
                    (string)$fields['PROPERTIES'][self::PROP_INN],
                    (string)$fields['NAME']
                );
            }

            if (isset($fields['PROPERTIES'][self::PROP_USER_ID])) {
                $fields['PROPERTIES'][self::PROP_USER_ID] = self::prepareOwnerPropertyValues(
                    $fields['PROPERTIES'][self::PROP_USER_ID]
                );
            }

            // Преобразуем PROPERTIES в PROPERTY_VALUES для Add
            if (isset($fields['PROPERTIES'])) {
                // Для свойства STATUS (список) нужно получить ID значения по XML_ID
                if (isset($fields['PROPERTIES'][self::PROP_STATUS]) && !empty($fields['PROPERTIES'][self::PROP_STATUS])) {
                    $enumId = self::getEnumIdByXmlId(self::PROP_STATUS, $fields['PROPERTIES'][self::PROP_STATUS]);
                    if ($enumId) {
                        $fields['PROPERTIES'][self::PROP_STATUS] = $enumId;
                    }
                }
                
                $fields['PROPERTY_VALUES'] = $fields['PROPERTIES'];
                unset($fields['PROPERTIES']);
            }
            
            $fields['IBLOCK_ID'] = $iblockId;
            $fields['ACTIVE'] = $fields['ACTIVE'] ?? 'Y';

            $elementId = $el->Add($fields);

            if (!$elementId) {
                throw new ArgumentException($el->LAST_ERROR ?: 'Ошибка при создании организации');
            }

            return (int)$elementId;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Обновить организацию
     * 
     * @param int $id ID элемента
     * @param array $fields Поля для обновления
     * @return bool
     * @throws ArgumentException
     */
    public static function update(int $id, array $fields): bool
    {
        try {
            $el = new CIBlockElement();

            // Обновление основных полей
            if (isset($fields['NAME']) || isset($fields['ACTIVE'])) {
                $updateFields = [];
                if (isset($fields['NAME'])) {
                    $updateFields['NAME'] = $fields['NAME'];
                }
                if (isset($fields['ACTIVE'])) {
                    $updateFields['ACTIVE'] = $fields['ACTIVE'];
                }

                if (!$el->Update($id, $updateFields)) {
                    throw new ArgumentException($el->LAST_ERROR ?: 'Ошибка при обновлении организации');
                }
            }

            // Обновление свойств
            if (isset($fields['PROPERTIES']) && is_array($fields['PROPERTIES'])) {
                $el = new CIBlockElement();
                foreach ($fields['PROPERTIES'] as $propCode => $propValue) {
                    // Для свойства STATUS (список) нужно получить ID значения по XML_ID
                    if ($propCode === self::PROP_STATUS && !empty($propValue)) {
                        $enumId = self::getEnumIdByXmlId($propCode, $propValue);
                        if ($enumId) {
                            $propValue = $enumId;
                        }
                    }
                    if ($propCode === self::PROP_USER_ID) {
                        $propValue = self::prepareOwnerPropertyValues($propValue);
                    }
                    $el->SetPropertyValueCode($id, $propCode, $propValue);
                }
            }

            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Удалить организацию
     * 
     * @param int $id ID элемента
     * @return bool
     */
    public static function delete(int $id): bool
    {
        return (bool)CIBlockElement::Delete($id);
    }

    /**
     * Получить организацию по ID
     * 
     * @param int $id ID организации
     * @return array
     */
    public static function getById(int $id): array
    {
        $element = Repository::getById($id);
        
        if (!$element) {
            return [];
        }

        // Получаем свойства
        $properties = self::getProperties($id);
        $element['PROPERTIES'] = $properties;

        return $element;
    }

    /**
     * Получить список организаций пользователя
     * 
     * @param int $userId ID пользователя
     * @return array
     */
    public static function getListByUser(int $userId): array
    {
        $elements = Repository::getListByUser($userId);

        foreach ($elements as &$element) {
            $element['PROPERTIES'] = self::getProperties((int)$element['ID']);
        }

        return $elements;
    }

    /**
     * Получить свойства элемента
     * 
     * @param int $elementId ID элемента
     * @return array
     */
    private static function getProperties(int $elementId): array
    {
        $result = [];
        $props = CIBlockElement::GetProperty(
            self::getIblockId(),
            $elementId,
            ['sort' => 'asc'],
            []
        );

        $neededProps = [
            self::PROP_STATUS,
            self::PROP_INN,
            self::PROP_KPP,
            self::PROP_UR_ADDRESS,
            self::PROP_FILE,
            self::PROP_USER_ID,
        ];

        while ($prop = $props->Fetch()) {
            // Фильтруем только нужные свойства
            if (in_array($prop['CODE'], $neededProps)) {
                if ($prop['CODE'] === self::PROP_USER_ID) {
                    if (!isset($result[self::PROP_USER_ID])) {
                        $result[self::PROP_USER_ID] = [
                            'ID' => $prop['ID'],
                            'VALUE' => [],
                            'VALUE_ENUM_ID' => [],
                            'VALUE_XML_ID' => [],
                            'VALUE_ENUM' => [],
                            'DESCRIPTION' => '',
                        ];
                    }
                    if ((int)$prop['VALUE'] > 0) {
                        $result[self::PROP_USER_ID]['VALUE'][] = (int)$prop['VALUE'];
                    }
                    continue;
                }

                $result[$prop['CODE']] = [
                    'ID' => $prop['ID'],
                    'VALUE' => $prop['VALUE'],
                    'VALUE_ENUM_ID' => $prop['VALUE_ENUM_ID'] ?? null,
                    'VALUE_XML_ID' => $prop['VALUE_XML_ID'] ?? null,
                    'VALUE_ENUM' => $prop['VALUE_ENUM'] ?? null,
                    'DESCRIPTION' => $prop['DESCRIPTION'],
                ];
            }
        }

        return $result;
    }

    /**
     * Валидация уникальности ИНН и названия
     * 
     * @param string $inn ИНН организации
     * @param string $name Название организации
     * @param int|null $excludeId ID элемента для исключения из проверки
     * @return void
     * @throws ArgumentException
     */
    public static function validateUniqueInnAndName(string $inn, string $name, ?int $excludeId = null): void
    {
        $filter = [
            'IBLOCK_ID' => self::getIblockId(),
            'ACTIVE' => 'Y',
            [
                'LOGIC' => 'OR',
                ['PROPERTY_' . self::PROP_INN => $inn],
                ['NAME' => $name],
            ],
        ];

        if ($excludeId) {
            $filter['!ID'] = $excludeId;
        }

        $res = CIBlockElement::GetList(
            [],
            $filter,
            false,
            ['nTopCount' => 1],
            ['ID', 'NAME', 'PROPERTY_' . self::PROP_INN]
        );

        if ($item = $res->Fetch()) {
            if ($item['PROPERTY_' . self::PROP_INN . '_VALUE'] === $inn) {
                throw new ArgumentException('Организация с таким ИНН уже существует');
            }
            if ($item['NAME'] === $name) {
                throw new ArgumentException('Организация с таким названием уже существует');
            }
        }
    }

    /**
     * Получить ID значения списка по XML_ID
     * 
     * @param string $propCode Код свойства
     * @param string $xmlId XML_ID значения
     * @return int|null
     */
    private static function getEnumIdByXmlId(string $propCode, string $xmlId): ?int
    {
        $property = \CIBlockProperty::GetList(
            [],
            [
                'IBLOCK_ID' => self::getIblockId(),
                'CODE' => $propCode,
            ]
        )->Fetch();

        if (!$property) {
            return null;
        }

        $enum = \CIBlockPropertyEnum::GetList(
            [],
            [
                'PROPERTY_ID' => $property['ID'],
                'XML_ID' => $xmlId,
            ]
        )->Fetch();

        return $enum ? (int)$enum['ID'] : null;
    }

    /**
     * Получить текстовое представление статуса
     * 
     * @param string $statusCode Код статуса
     * @return string
     */
    public static function getStatusText(string $statusCode): string
    {
        return match ($statusCode) {
            self::STATUS_APPROVED => 'Одобрена',
            self::STATUS_REJECTED => 'Отклонена',
            self::STATUS_PENDING => 'На проверке',
            default => 'Не проверена',
        };
    }

    /**
     * Найти организацию по ИНН или названию
     *
     * @param string|null $inn ИНН
     * @param string|null $name Название
     * @return array|null
     */
    public static function findByInnOrName(?string $inn, ?string $name): ?array
    {
        $iblockId = self::getIblockId();

        if (!$iblockId) {
            return null;
        }

        $inn = trim((string)$inn);
        $name = trim((string)$name);

        if ($inn !== '') {
            $element = self::fetchElementByFilter([
                'IBLOCK_ID' => $iblockId,
                'ACTIVE' => 'Y',
                '=PROPERTY_' . self::PROP_INN => $inn,
            ]);
            if ($element) {
                return self::getById((int)$element['ID']);
            }
        }

        if ($name !== '') {
            $element = self::fetchElementByFilter([
                'IBLOCK_ID' => $iblockId,
                'ACTIVE' => 'Y',
                '=NAME' => $name,
            ]);
            if ($element) {
                return self::getById((int)$element['ID']);
            }
        }

        return null;
    }

    /**
     * Получить массив владельцев организации
     *
     * @param int $organizationId ID организации
     * @return array
     */
    public static function getOwnerIds(int $organizationId): array
    {
        if ($organizationId <= 0) {
            return [];
        }

        $properties = self::getProperties($organizationId);

        return self::getOwnerIdsFromProperties($properties);
    }

    /**
     * Получить массив владельцев на основе свойств
     *
     * @param array $properties Свойства элемента
     * @return array
     */
    public static function getOwnerIdsFromProperties(array $properties): array
    {
        if (!isset($properties[self::PROP_USER_ID])) {
            return [];
        }

        $ownerProp = $properties[self::PROP_USER_ID];
        $values = $ownerProp['VALUE'] ?? [];

        if (!is_array($values)) {
            $values = [$values];
        }

        $owners = [];
        foreach ($values as $value) {
            $ownerId = (int)$value;
            if ($ownerId > 0) {
                $owners[] = $ownerId;
            }
        }

        return array_values(array_unique($owners));
    }

    /**
     * Привязать пользователя к организации
     *
     * @param int $organizationId ID организации
     * @param int $userId ID пользователя
     * @return bool
     */
    public static function addOwner(int $organizationId, int $userId): bool
    {
        if ($organizationId <= 0 || $userId <= 0) {
            return false;
        }

        $owners = self::getOwnerIds($organizationId);

        if (in_array($userId, $owners, true)) {
            return true;
        }

        $owners[] = $userId;

        return self::persistOwnerIds($organizationId, $owners);
    }

    /**
     * Отвязать пользователя от организации
     *
     * @param int $organizationId ID организации
     * @param int $userId ID пользователя
     * @return bool
     */
    public static function removeOwner(int $organizationId, int $userId): bool
    {
        if ($organizationId <= 0 || $userId <= 0) {
            return false;
        }

        $owners = self::getOwnerIds($organizationId);

        if (empty($owners)) {
            return true;
        }

        $filtered = array_filter(
            $owners,
            static fn(int $ownerId): bool => $ownerId !== $userId
        );

        if ($owners === $filtered) {
            return true;
        }

        return self::persistOwnerIds($organizationId, $filtered);
    }

    /**
     * Проверить принадлежность организации пользователю
     *
     * @param array $organization Данные организации (с PROPERTIES)
     * @param int $userId ID пользователя
     * @return bool
     */
    public static function userHasAccess(array $organization, int $userId): bool
    {
        if ($userId <= 0 || empty($organization['PROPERTIES'])) {
            return false;
        }

        $owners = self::getOwnerIdsFromProperties($organization['PROPERTIES']);

        return in_array($userId, $owners, true);
    }

    /**
     * Сохранить список владельцев
     *
     * @param int $organizationId ID организации
     * @param array $ownerIds Список владельцев
     * @return bool
     */
    private static function persistOwnerIds(int $organizationId, array $ownerIds): bool
    {
        $ownerIds = array_values(array_unique(array_filter(
            array_map(static fn($id) => (int)$id, $ownerIds),
            static fn(int $id): bool => $id > 0
        )));

        if (empty($ownerIds)) {
            \CIBlockElement::SetPropertyValueCode($organizationId, self::PROP_USER_ID, false);
            return true;
        }

        \CIBlockElement::SetPropertyValuesEx(
            $organizationId,
            self::getIblockId(),
            [self::PROP_USER_ID => $ownerIds]
        );

        return true;
    }

    /**
     * Преобразовать входящие значения владельцев к массиву
     *
     * @param mixed $value Значение свойства
     * @return array
     */
    private static function prepareOwnerPropertyValues(mixed $value): array
    {
        if (is_array($value) && array_key_exists('VALUE', $value)) {
            $value = $value['VALUE'];
        }

        $rawValues = is_array($value) ? $value : [$value];
        $ownerIds = [];

        foreach ($rawValues as $rawValue) {
            $ownerId = (int)$rawValue;
            if ($ownerId > 0) {
                $ownerIds[] = $ownerId;
            }
        }

        return array_values(array_unique($ownerIds));
    }

    /**
     * Найти элемент по фильтру
     *
     * @param array $filter Фильтр
     * @return array|null
     */
    private static function fetchElementByFilter(array $filter): ?array
    {
        $res = CIBlockElement::GetList([], $filter, false, ['nTopCount' => 1], ['ID']);

        $element = $res->Fetch();

        return $element ?: null;
    }
}

