<?php namespace Neomerx\Core\Events;

use \Neomerx\Core\Models\BaseModel;

abstract class EventArgs
{
    /**
     * Event name.
     *
     * @var string
     */
    private $name;

    /**
     * @var EventArgs
     */
    private $innerEventArgs;

    /**
     * @param string    $name Event name.
     * @param EventArgs $args
     */
    public function __construct($name, EventArgs $args = null)
    {
        $this->name = $name;
        $this->innerEventArgs = $args;
    }

    /**
     * Get event name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get inner event arguments.
     *
     * @return EventArgs
     */
    public function getInnerArgs()
    {
        return $this->innerEventArgs;
    }

    /**
     * Get event model instance.
     *
     * @return BaseModel
     */
    abstract public function getModel();
}
