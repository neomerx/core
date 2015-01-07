<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Events\EventArgs;
use \Neomerx\Core\Models\ProductTaxType;

class ProductTaxTypeArgs extends EventArgs
{
    /**
     * @var ProductTaxType
     */
    private $productTaxType;

    /**
     * @param string         $name
     * @param ProductTaxType $productTaxType
     * @param EventArgs      $args
     */
    public function __construct($name, ProductTaxType $productTaxType, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->productTaxType = $productTaxType;
    }

    /**
     * @return ProductTaxType
     */
    public function getModel()
    {
        return $this->productTaxType;
    }
}
