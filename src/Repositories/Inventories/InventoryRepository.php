<?php namespace Neomerx\Core\Repositories\Inventories;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Inventory;
use \Neomerx\Core\Models\Warehouse;
use \Illuminate\Database\Eloquent\Builder;
use \Neomerx\Core\Repositories\BaseRepository;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @package Neomerx\Core
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class InventoryRepository extends BaseRepository implements InventoryRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Inventory::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Product $product, Warehouse $warehouse, array $attributes)
    {
        return $this->create($this->idOf($product), $this->idOf($warehouse), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($productId, $warehouseId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($productId, $warehouseId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        Inventory $resource,
        Product $product = null,
        Warehouse $warehouse = null,
        array $attributes = null
    ) {
        $this->update($resource, $this->idOf($product), $this->idOf($warehouse), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(
        Inventory $resource,
        $productId = null,
        $warehouseId = null,
        array $attributes = null
    ) {
        $this->update($resource, $attributes, $this->getRelationships($productId, $warehouseId));
    }

    /**
     * @inheritdoc
     */
    public function incrementProduct($productId, $warehouseId, $quantity)
    {
        $this->checkQuantity($quantity);

        $inventoryHasChanged = $this->incrementColumn(Inventory::FIELD_IN, $productId, $warehouseId, $quantity) > 0;
        if ($inventoryHasChanged === false) {
            $this->create($productId, $warehouseId, [
                Inventory::FIELD_IN => $quantity,
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function decrementProduct($productId, $warehouseId, $quantity)
    {
        $this->checkQuantity($quantity);

        $inventoryHasChanged = $this->incrementColumn(Inventory::FIELD_OUT, $productId, $warehouseId, $quantity) > 0;
        $inventoryHasChanged === true ?: $this->throwNoSuchProductOrWarehouse($productId, $warehouseId);
    }

    /**
     * @inheritdoc
     */
    public function incrementReserve($productId, $warehouseId, $quantity)
    {
        $this->checkQuantity($quantity);

        $inventoryHasChanged =
            $this->incrementColumn(Inventory::FIELD_RESERVED_IN, $productId, $warehouseId, $quantity) > 0;

        $inventoryHasChanged === true ?: $this->throwNoSuchProductOrWarehouse($productId, $warehouseId);
    }

    /**
     * @inheritdoc
     */
    public function decrementReserve($productId, $warehouseId, $quantity)
    {
        $this->checkQuantity($quantity);

        $inventoryHasChanged =
            $this->incrementColumn(Inventory::FIELD_RESERVED_OUT, $productId, $warehouseId, $quantity) > 0;

        $inventoryHasChanged === true ?: $this->throwNoSuchProductOrWarehouse($productId, $warehouseId);
    }

    /**
     * @inheritdoc
     */
    public function moveProduct($productId, $warehouseFromId, $warehouseToId, $quantity)
    {
        $this->executeInTransaction(function () use ($productId, $warehouseFromId, $warehouseToId, $quantity) {
            $this->decrementProduct($productId, $warehouseFromId, $quantity);
            $this->incrementProduct($productId, $warehouseToId, $quantity);
        });
    }

    /**
     * @inheritdoc
     */
    public function selectInventory(
        $productId = null,
        $warehouseId = null,
        array $columns = ['*'],
        array $relations = []
    ) {
        $query = $this->createSelectQuery($productId, $warehouseId);
        empty($relations) === true ?: $query->with($relations);
        $result = $query->get($columns)->all();

        return $result;
    }

    /**
     * @param int $productId
     * @param int $warehouseId
     *
     * @return array
     */
    protected function getRelationships($productId, $warehouseId)
    {
        return $this->filterNulls([
            Inventory::FIELD_ID_PRODUCT   => $productId,
            Inventory::FIELD_ID_WAREHOUSE => $warehouseId,
        ]);
    }

    /**
     * @param string $column
     * @param int    $productId
     * @param int    $warehouseId
     * @param int    $quantity
     *
     * @return int
     */
    protected function incrementColumn($column, $productId, $warehouseId, $quantity)
    {
        $query = $this->getUnderlyingModel()->newQuery()
            ->where(Inventory::FIELD_ID_PRODUCT, $productId)
            ->where(Inventory::FIELD_ID_WAREHOUSE, $warehouseId);

        $result = $query->increment($column, $quantity);

        return $result;
    }

    /**
     * @param int|null $productId
     * @param int|null $warehouseId
     *
     * @return Builder
     */
    private function createSelectQuery($productId = null, $warehouseId = null)
    {
        $query = $this->getUnderlyingModel()->newQuery();

        $productId === null ?: $query->where(Inventory::FIELD_ID_PRODUCT, $productId);
        $warehouseId === null ?: $query->where(Inventory::FIELD_ID_WAREHOUSE, $warehouseId);

        return $query;
    }

    /**
     * @param int $quantity
     *
     * @throws InvalidArgumentException
     */
    private function checkQuantity($quantity)
    {
        if (is_int($quantity) === false || $quantity <= 0) {
            throw new InvalidArgumentException('quantity');
        }
    }

    /**
     * @param int $productId
     * @param int $warehouseId
     *
     * @throws InvalidArgumentException
     */
    private function throwNoSuchProductOrWarehouse($productId, $warehouseId)
    {
        $productId ?: null;
        $warehouseId ?: null;

        throw new InvalidArgumentException('');
    }
}
