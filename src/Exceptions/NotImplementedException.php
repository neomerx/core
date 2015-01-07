<?php namespace Neomerx\Core\Exceptions;

class NotImplementedException extends Exception
{
    /**
     * {@inheritDoc}
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($this->loadIfEmpty($message, 'not_implemented_exception'), $code, $previous);
    }
}
