<?php namespace Neomerx\Core\Api\Addresses;

use \Log;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Address;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;

class Addresses implements AddressesInterface
{
    const EVENT_PREFIX = 'Api.Address.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Address
     */
    private $addressModel;

    /**
     * @var Region
     */
    private $regionModel;

    /**
     * @param Address $addressModel
     * @param Region  $regionModel
     */
    public function __construct(Address $addressModel, Region $regionModel)
    {
        $this->addressModel = $addressModel;
        $this->regionModel  = $regionModel;
    }

    /**
     * @inheritdoc
     */
    public function create(array $input)
    {
        $input = $this->replaceRegionCodeWithId($input);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var Address $address */
            $address = $this->addressModel->createOrFailResource($input);
            Permissions::check($address, Permission::create());

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new AddressArgs(self::EVENT_PREFIX . 'created', $address));

        return $address;
    }

    /**
     * @inheritdoc
     */
    public function read($addressId)
    {
        /** @var Address $resource */
        /** @noinspection PhpUndefinedMethodInspection */
        $resource = $this->addressModel->newQuery()->withRegionAndCountry()->findOrFail($addressId);
        Permissions::check($resource, Permission::view());
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function update($addressId, array $input)
    {
        /** @var Address $address */
        $address = $this->addressModel->findOrFail($addressId);
        $this->updateModel($address, $input);
    }

    /**
     * @inheritdoc
     */
    public function updateModel(Address $address, array $input)
    {
        Permissions::check($address, Permission::edit());
        $address->updateOrFail($this->replaceRegionCodeWithId($input));
        Event::fire(new AddressArgs(self::EVENT_PREFIX . 'updated', $address));
    }

    /**
     * @inheritdoc
     */
    public function delete($addressId)
    {
        /** @var Address $address */
        $address = $this->addressModel->newQuery()->findOrFail($addressId);
        $this->deleteModel($address);
    }

    /**
     * @inheritdoc
     */
    public function deleteModel(Address $address)
    {
        Permissions::check($address, Permission::delete());
        $address->deleteOrFail();
        Event::fire(new AddressArgs(self::EVENT_PREFIX . 'deleted', $address));
    }

    private function replaceRegionCodeWithId(array $input)
    {
        // unset ID if someone decided to bypass check providing ID instead of code
        if (isset($input[Address::FIELD_ID_REGION])) {
            unset($input[Address::FIELD_ID_REGION]);
            // TODO move string to resources and add similar checks + logging to app
            Log::warning('Security bypass attempt occurred. @' . __FILE__ . '#' . __LINE__);
        }

        if (isset($input[self::PARAM_REGION_CODE])) {

            /** @var Region $region */
            $region = $this->regionModel
                ->selectByCode($input[self::PARAM_REGION_CODE])
                ->firstOrFail([Region::FIELD_ID]);
            unset($input[self::PARAM_REGION_CODE]);

            Permissions::check($region, Permission::view());

            $input[Address::FIELD_ID_REGION] = $region->{Region::FIELD_ID};
        }
        return $input;
    }
}
