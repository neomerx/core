<?php namespace Neomerx\Core\Exceptions;

class FileException extends Exception
{
    /**
     * {@inheritDoc}
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($this->loadIfEmpty($message, 'file_exception'), $code, $previous);
    }
}
