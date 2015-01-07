<?php namespace Neomerx\Core\Api\Images;

use \Neomerx\Core\Events\EventArgs;
use \Neomerx\Core\Models\ImageFormat as Model;

class ImageFormatArgs extends EventArgs
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
