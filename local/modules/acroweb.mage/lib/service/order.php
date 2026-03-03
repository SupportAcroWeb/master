<?php

declare(strict_types=1);

namespace Acroweb\Mage\Service;

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Bitrix\Main\Engine\CurrentUser;
use Acroweb\Mage\Organization\Service as OrganizationService;
use Acroweb\Mage\Service\Manager as ManagerService;

/**
 * Сервис для работы с заказами
 */
class Order
{
    /**
     * Обработчик события OnSaleComponentOrderJsData
     * Изменяет формат ФИО плательщика на "Фамилия Имя Отчество"
     *
     * @param array &$arResult Результат компонента (по ссылке)
     * @param array &$arParams Параметры компонента (по ссылке)
     * @return void
     */
    public static function onSaleComponentOrderJsData(
        array &$arResult,
        array &$arParams
    ): void
    {
        if (!Loader::includeModule('main')) {
            return;
        }

        // Получаем текущего пользователя через D7
        $currentUser = CurrentUser::get();
        $userId = (int)$currentUser->getId();
        
        if (!$userId) {
            return;
        }

        // Работаем с JS_DATA
        if (empty($arResult['JS_DATA']['ORDER_PROP']['properties'])) {
            return;
        }

        // Получаем данные пользователя через D7 API (включая UF_MANAGER_ID)
        $user = UserTable::getList([
            'filter' => ['ID' => $userId],
            'select' => ['*', 'UF_*']
        ])->fetch();
        
        if (!$user) {
            return;
        }

        $lastName = trim($user['LAST_NAME'] ?? '');
        $firstName = trim($user['NAME'] ?? '');
        $secondName = trim($user['SECOND_NAME'] ?? '');

        // Формируем ФИО в формате "Фамилия Имя Отчество"
        if ($lastName || $firstName) {
            $fullName = trim(implode(' ', array_filter([$lastName, $firstName, $secondName])));
            
            // Стандартный формат Битрикса: "Имя Фамилия"
            $bitrixDefaultFormat = trim($firstName . ' ' . $lastName);

            // Находим свойство по коду или по IS_PAYER
            foreach ($arResult['JS_DATA']['ORDER_PROP']['properties'] as $key => &$property) {
                $code = $property['CODE'] ?? '';
                $currentValue = is_array($property['VALUE']) ? ($property['VALUE'][0] ?? '') : $property['VALUE'];
                
                // Проверяем свойства ФИО
                if (
                    ($code === 'FIO' || $code === 'CONTACT_PERSON') 
                    || (isset($property['IS_PAYER']) && $property['IS_PAYER'] === 'Y')
                ) {
                    // Заполняем только если:
                    // 1. Поле пустое
                    // 2. Или значение совпадает со стандартным форматом Битрикса "Имя Фамилия"
                    if (
                        empty($currentValue)
                        || $currentValue === $bitrixDefaultFormat
                    ) {
                        $property['VALUE'] = [$fullName];
                    }
                }
            }
            unset($property);
        }
        
        // Добавляем список организаций пользователя
        self::addUserOrganizations($arResult, $userId);
        
        // Добавляем список менеджеров и ID менеджера пользователя
        self::addManagers($arResult, $userId);
        
        // Заполняем свойства менеджера на основе UF_MANAGER_ID
        self::fillManagerProperties($arResult, $user);
        
        // Фильтруем платёжные системы на основе статуса организации
        self::filterPaySystemsByOrganization($arResult, $arParams, $userId);
        
        // Автоматически выбираем первую доступную доставку и оплату
        self::autoSelectDeliveryAndPayment($arResult);
    }
    
    /**
     * Автоматически выбирает первую доступную доставку и оплату, если они не выбраны
     *
     * @param array &$arResult Результат компонента (по ссылке)
     * @return void
     */
    private static function autoSelectDeliveryAndPayment(array &$arResult): void
    {
        // Проверяем и выбираем доставку
        if (isset($arResult['JS_DATA']['DELIVERY']) && is_array($arResult['JS_DATA']['DELIVERY'])) {
            $hasSelectedDelivery = false;
            
            // Проверяем, есть ли уже выбранная доставка
            foreach ($arResult['JS_DATA']['DELIVERY'] as $delivery) {
                if (!empty($delivery['CHECKED']) && $delivery['CHECKED'] === 'Y') {
                    $hasSelectedDelivery = true;
                    break;
                }
            }
            
            // Если нет выбранной, выбираем первую доступную
            if (!$hasSelectedDelivery && !empty($arResult['JS_DATA']['DELIVERY'])) {
                $firstDelivery = reset($arResult['JS_DATA']['DELIVERY']);
                $deliveryId = $firstDelivery['ID'] ?? null;
                
                if ($deliveryId) {
                    foreach ($arResult['JS_DATA']['DELIVERY'] as &$delivery) {
                        if ($delivery['ID'] == $deliveryId) {
                            $delivery['CHECKED'] = 'Y';
                            break;
                        }
                    }
                    unset($delivery);
                }
            }
        }
        
        // Проверяем и выбираем оплату
        if (isset($arResult['JS_DATA']['PAY_SYSTEM']) && is_array($arResult['JS_DATA']['PAY_SYSTEM'])) {
            $hasSelectedPayment = false;
            
            // Проверяем, есть ли уже выбранная оплата
            foreach ($arResult['JS_DATA']['PAY_SYSTEM'] as $paySystem) {
                if (!empty($paySystem['CHECKED']) && $paySystem['CHECKED'] === 'Y') {
                    $hasSelectedPayment = true;
                    break;
                }
            }
            
            // Если нет выбранной, выбираем первую доступную
            if (!$hasSelectedPayment && !empty($arResult['JS_DATA']['PAY_SYSTEM'])) {
                $firstPaySystem = reset($arResult['JS_DATA']['PAY_SYSTEM']);
                $paySystemId = $firstPaySystem['ID'] ?? null;
                
                if ($paySystemId) {
                    foreach ($arResult['JS_DATA']['PAY_SYSTEM'] as &$paySystem) {
                        if ($paySystem['ID'] == $paySystemId) {
                            $paySystem['CHECKED'] = 'Y';
                            break;
                        }
                    }
                    unset($paySystem);
                }
            }
        }
    }
    
    /**
     * Фильтрует платёжные системы на основе статуса организации
     * Если выбранная организация не одобрена, скрывает определённые способы оплаты
     *
     * @param array &$arResult Результат компонента (по ссылке)
     * @param array $arParams Параметры компонента
     * @param int $userId ID пользователя
     * @return void
     */
    private static function filterPaySystemsByOrganization(array &$arResult, array $arParams, int $userId): void
    {
        // Получаем список платёжных систем из параметров компонента
        $restrictedPaySystemIds = !empty($arParams['RESTRICTED_PAY_SYSTEMS']) 
            ? (array)$arParams['RESTRICTED_PAY_SYSTEMS'] 
            : [];
        
        // Если список пустой, ничего не делаем
        if (empty($restrictedPaySystemIds)) {
            return;
        }
        
        // Получаем список организаций пользователя
        try {
            $organizations = OrganizationService::getListByUser($userId);
            if (empty($organizations)) {
                return; // Нет организаций - ничего не делаем
            }
        } catch (\Exception $e) {
            return;
        }
        
        // Получаем ID выбранной организации из свойства ORGANIZATION_ID
        $selectedOrganizationId = 0;
        
        if (isset($arResult['JS_DATA']['ORDER_PROP']['properties'])) {
            foreach ($arResult['JS_DATA']['ORDER_PROP']['properties'] as $prop) {
                if (($prop['CODE'] ?? '') === 'ORGANIZATION_ID') {
                    $value = $prop['VALUE'] ?? [];
                    if (is_array($value) && !empty($value[0])) {
                        $selectedOrganizationId = (int)$value[0];
                    } elseif (!is_array($value) && !empty($value)) {
                        $selectedOrganizationId = (int)$value;
                    }
                    break;
                }
            }
        }
        
        // Если не нашли значение, берём первую организацию
        if (!$selectedOrganizationId) {
            $firstOrg = reset($organizations);
            $selectedOrganizationId = (int)$firstOrg['ID'];
        }
        
        if (!$selectedOrganizationId) {
            return;
        }
        
        // Находим выбранную организацию в списке
        $selectedOrg = null;
        foreach ($organizations as $org) {
            if ((int)$org['ID'] === $selectedOrganizationId) {
                $selectedOrg = $org;
                break;
            }
        }
        
        if (!$selectedOrg) {
            return;
        }
        
        // Проверяем статус организации
        $orgStatus = $selectedOrg['PROPERTIES'][OrganizationService::PROP_STATUS]['VALUE_XML_ID'] ?? '';
        $isApproved = $orgStatus === OrganizationService::STATUS_APPROVED;
        
        // Если организация не одобрена, удаляем запрещённые платёжные системы
        if (!$isApproved && isset($arResult['JS_DATA']['PAY_SYSTEM'])) {
            foreach ($arResult['JS_DATA']['PAY_SYSTEM'] as $key => $paySystem) {
                if (in_array((int)$paySystem['ID'], array_map('intval', $restrictedPaySystemIds))) {
                    unset($arResult['JS_DATA']['PAY_SYSTEM'][$key]);
                }
            }
            // Переиндексируем массив для корректной работы JS
            $arResult['JS_DATA']['PAY_SYSTEM'] = array_values($arResult['JS_DATA']['PAY_SYSTEM']);
        }
    }
    
    /**
     * Добавляет список организаций пользователя в JS_DATA
     *
     * @param array &$arResult Результат компонента (по ссылке)
     * @param int $userId ID пользователя
     * @return void
     */
    private static function addUserOrganizations(array &$arResult, int $userId): void
    {
        try {
            // Получаем организации пользователя
            $organizations = OrganizationService::getListByUser($userId);
            
            // Фильтруем только одобренные организации
//            $approvedOrganizations = array_filter($organizations, function($org) {
//                return isset($org['PROPERTIES'][OrganizationService::PROP_STATUS]['VALUE_XML_ID'])
//                    && $org['PROPERTIES'][OrganizationService::PROP_STATUS]['VALUE_XML_ID'] === OrganizationService::STATUS_APPROVED;
//            });

            // Формируем список для JS
            $organizationsList = [];
            foreach ($organizations as $org) {
                $organizationsList[] = [
                    'ID' => (int)$org['ID'],
                    'NAME' => $org['NAME'],
                    'INN' => $org['PROPERTIES'][OrganizationService::PROP_INN]['VALUE'] ?? '',
                    'KPP' => $org['PROPERTIES'][OrganizationService::PROP_KPP]['VALUE'] ?? '',
                    'ADDRESS' => $org['PROPERTIES'][OrganizationService::PROP_UR_ADDRESS]['VALUE'] ?? '',
                ];
            }
            
            // Добавляем в JS_DATA
            $arResult['JS_DATA']['USER_ORGANIZATIONS'] = $organizationsList;
        } catch (\Exception $e) {
            // В случае ошибки возвращаем пустой массив
            $arResult['JS_DATA']['USER_ORGANIZATIONS'] = [];
        }
    }
    
    /**
     * Добавляет список менеджеров и ID менеджера пользователя в JS_DATA
     *
     * @param array &$arResult Результат компонента (по ссылке)
     * @param int $userId ID пользователя
     * @return void
     */
    private static function addManagers(array &$arResult, int $userId): void
    {
        try {
            // Получаем данные пользователя с UF_MANAGER_ID
            $user = UserTable::getList([
                'filter' => ['ID' => $userId],
                'select' => ['ID', 'UF_MANAGER_ID']
            ])->fetch();
            
            $userManagerId = isset($user['UF_MANAGER_ID']) ? (int)$user['UF_MANAGER_ID'] : 0;
            
            // Получаем список всех менеджеров
            $managers = ManagerService::getManagersId();
            
            // Формируем список для JS
            $managersList = [];
            
            // Добавляем опцию "Без менеджера"
            $managersList[] = [
                'ID' => 0,
                'NAME' => 'Без менеджера',
                'EMAIL' => '',
                'FIO' => ''
            ];
            
            // Добавляем менеджеров
            foreach ($managers as $managerId => $manager) {
                $fio = trim(implode(' ', [
                    $manager['LAST_NAME'] ?? '',
                    $manager['NAME'] ?? '',
                    $manager['SECOND_NAME'] ?? ''
                ]));
                
                $managersList[] = [
                    'ID' => (int)$managerId,
                    'NAME' => $fio ?: ('Менеджер #' . $managerId),
                    'EMAIL' => $manager['EMAIL'] ?? '',
                    'FIO' => $fio
                ];
            }
            
            // Добавляем в JS_DATA
            $arResult['JS_DATA']['MANAGERS_LIST'] = $managersList;
            $arResult['JS_DATA']['USER_MANAGER_ID'] = $userManagerId;
        } catch (\Exception $e) {
            // В случае ошибки возвращаем пустые данные
            $arResult['JS_DATA']['MANAGERS_LIST'] = [
                [
                    'ID' => 0,
                    'NAME' => 'Без менеджера',
                    'EMAIL' => '',
                    'FIO' => ''
                ]
            ];
            $arResult['JS_DATA']['USER_MANAGER_ID'] = 0;
        }
    }
    
    /**
     * Заполняет свойства менеджера на основе UF_MANAGER_ID пользователя
     *
     * @param array &$arResult Результат компонента (по ссылке)
     * @param array $user Данные пользователя
     * @return void
     */
    private static function fillManagerProperties(array &$arResult, array $user): void
    {
        $userManagerId = isset($user['UF_MANAGER_ID']) ? (int)$user['UF_MANAGER_ID'] : 0;
        
        // Если менеджер не назначен, очищаем поля
        if (!$userManagerId) {
            foreach ($arResult['JS_DATA']['ORDER_PROP']['properties'] as $key => &$property) {
                $code = $property['CODE'] ?? '';
                if ($code === 'FIO_MANAGER' || $code === 'EMAIL_MANAGER') {
                    $property['VALUE'] = [''];
                }
            }
            unset($property);
            return;
        }
        
        // Получаем список менеджеров
        try {
            $managers = ManagerService::getManagersId();
            
            if (!isset($managers[$userManagerId])) {
                return;
            }
            
            $manager = $managers[$userManagerId];
            
            // Формируем ФИО менеджера
            $managerFio = trim(implode(' ', [
                $manager['LAST_NAME'] ?? '',
                $manager['NAME'] ?? '',
                $manager['SECOND_NAME'] ?? ''
            ]));
            
            $managerEmail = $manager['EMAIL'] ?? '';
            
            // Заполняем свойства (PHP заполняет начальные значения, JS обновляет их при изменении селекта)
            foreach ($arResult['JS_DATA']['ORDER_PROP']['properties'] as $key => &$property) {
                $code = $property['CODE'] ?? '';
                
                if ($code === 'FIO_MANAGER') {
                    $property['VALUE'] = [$managerFio];
                } elseif ($code === 'EMAIL_MANAGER') {
                    $property['VALUE'] = [$managerEmail];
                }
            }
            unset($property);
        } catch (\Exception $e) {
            // В случае ошибки ничего не делаем
        }
    }
}
