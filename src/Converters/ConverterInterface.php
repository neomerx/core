<?php namespace Neomerx\Core\Converters;

interface ConverterInterface
{
    /**
     * Format model to array representation.
     *
     * @param mixed $object
     *
     * @return array<mixed,mixed>
     */
    public function convert($object = null);
}
