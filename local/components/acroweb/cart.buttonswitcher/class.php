<?php

declare(strict_types=1);

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Web\Json;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use CBitrixComponent;
use Exception;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Компонент для автоматического переключения кнопок "Купить" на "В корзине"
 * для товаров, уже добавленных в корзину текущего пользователя.
 *
 * Производительность:
 * - Один запрос к корзине за весь цикл работы компонента
 * - Передача массива ID в клиент для быстрого поиска по data-атрибутам
 * - Минимальные манипуляции с DOM на клиенте
 *
 * @package Acroweb\Components
 */
class CartButtonSwitcher extends CBitrixComponent implements Controllerable
{
    /**
     * Кешированный массив ID товаров в корзине
     *
     * @var array<int>|null
     */
    private ?array $basketProductIds = null;

    /**
     * Настройка доступа к AJAX-методам компонента
     *
     * @return array<string, array<string, string>>
     */
    public function configureActions(): array
    {
        return [
            'getBasketIds' => [
                'prefilters' => [],
                'postfilters' => [],
            ],
        ];
    }

    /**
     * AJAX-метод для получения списка ID товаров в корзине
     *
     * @return array<int>
     */
    public function getBasketIdsAction(): array
    {
        if (!Loader::includeModule('sale')) {
            return [];
        }

        if (!Loader::includeModule('catalog')) {
            return [];
        }

        return $this->getBasketProductIds();
    }

    /**
     * Подключение необходимых модулей
     *
     * @return bool
     * @throws LoaderException
     */
    protected function checkModules(): bool
    {
        if (!Loader::includeModule('sale')) {
            ShowError('Модуль "Интернет-магазин" не установлен');
            return false;
        }

        if (!Loader::includeModule('catalog')) {
            ShowError('Модуль "Торговый каталог" не установлен');
            return false;
        }

        return true;
    }

    /**
     * Получение ID товаров из корзины текущего пользователя
     * Один запрос, результат кешируется в памяти на время выполнения компонента
     *
     * @return array<int>
     */
    protected function getBasketProductIds(): array
    {
        if ($this->basketProductIds !== null) {
            return $this->basketProductIds;
        }

        $this->basketProductIds = [];

        try {
            $fUserId = Fuser::getId();
            if (!$fUserId) {
                return $this->basketProductIds;
            }

            $siteId = SITE_ID;
            $basket = Basket::loadItemsForFUser($fUserId, $siteId);

            if ($basket && !$basket->isEmpty()) {
                /** @var Bitrix\Sale\BasketItem $item */
                foreach ($basket as $item) {
                    $productId = (int)$item->getProductId();
                    if ($productId > 0) {
                        $this->basketProductIds[] = $productId;
                    }
                }
            }

            // Убираем дубли и переиндексируем
            $this->basketProductIds = array_values(array_unique($this->basketProductIds));
        } catch (Exception $e) {
            // Логируем ошибку, но не прерываем работу
            AddMessage2Log(
                'CartButtonSwitcher: ошибка получения корзины - ' . $e->getMessage(),
                'acroweb.cart.buttonswitcher'
            );
        }

        return $this->basketProductIds;
    }

    /**
     * Точка входа компонента
     *
     * @return void
     */
    public function executeComponent(): void
    {
        try {
            if (!$this->checkModules()) {
                return;
            }

            // Получаем ID товаров в корзине одним запросом
            $basketIds = $this->getBasketProductIds();

            // Передаём в шаблон для рендера в data-атрибут
            $this->arResult = [
                'BASKET_PRODUCT_IDS' => $basketIds,
            ];

            $this->includeComponentTemplate();
        } catch (Exception $e) {
            ShowError('Ошибка работы компонента: ' . $e->getMessage());
            AddMessage2Log(
                'CartButtonSwitcher executeComponent error: ' . $e->getMessage(),
                'acroweb.cart.buttonswitcher'
            );
        }
    }
}

