<?php

declare(strict_types=1);

namespace Acroweb\Mage\Controller;

use Bitrix\Catalog\Product\Basket as CatalogBasket;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Fuser;

/**
 * Контроллер для операций с корзиной (AJAX).
 */
class CartAjax extends Controller
{
    /**
     * @return array
     */
    public function configureActions(): array
    {
        return [
            'add' => [
                'prefilters' => [
                    new Csrf(),
                ],
                '-prefilters' => [
                    '\Bitrix\Main\Engine\ActionFilter\Authentication',
                ],
            ],
        ];
    }

    /**
     * Добавить товар в корзину с доп. свойствами позиции.
     *
     * @param int $productId ID товара (SKU/offer или простой товар)
     * @param mixed $quantity Количество (int|float|string из AJAX)
     * @param array $props Свойства позиции корзины
     * @return array{success:bool,basketId:int|null,quantity:float}|array{success:bool,message:string}
     */
    public function addAction(int $productId, $quantity = 1, array $props = []): array
    {
        try {
            if (!Loader::includeModule('sale') || !Loader::includeModule('catalog')) {
                throw new SystemException('Не удалось подключить модули sale/catalog.');
            }

            $productId = (int)$productId;
            if ($productId <= 0) {
                return ['success' => false, 'message' => 'Некорректный идентификатор товара.'];
            }

            $qty = (float)$quantity;
            if ($qty <= 0) {
                $qty = 1.0;
            }

            $propsToSet = $this->normalizeProps($props);
            $product = [
                'PRODUCT_ID' => $productId,
                'QUANTITY' => $qty,
            ];
            if (!empty($propsToSet)) {
                $product['PROPS'] = $propsToSet;
            }

            $addResult = CatalogBasket::addProduct($product, ['LID' => SITE_ID]);
            if (!$addResult->isSuccess()) {
                $msg = implode('; ', $addResult->getErrorMessages());
                return ['success' => false, 'message' => $msg ?: 'Не удалось добавить товар в корзину.'];
            }

            $data = $addResult->getData();
            $basketId = isset($data['ID']) ? (int)$data['ID'] : null;

            return [
                'success' => true,
                'basketId' => $basketId,
                'quantity' => $qty,
            ];
        } catch (\Throwable $e) {
            $this->addError(new Error('Ошибка добавления в корзину: ' . $e->getMessage()));
            return ['success' => false, 'message' => 'Ошибка добавления в корзину.'];
        }
    }

    /**
     * Нормализовать свойства позиции корзины.
     *
     * Ожидаемый формат входа:
     * - массив массивов: [['CODE'=>'COLOR','NAME'=>'Цвет','VALUE'=>'...','XML_ID'=>'123'], ...]
     * - или {color:{id,name}, lock:{id,name}} из JS
     *
     * @param array $props
     * @return array<int, array{NAME:string,CODE:string,VALUE:string,SORT:int,XML_ID?:string}>
     */
    private function normalizeProps(array $props): array
    {
        $result = [];

        if (isset($props[0]) && is_array($props[0])) {
            foreach ($props as $p) {
                $code = (string)($p['CODE'] ?? '');
                $name = (string)($p['NAME'] ?? '');
                $value = (string)($p['VALUE'] ?? '');
                if ($code === '' || $name === '' || $value === '') {
                    continue;
                }
                $row = [
                    'NAME' => $name,
                    'CODE' => $code,
                    'VALUE' => $value,
                    'SORT' => (int)($p['SORT'] ?? 100),
                ];
                if (isset($p['XML_ID']) && (string)$p['XML_ID'] !== '') {
                    $row['XML_ID'] = (string)$p['XML_ID'];
                }
                $result[] = $row;
            }

            return $result;
        }

        $color = is_array($props['color'] ?? null) ? $props['color'] : null;
        $lock = is_array($props['lock'] ?? null) ? $props['lock'] : null;

        if ($lock && !empty($lock['name'])) {
            $row = [
                'NAME' => 'Запирание',
                'CODE' => 'LOCK_TYPE',
                'VALUE' => (string)$lock['name'],
                'SORT' => 100,
            ];
            if (!empty($lock['id'])) {
                $row['XML_ID'] = (string)$lock['id'];
            }
            $result[] = $row;
        }

        if ($color && !empty($color['name'])) {
            $row = [
                'NAME' => 'Цвет',
                'CODE' => 'COLOR',
                'VALUE' => (string)$color['name'],
                'SORT' => 110,
            ];
            if (!empty($color['id'])) {
                $row['XML_ID'] = (string)$color['id'];
            }
            $result[] = $row;
        }

        return $result;
    }
}

