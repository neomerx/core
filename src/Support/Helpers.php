<?php

namespace Neomerx\Core\Support {

    use \Neomerx\Core\Exceptions\InvalidArgumentException;

    /**
     * @param \Exception $exception
     *
     * @throws \Exception
     */
    function throwEx(\Exception $exception)
    {
        throw $exception;
    }

    /**
     * @param array      $array
     * @param mixed      $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    function arrayGetValue(array $array, $key, $default = null)
    {
        return isset($array[$key]) === true ? $array[$key] : $default;
    }

    /**
     * @param array      $array
     * @param mixed      $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    function arrayGetValue1d(array $array, $key, $default = null)
    {
        return arrayGetValue($array, $key, $default);
    }

    /**
     * @param array      $array
     * @param mixed      $key1
     * @param mixed      $key2
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    function arrayGetValue2d(array $array, $key1, $key2, $default = null)
    {
        return isset($array[$key1][$key2]) === true ? $array[$key1][$key2] : $default;
    }

    /**
     * @param array $array
     * @param mixed $key
     *
     * @return mixed|null
     */
    function arrayGetValueEx(array $array, $key)
    {
        isset($array[$key]) === true ?: throwEx(new InvalidArgumentException($key));
        return $array[$key];
    }

    /**
     * @param array $array
     * @param mixed $key
     *
     * @return mixed|null
     */
    function arrayGetValue1dEx(array $array, $key)
    {
        return arrayGetValueEx($array, $key);
    }

    /**
     * @param array $array
     * @param mixed $key1
     * @param mixed $key2
     *
     * @return mixed|null
     */
    function arrayGetValue2dEx(array $array, $key1, $key2)
    {
        isset($array[$key1][$key2]) === true ?: throwEx(new InvalidArgumentException($key2));
        return $array[$key1][$key2];
    }

    /**
     * @param array $array
     *
     * @return array
     */
    function arrayFilterNulls(array $array)
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
    function nameToDbEnum($className)
    {
        return str_replace('\\', '\\\\', $className);
    }
}
