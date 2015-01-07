<?php namespace Neomerx\Core\Exceptions;

class NullArgumentException extends InvalidArgumentException
{
    /**
     * {@inheritDoc}
     */
    public function __construct($name, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($name, $this->loadIfEmpty($message, 'null_argument_exception'), $code, $previous);
    }
}
