<?php

namespace Acroweb\Mage\Service;

use Acroweb\Mage\Config;
use Acroweb\Mage\Organization\Service as OrganizationService;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\UserGroupTable;
use COption;

class Manager
{
    /**
     * Получить список всех менеджеров
     * 
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getManagersId(): array
    {
        $result = [];
        $res = UserGroupTable::getList([
            'order' => ['USER.LAST_LOGIN' => 'DESC'],
            'filter' => [
                'USER.ACTIVE' => 'Y',
                'GROUP_ID' => Config::GROUP_MANAGER,
            ],
            'select' => [
                'ID' => 'USER.ID',
                'NAME' => 'USER.NAME',
                'EMAIL' => 'USER.EMAIL',
                'LAST_NAME' => 'USER.LAST_NAME',
                'SECOND_NAME' => 'USER.SECOND_NAME',
            ],
        ]);

        while ($user = $res->fetch()) {
            $result[$user["ID"]] = $user;
        }

        return $result;
    }

    /**
     * Получить ID случайного активного менеджера
     * 
     * @return int|null
     */
    public static function getRandomManagerId(): ?int
    {
        try {
            $managers = self::getManagersId();
            
            if (empty($managers)) {
                return null;
            }
            
            // Получаем массив ID менеджеров
            $managerIds = array_keys($managers);
            
            // Выбираем случайный ID
            $randomKey = array_rand($managerIds);
            
            return (int)$managerIds[$randomKey];
        } catch (\Exception $e) {
            AddMessage2Log('Ошибка получения случайного менеджера: ' . $e->getMessage(), 'acroweb.mage');
            return null;
        }
    }

    public static function getManagerForId(string|int $id)
    {
        $arInfo = User::getInfoUserForId($id, ['ID', 'LAST_NAME', 'NAME', 'SECOND_NAME'])[$id];
        unset($arInfo['ID']);

        return trim(implode(' ', $arInfo));
    }

    public static function getEmailManagerForId(string|int|false|null $id = false)
    {
        $saleManager = COption::GetOptionString("sale", "order_email") ?? 'info@naanit.ru';
        if (!$id) {
            return $saleManager;
        }

        $arInfo = User::getInfoUserForId($id, ['ID', 'EMAIL'])[$id];
        unset($arInfo['ID']);

        return [$saleManager, $arInfo["EMAIL"]];
    }

    /**
     * Отправить уведомление о верификации организации
     * 
     * @param int $orgId ID организации
     * @param int|null $userId ID пользователя, инициировавшего действие
     * @return void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function sendOrganizationVerificationEmail(int $orgId, ?int $userId = null): void
    {
        // Получаем данные организации
        $organization = OrganizationService::getById($orgId);
        
        if (empty($organization)) {
            return;
        }

        $ownerIds = OrganizationService::getOwnerIdsFromProperties($organization['PROPERTIES']);

        if (empty($ownerIds)) {
            return;
        }

        if ($userId && !in_array($userId, $ownerIds, true)) {
            return;
        }

        $targetUserId = $userId ?: (int)reset($ownerIds);

        if (!$targetUserId) {
            return;
        }

        // Получаем данные пользователя (не проверяем ACTIVE, так как пользователь может быть неактивен при регистрации)
        $userInfo = User::getInfoUserForId(
            $targetUserId,
            ['ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'UF_MANAGER_ID'],
            false
        )[$targetUserId] ?? [];
        
        if (empty($userInfo)) {
            return;
        }

        // Формируем ФИО
        $fio = trim(implode(' ', [
            $userInfo['LAST_NAME'] ?? '',
            $userInfo['NAME'] ?? '',
            $userInfo['SECOND_NAME'] ?? '',
        ]));

        // Получаем email менеджера
        $managerId = (int)($userInfo['UF_MANAGER_ID'] ?? 0);
        $emails = self::getEmailManagerForId($managerId ?: false);
        
        if (!is_array($emails)) {
            $emails = [$emails];
        }

        // Отправляем письмо
        Event::send([
            'EVENT_NAME' => 'ORGANIZATION_VERIFICATION',
            'LID' => Config::SITE_ID,
            'C_FIELDS' => [
                'EMAIL' => implode(',', $emails),
                'FIO' => $fio,
                'NAME_ORGANIZATION' => $organization['NAME'] ?? '',
            ],
        ]);
    }
}