<?php namespace Neomerx\Core\Exceptions;

use \Neomerx\Core\Support\Translate as T;

/**
 * @package Neomerx\Core
 */
class InvalidArgumentException extends LogicException
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string $name
     * @inheritdoc
     */
    public function __construct($name, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($this->loadIfEmpty($message, T::KEY_EX_INVALID_ARGUMENT_EXCEPTION), $code, $previous);
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
