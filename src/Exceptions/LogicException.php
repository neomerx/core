<?php namespace Neomerx\Core\Exceptions;

class LogicException extends Exception
{
    /**
     * {@inheritDoc}
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($this->loadIfEmpty($message, 'logic_exception'), $code, $previous);
    }
}
