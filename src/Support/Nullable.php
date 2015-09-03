<?php namespace Neomerx\Core\Support;

/**
 * @package Neomerx\Core
 */
class Nullable
{
    /**
     * @var mixed
     */
    public $value;

    /**
     * Constructor.
     *
     * @param mixed       $value
     * @param string|null $assertClass
     */
    public function __construct($value, $assertClass = null)
    {
        // suppress 'unused' warning
        $assertClass ?: null;
        assert('$assertClass === null || get_class($value) === $assertClass');

        $this->value = $value;
    }
}
