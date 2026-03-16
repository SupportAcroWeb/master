<?php

declare(strict_types=1);

namespace Acroweb\Mage\Service;

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;

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
    
}
