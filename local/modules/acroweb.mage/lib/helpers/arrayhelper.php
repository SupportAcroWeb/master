<?php

namespace Acroweb\Mage\Helpers;

use Closure;

class ArrayHelper
{
    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof Closure) {
            return $key($array, $default);
        }

        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = static::getValue($array, $keyPart);
            }
            $key = $lastKey;
        }

        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            return $array->$key;
        } elseif (is_array($array)) {
            return (isset($array[$key]) || array_key_exists($key, $array)) ? $array[$key] : $default;
        }

        return $default;
    }

    public static function setValue(&$array, $path, $value): void
    {
        if ($path === null) {
            $array = $value;
            return;
        }

        $keys = is_array($path) ? $path : explode('.', $path);

        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key])) {
                $array[$key] = [];
            }
            if (!is_array($array[$key])) {
                $array[$key] = [$array[$key]];
            }
            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;
    }

    public static function remove(&$array, $key, $default = null)
    {
        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            $value = $array[$key];
            unset($array[$key]);

            return $value;
        }

        return $default;
    }

    public static function merge($a, $b)
    {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            foreach (array_shift($args) as $k => $v) {

                if (is_int($k)) {
                    if (array_key_exists($k, $res)) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = static::merge($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    public static function removeValue(&$array, $value): array
    {
        $result = [];
        if (is_array($array)) {
            foreach ($array as $key => $val) {
                if ($val === $value) {
                    $result[$key] = $val;
                    unset($array[$key]);
                }
            }
        }

        return $result;
    }

    public static function index($array, $key, $groups = [])
    {
        $result = [];
        $groups = (array)$groups;

        foreach ($array as $element) {
            $lastArray = &$result;

            foreach ($groups as $group) {
                $value = static::getValue($element, $group);
                if (!array_key_exists($value, $lastArray)) {
                    $lastArray[$value] = [];
                }
                $lastArray = &$lastArray[$value];
            }

            if ($key === null) {
                if (!empty($groups)) {
                    $lastArray[] = $element;
                }
            } else {
                $value = static::getValue($element, $key);
                if ($value !== null) {
                    if (is_float($value)) {
                        $value = (string)$value;
                    }
                    $lastArray[$value] = $element;
                }
            }
            unset($lastArray);
        }

        return $result;
    }

    public static function getColumn($array, $name, $keepKeys = true): array
    {
        $result = [];
        if ($keepKeys) {
            foreach ($array as $k => $element) {
                $result[$k] = static::getValue($element, $name);
            }
        } else {
            foreach ($array as $element) {
                $result[] = static::getValue($element, $name);
            }
        }

        return $result;
    }

    public static function map($array, $from, $to, $group = null): array
    {
        $result = [];
        foreach ($array as $element) {
            $key = static::getValue($element, $from);
            $value = static::getValue($element, $to);
            if ($group !== null) {
                $result[static::getValue($element, $group)][$key] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public static function searchKey4ArrayValue(array $array, int|string $value): false|int|string
    {
        foreach ($array as $key => $ar) {
            if (array_search($value, $ar)) {
                return $key;
            }
        }

        return false;
    }

    public static function keyExists($key, $array, $caseSensitive = true): bool
    {
        if ($caseSensitive) {
            return isset($array[$key]) || array_key_exists($key, $array);
        }

        foreach (array_keys($array) as $k) {
            if (strcasecmp($key, $k) === 0) {
                return true;
            }
        }

        return false;
    }
}