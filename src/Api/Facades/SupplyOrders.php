<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\SupplyOrder;
use \Illuminate\Support\Facades\Facade;
use \Neomerx\Core\Models\SupplyOrderDetails;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\SupplyOrders\SupplyOrdersInterface;

/**
 * @see SupplyOrdersInterface
 *
 * @method static SupplyOrder        create(array $input)
 * @method static SupplyOrder        read(int $id)
 * @method static void               update(int $id, array $input)
 * @method static void               delete(int $id)
 * @method static SupplyOrderDetails createDetails(SupplyOrder $supplyOrder, array $input)
 * @method static SupplyOrderDetails readDetails(int $detailsId)
 * @method static void               updateDetails(SupplyOrderDetails $detailsRow, array $input)
 * @method static void               deleteDetails(int $detailsId)
 * @method static Collection         search(array $parameters = [])
 */
class SupplyOrders extends Facade
{
    const INTERFACE_BIND_NAME = SupplyOrdersInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
