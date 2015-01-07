<?php namespace Neomerx\Core\Api\Inventory;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Warehouse;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Exceptions\LogicException;
use \Neomerx\Core\Models\Product as ProductModel;
use \Neomerx\Core\Models\Variant as VariantModel;
use \Neomerx\Core\Models\Supplier as SupplierModel;
use \Neomerx\Core\Models\Inventory as InventoryModel;
use \Neomerx\Core\Models\Warehouse as WarehouseModel;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\SupplyOrder as SupplyOrderModel;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Inventory implements InventoryInterface
{
    const EVENT_PREFIX = 'Api.Inventory.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var InventoryModel
     */
    private $inventoryModel;

    /**
     * @var ProductModel
     */
    private $productModel;

    /**
     * @var VariantModel
     */
    private $variantModel;

    /**
     * @var SupplierModel
     */
    private $supplierModel;

    /**
     * @var WarehouseModel
     */
    private $warehouseModel;

    /**
     * @var SupplyOrderModel
     */
    private $supplyOrderModel;

    /**
     * @var array
     */
    private static $relations = [
        'warehouse',
    ];

    /**
     * @param InventoryModel   $inventory
     * @param ProductModel     $product
     * @param VariantModel     $variant
     * @param SupplierModel    $supplier
     * @param WarehouseModel   $warehouse
     * @param SupplyOrderModel $supplyOrder
     */
    public function __construct(
        InventoryModel $inventory,
        ProductModel $product,
        VariantModel $variant,
        SupplierModel $supplier,
        WarehouseModel $warehouse,
        SupplyOrderModel $supplyOrder
    ) {
        $this->inventoryModel   = $inventory;
        $this->productModel     = $product;
        $this->variantModel     = $variant;
        $this->supplierModel    = $supplier;
        $this->warehouseModel   = $warehouse;
        $this->supplyOrderModel = $supplyOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function read(Variant $variant, Warehouse $warehouse)
    {
        return $this->inventoryModel
            ->selectBySkuAndWarehouse($variant->{Variant::FIELD_SKU}, $warehouse->{Warehouse::FIELD_ID})
            ->with(self::$relations)
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function increment(Variant $variant, Warehouse $warehouse, $quantity)
    {
        (is_int($quantity) or ctype_digit($quantity)) ?: S\throwEx(new InvalidArgumentException('quantity'));
        $quantity = (int)$quantity;
        $quantity > 0 ?: S\throwEx(new InvalidArgumentException('quantity'));

        $sku = $variant->{Variant::FIELD_SKU};
        $warehouseId = $warehouse->{Warehouse::FIELD_ID};

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            $this->setIsolationSerializable();

            /** @var InventoryModel $inventoryRow */
            $inventoryRow = $this->inventoryModel->selectBySkuAndWarehouse($sku, $warehouseId)->first();

            if ($inventoryRow !== null) {
                $inventoryRow->incrementIn($quantity);
            } else {
                $inventoryData = [
                    InventoryModel::FIELD_SKU          => $sku,
                    InventoryModel::FIELD_ID_WAREHOUSE => $warehouseId,
                    InventoryModel::FIELD_IN           => $quantity,
                ];
                $inventoryRow = $this->inventoryModel->createOrFailResource($inventoryData);
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new InventoryArgs(self::EVENT_PREFIX . 'itemIncreased', $inventoryRow));
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function decrement(Variant $variant, Warehouse $warehouse, $quantity, $includingReserve = false)
    {
        if (!(is_int($quantity) or ctype_digit($quantity)) or ($quantity = (int)$quantity) <= 0) {
            throw new InvalidArgumentException('quantity');
        }

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            $this->setIsolationSerializable();

            /** @var InventoryModel $inventoryRow */
            $inventoryRow = $this->inventoryModel->selectBySkuAndWarehouse(
                $variant->{Variant::FIELD_SKU},
                $warehouse->{Warehouse::FIELD_ID}
            )->firstOrFail();

            $outQty      = $inventoryRow->out;
            $reservedQty = $inventoryRow->reserved;
            $itemsLeft   = $inventoryRow->in - $outQty - ($includingReserve ? 0 : $reservedQty) - $quantity;

            $itemsLeft >= 0 ?:  S\throwEx(new InvalidArgumentException('quantity'));

            $inventoryRow->out = $outQty + $quantity;
            $includingReserve ? ($inventoryRow->reserved = $reservedQty - $quantity) : null;

            $inventoryRow->save() ?: S\throwEx(new LogicException());

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new InventoryArgs(self::EVENT_PREFIX . 'itemDecreased', $inventoryRow));
        !$includingReserve ?: Event::fire(new InventoryArgs(self::EVENT_PREFIX . 'reserveDecreased', $inventoryRow));
    }

    /**
     * {@inheritdoc}
     */
    public function makeReserve(Variant $variant, Warehouse $warehouse, $quantity)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            $this->setIsolationSerializable();

            $inventoryRow = $this->checkInputAndFindInventoryRow($variant, $warehouse, $quantity);
            $this->incrementReserveInternal($inventoryRow, $quantity);

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function releaseReserve(Variant $variant, Warehouse $warehouse, $quantity)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            $this->setIsolationSerializable();

            $inventoryRow = $this->checkInputAndFindInventoryRow($variant, $warehouse, $quantity);
            $this->decrementReserveInternal($inventoryRow, $quantity);

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }
    }

    /**
     * @param Variant   $item
     * @param Warehouse $warehouse
     * @param int       $quantity
     *
     * @return InventoryModel
     */
    private function checkInputAndFindInventoryRow(Variant $item, Warehouse $warehouse, $quantity)
    {
        (is_int($quantity) and $quantity > 0) ?: S\throwEx(new InvalidArgumentException('quantity'));

        /** @noinspection PhpUndefinedFieldInspection */
        return $this->inventoryModel
            ->selectBySkuAndWarehouse($item->sku, $warehouse->{Warehouse::FIELD_ID})
            ->firstOrFail();
    }

    /**
     * @param InventoryModel $inventoryRow
     * @param int            $quantity
     */
    private function incrementReserveInternal(InventoryModel $inventoryRow, $quantity)
    {
        $availableForReserve = (int)$inventoryRow->in - $inventoryRow->out - $inventoryRow->reserved;
        $availableForReserve >= $quantity ?: S\throwEx(new InvalidArgumentException('quantity'));

        $inventoryRow->incrementReserved($quantity);

        Event::fire(new InventoryArgs(self::EVENT_PREFIX . 'reserveIncreased', $inventoryRow));
    }

    /**
     * @param InventoryModel $inventoryRow
     * @param int            $quantity
     */
    private function decrementReserveInternal(InventoryModel $inventoryRow, $quantity)
    {
        $inventoryRow->reserved >= $quantity ?: S\throwEx(new InvalidArgumentException('quantity'));

        $inventoryRow->decrementReserved($quantity);

        Event::fire(new InventoryArgs(self::EVENT_PREFIX . 'reserveDecreased', $inventoryRow));
    }

    /**
     * Set session transaction isolation level as Serializable.
     */
    private function setIsolationSerializable()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $success =  DB::statement(DB::raw('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE'));
        $success ?: S\throwEx(new LogicException());
    }
}
