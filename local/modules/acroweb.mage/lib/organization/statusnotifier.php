<?php

declare(strict_types=1);

namespace Acroweb\Mage\Organization;

use Acroweb\Mage\Config;
use Acroweb\Mage\Service\User;
use Bitrix\Main\Mail\Event;
use CIBlockElement;
use Bitrix\Main\Diag\Debug;

/**
 * Класс для отслеживания изменений статуса организации
 */
class StatusNotifier
{
    /**
     * Обработчик события OnAfterIBlockElementSetPropertyValues
     * Срабатывает после изменения свойств элемента
     * 
     * @param int $elementId ID элемента
     * @param int $iblockId ID инфоблока
     * @param array $propertyValues Массив значений свойств (ключи - ID свойств)
     * @param string $propertyCode Код свойства
     * @return void
     */
    public static function onAfterIBlockElementSetPropertyValues(
        int $elementId,
        int $iblockId,
        array $propertyValues = [],
        string $propertyCode = ''
    ): void {
        // Проверяем, что это элемент инфоблока организаций
        if ($iblockId !== Service::getIblockId()) {
            return;
        }

        // Получаем ID свойства STATUS
        $statusPropertyId = self::getPropertyIdByCode(Service::getIblockId(), Service::PROP_STATUS);

        // Проверяем, что изменялось свойство STATUS
        if (!$statusPropertyId || !isset($propertyValues[$statusPropertyId])) {
            return;
        }

        // Получаем новый статус
        $props = CIBlockElement::GetProperty(
            Service::getIblockId(),
            $elementId,
            [],
            ['CODE' => Service::PROP_STATUS]
        );
        
        if (!$prop = $props->Fetch()) {
            return;
        }

        $newStatus = (string)($prop['VALUE_XML_ID'] ?? '');

        // Отправляем уведомление только если статус изменился на Y (Одобрена) или N (Отклонена)
        if (in_array($newStatus, [Service::STATUS_APPROVED, Service::STATUS_REJECTED], true)) {
            self::sendStatusChangeNotification($elementId, $newStatus);
        }
    }

    /**
     * Получить ID свойства по его коду
     * 
     * @param int $iblockId ID инфоблока
     * @param string $code Код свойства
     * @return int|null ID свойства или null
     */
    private static function getPropertyIdByCode(int $iblockId, string $code): ?int
    {
        $property = \CIBlockProperty::GetList(
            [],
            [
                'IBLOCK_ID' => $iblockId,
                'CODE' => $code,
            ]
        )->Fetch();

        return $property ? (int)$property['ID'] : null;
    }

    /**
     * Отправить уведомление об изменении статуса
     * 
     * @param int $elementId ID организации
     * @param string $newStatus Новый статус
     * @return void
     */
    private static function sendStatusChangeNotification(int $elementId, string $newStatus): void
    {
        try {
            // Получаем данные организации
            $organization = Service::getById($elementId);
            
            if (empty($organization)) {
                return;
            }

            $ownerIds = Service::getOwnerIdsFromProperties($organization['PROPERTIES']);

            if (empty($ownerIds)) {
                return;
            }

            $users = User::getInfoUserForId(
                $ownerIds,
                ['ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'EMAIL']
            );

            $statusText = Service::getStatusText($newStatus);

            foreach ($ownerIds as $ownerId) {
                $userInfo = $users[$ownerId] ?? null;

                if (empty($userInfo) || empty($userInfo['EMAIL'])) {
                    continue;
                }

                $fio = trim(implode(' ', [
                    $userInfo['LAST_NAME'] ?? '',
                    $userInfo['NAME'] ?? '',
                    $userInfo['SECOND_NAME'] ?? '',
                ]));

                Event::send([
                    'EVENT_NAME' => 'CHANGE_STATUS_ORGANIZATION',
                    'LID' => Config::SITE_ID,
                    'C_FIELDS' => [
                        'EMAIL' => $userInfo['EMAIL'],
                        'FIO' => $fio,
                        'NAME_ORGANIZATION' => $organization['NAME'] ?? '',
                        'STATUS' => $statusText,
                    ],
                ]);
            }
        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем выполнение
            Debug::writeToFile($e->getMessage(), '', 'organization_status_notifier.log');
        }
    }
}

