<?php namespace Neomerx\Core\Exceptions;

class Exception extends \Exception
{
    /**
     * {@inheritDoc}
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($this->loadIfEmpty($message, 'exception'), $code, $previous);
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
        $message = ($message === null or $message === '') ? trans('nm::exceptions.' . $key) : $message;
        return $message;
    }
}
