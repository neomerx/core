<?php namespace Neomerx\Core\Api\Customers;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Auth\Facades\Permissions;

class CustomerTypes implements CustomerTypesInterface
{
    const EVENT_PREFIX = 'Api.CustomerType.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var CustomerType
     */
    private $customerType;

    /**
     * Constructor.
     *
     * @param CustomerType $customerType
     */
    public function __construct(CustomerType $customerType)
    {
        $this->customerType = $customerType;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var \Neomerx\Core\Models\CustomerType $resource */
            $resource = $this->customerType->createOrFailResource($input);
            Permissions::check($resource, Permission::create());

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new CustomerTypeArgs(self::EVENT_PREFIX . 'created', $resource));

        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var \Neomerx\Core\Models\CustomerType $resource */
        $resource = $this->customerType->selectByCode($code)->firstOrFail();
        Permissions::check($resource, Permission::view());
        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        /** @var \Neomerx\Core\Models\CustomerType $resource */
        $resource = $this->customerType->selectByCode($code)->firstOrFail();
        Permissions::check($resource, Permission::edit());
        empty($input) ?: $resource->updateOrFail($input);

        Event::fire(new CustomerTypeArgs(self::EVENT_PREFIX . 'updated', $resource));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        /** @var \Neomerx\Core\Models\CustomerType $resource */
        $resource = $this->customerType->selectByCode($code)->firstOrFail();
        Permissions::check($resource, Permission::delete());
        $resource->deleteOrFail();

        Event::fire(new CustomerTypeArgs(self::EVENT_PREFIX . 'deleted', $resource));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $resources = $this->customerType->all();

        foreach ($resources as $resource) {
            Permissions::check($resource, Permission::view());
        }

        return $resources;
    }
}
