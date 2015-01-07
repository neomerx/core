<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Events\EventArgs;
use \Neomerx\Core\Models\BaseModel as Model;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class ProductArgs extends EventArgs
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @param string    $name
     * @param Model     $model
     * @param EventArgs $args
     *
     * @throws InvalidArgumentException
     */
    public function __construct($name, Model $model, EventArgs $args = null)
    {
        parent::__construct($name, $args);

        $isProductOrVariant =  (($model instanceof Product) or ($model instanceof Variant));
        $isProductOrVariant ?: S\throwEx(new InvalidArgumentException('model'));

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
