<?php

namespace Acroweb\Mage;

use Acroweb\Mage\Helpers\ArrayHelper;

trait CacheTrait
{
    /** @var array Статическое защищенное свойство для хранения кэша */
    protected static array $cache = [];

    /**
     * Устанавливает значение в кэш по указанному ключу
     * @param string|int|array $key Ключ кэша (может быть строкой, целым числом или массивом для вложенных ключей)
     * @param mixed $value Значение для сохранения в кэш
     */
    public static function setCache(string|int|array $key, mixed $value): void
    {
        ArrayHelper::setValue(self::$cache, $key, $value);
    }

    /**
     * Возвращает весь кэш
     * @return array Весь кэш
     */
    public static function getCacheAll(): array
    {
        return self::$cache;
    }

    /**
     * Получает значение из кэша по указанному ключу
     * @param string|int|array $key Ключ кэша (может быть строкой, целым числом или массивом для вложенных ключей)
     * @param mixed $default Значение по умолчанию, если ключ не найден
     * @return mixed Значение из кэша или значение по умолчанию
     */
    public static function getCache(string|int|array $key, mixed $default = null): mixed
    {
        return ArrayHelper::getValue(self::$cache, $key, $default);
    }

    /**
     * Очищает весь кэш
     */
    public static function clearCache(): void
    {
        self::$cache = [];
    }

    /**
     * Проверяет наличие ключа в кэше
     * @param string|int|array $key Ключ для проверки
     * @return bool True, если ключ существует, иначе False
     */
    public static function hasCache(string|int|array $key): bool
    {
        return ArrayHelper::keyExists(self::$cache, $key);
    }

    /**
     * Удаляет значение из кэша по указанному ключу
     * @param string|int|array $key Ключ для удаления
     */
    public static function removeCache(string|int|array $key): void
    {
        ArrayHelper::removeValue(self::$cache, $key);
    }
}