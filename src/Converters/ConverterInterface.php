<?php namespace Neomerx\Core\Converters;

interface ConverterInterface
{
    /**
     * Format model to array representation.
     *
     * @param mixed $object
     *
     * @return array<*,string|boolean|double|integer|null|array>
     */
    public function convert($object = null);
}
