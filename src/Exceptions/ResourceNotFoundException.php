<?php namespace Neomerx\Core\Exceptions;

class ResourceNotFoundException extends InvalidArgumentException
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string     $name
     * @param mixed      $value
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($name, $value, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($name, $this->loadIfEmpty($message, 'resource_not_found_exception'), $code, $previous);
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
