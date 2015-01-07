<?php namespace Neomerx\Core\Api\Facades;

use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Api\Products\ProductTaxTypesInterface;

/**
 * @see ProductTaxTypesInterface
 *
 * @method static void  create(array $input)
 * @method static array read(string $code)
 * @method static void  update(string $code, array $input)
 * @method static void  delete(string $code)
 * @method static array all()
 */
class ProductTaxTypes extends Facade
{
    const INTERFACE_BIND_NAME = ProductTaxTypesInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
