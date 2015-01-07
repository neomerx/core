<?php

namespace Neomerx\Core\Support {

    function throwEx(\Exception $exception)
    {
        throw $exception;
    }

    function array_get_value(array $array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
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
