<?php namespace Neomerx\Core\Exceptions;

use \Neomerx\Core\Support\Translate;
use \Neomerx\Core\Support\Translate as T;

/**
 * @package Neomerx\Core
 */
class Exception extends \Exception
{
    /**
     * @inheritdoc
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($this->loadIfEmpty($message, T::KEY_EX_EXCEPTION), $code, $previous);
    }

    /**
     * Loads exception internationalized message for $key if $message is empty.
     *
     * @param string $message
     * @param string $key
     *
     * @return string
     */
    protected function loadIfEmpty($message, $key)
    {
        return empty($message) ? Translate::trans($key) : $message;
    }
}
