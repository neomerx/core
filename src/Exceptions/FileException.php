<?php namespace Neomerx\Core\Exceptions;

use \Neomerx\Core\Support\Translate as T;

class FileException extends Exception
{
    /**
     * {@inheritDoc}
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($this->loadIfEmpty($message, T::KEY_EX_FILE_EXCEPTION), $code, $previous);
    }
}
