<?php namespace Neomerx\Core\Api\Orders;

use \Neomerx\Core\Events\EventArgs;
use \Neomerx\Core\Models\Order as Model;

class OrderArgs extends EventArgs
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @param string    $name
     * @param Model     $model
     * @param EventArgs $args
     */
    public function __construct($name, Model $model, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->model = $model;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }
}
