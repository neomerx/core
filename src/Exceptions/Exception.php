<?php namespace Neomerx\Core\Exceptions;

use \Neomerx\Core\Support\Translate;
use \Neomerx\Core\Support\Translate as T;

class Exception extends \Exception
{
    /**
     * {@inheritDoc}
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
