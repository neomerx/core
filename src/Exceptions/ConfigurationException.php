<?php namespace Neomerx\Core\Exceptions;

class ConfigurationException extends LogicException
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string $name
     * {@inheritDoc}
     */
    public function __construct($name, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($this->loadIfEmpty($message, 'configuration_exception'), $code, $previous);
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
