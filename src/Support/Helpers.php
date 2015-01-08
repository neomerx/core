<?php

namespace Neomerx\Core\Support {

    use \Neomerx\Core\Exceptions\InvalidArgumentException;

    function throwEx(\Exception $exception)
    {
        throw $exception;
    }

    function array_get_value(array $array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    function array_get_value_1D(array $array, $key, $default = null)
    {
        return array_get_value($array, $key, $default);
    }

    function array_get_value_2D(array $array, $key1, $key2, $default = null)
    {
        return isset($array[$key1][$key2]) ? $array[$key1][$key2] : $default;
    }

    function array_get_value_ex(array $array, $key)
    {
        isset($array[$key]) ?: throwEx(new InvalidArgumentException($key));
        return $array[$key];
    }

    function array_get_value_1D_ex(array $array, $key)
    {
        return array_get_value_ex($array, $key);
    }

    function array_get_value_2D_ex(array $array, $key1, $key2)
    {
        isset($array[$key1][$key2]) ?: throwEx(new InvalidArgumentException($key2));
        return $array[$key1][$key2];
    }

    function array_filter_nulls(array $array)
    {
        $result = [];
        foreach ($array as $key => $item) {
            $item === null ?: $result[$key] = $item;
        }
        return $result;
    }

    /**
     * Convert class name with namespace to a form suitable for storing in database enum.
     *
     * @param string $className
     *
     * @return string
     */
    function name_to_db_enum($className)
    {
        return str_replace('\\', '\\\\', $className);
    }
}
