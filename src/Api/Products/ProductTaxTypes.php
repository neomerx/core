<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Auth\Facades\Permissions;

class ProductTaxTypes implements ProductTaxTypesInterface
{
    const EVENT_PREFIX = 'Api.ProductTaxType.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var ProductTaxType
     */
    private $productTaxType;

    /**
     * Constructor.
     *
     * @param ProductTaxType $productTaxType
     */
    public function __construct(ProductTaxType $productTaxType)
    {
        $this->productTaxType = $productTaxType;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var ProductTaxType $resource */
            $resource = $this->productTaxType->createOrFailResource($input);
            Permissions::check($resource, Permission::create());

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new ProductTaxTypeArgs(self::EVENT_PREFIX . 'created', $resource));

        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var ProductTaxType $resource */
        $resource = $this->productTaxType->selectByCode($code)->firstOrFail();
        Permissions::check($resource, Permission::view());
        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        /** @var ProductTaxType $resource */
        $resource = $this->productTaxType->selectByCode($code)->firstOrFail();
        Permissions::check($resource, Permission::edit());
        empty($input) ?: $resource->updateOrFail($input);

        Event::fire(new ProductTaxTypeArgs(self::EVENT_PREFIX . 'updated', $resource));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        /** @var ProductTaxType $resource */
        $resource = $this->productTaxType->selectByCode($code)->firstOrFail();
        Permissions::check($resource, Permission::delete());
        $resource->deleteOrFail();

        Event::fire(new ProductTaxTypeArgs(self::EVENT_PREFIX . 'deleted', $resource));
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $parameters = [])
    {
        $resources = $this->productTaxType->all();

        foreach ($resources as $resource) {
            Permissions::check($resource, Permission::view());
        }

        return $resources;
    }
}
