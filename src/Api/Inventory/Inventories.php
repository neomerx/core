<?php namespace Neomerx\Core\Api\Inventory;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Supplier;
use \Neomerx\Core\Models\Inventory;
use \Neomerx\Core\Models\Warehouse;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\SupplyOrder;
use \Neomerx\Core\Exceptions\LogicException;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Inventories implements InventoriesInterface
{
    const EVENT_PREFIX = 'Api.Inventories.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Inventory
     */
    private $inventoryModel;

    /**
     * @var Product
     */
    private $productModel;

    /**
     * @var Variant
     */
    private $variantModel;

    /**
     * @var Supplier
     */
    private $supplierModel;

    /**
     * @var Warehouse
     */
    private $warehouseModel;

    /**
     * @var SupplyOrder
     */
    private $supplyOrderModel;

    /**
     * @var array
     */
    protected static $relations = [
        'warehouse',
    ];

    /**
     * @param Inventory   $inventory
     * @param Product     $product
     * @param Variant     $variant
     * @param Supplier    $supplier
     * @param Warehouse   $warehouse
     * @param SupplyOrder $supplyOrder
     */
    public function __construct(
        Inventory $inventory,
        Product $product,
        Variant $variant,
        Supplier $supplier,
        Warehouse $warehouse,
        SupplyOrder $supplyOrder
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
            ->with(static::$relations)
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

        $inventoryRow = null;

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            $this->setIsolationSerializable();

            /** @var Inventory $inventoryRow */
            $inventoryRow = $this->inventoryModel->selectBySkuAndWarehouse($sku, $warehouseId)->first();

            if ($inventoryRow !== null) {
                $inventoryRow->incrementIn($quantity);
            } else {
                $inventoryData = [
                    Inventory::FIELD_SKU          => $sku,
                    Inventory::FIELD_ID_WAREHOUSE => $warehouseId,
                    Inventory::FIELD_IN           => $quantity,
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

        $inventoryRow = null;

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            $this->setIsolationSerializable();

            /** @var Inventory $inventoryRow */
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
     * @return Inventory
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
     * @param Inventory $inventoryRow
     * @param int       $quantity
     */
    private function incrementReserveInternal(Inventory $inventoryRow, $quantity)
    {
        $availableForReserve = (int)$inventoryRow->in - $inventoryRow->out - $inventoryRow->reserved;
        $availableForReserve >= $quantity ?: S\throwEx(new InvalidArgumentException('quantity'));

        $inventoryRow->incrementReserved($quantity);

        Event::fire(new InventoryArgs(self::EVENT_PREFIX . 'reserveIncreased', $inventoryRow));
    }

    /**
     * @param Inventory $inventoryRow
     * @param int       $quantity
     */
    private function decrementReserveInternal(Inventory $inventoryRow, $quantity)
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
