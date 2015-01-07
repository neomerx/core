<?php namespace Neomerx\Core\Api\Addresses;

use \Log;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\Region as RegionModel;
use \Neomerx\Core\Models\Address as AddressModel;

class Addresses implements AddressesInterface
{
    const EVENT_PREFIX = 'Api.Address.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var AddressModel
     */
    private $addressModel;

    /**
     * @var RegionModel
     */
    private $regionModel;

    /**
     * @param AddressModel $addressModel
     * @param RegionModel  $regionModel
     */
    public function __construct(AddressModel $addressModel, RegionModel $regionModel)
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

            /** @var AddressModel $address */
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
        /** @var AddressModel $resource */
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
        /** @var AddressModel $address */
        $address = $this->addressModel->findOrFail($addressId);
        $this->updateModel($address, $input);
    }

    /**
     * @inheritdoc
     */
    public function updateModel(AddressModel $address, array $input)
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
        /** @var AddressModel $address */
        $address = $this->addressModel->newQuery()->findOrFail($addressId);
        $this->deleteModel($address);
    }

    /**
     * @inheritdoc
     */
    public function deleteModel(AddressModel $address)
    {
        Permissions::check($address, Permission::delete());
        $address->deleteOrFail();
        Event::fire(new AddressArgs(self::EVENT_PREFIX . 'deleted', $address));
    }

    private function replaceRegionCodeWithId(array $input)
    {
        // unset ID if someone decided to bypass check providing ID instead of code
        if (isset($input[AddressModel::FIELD_ID_REGION])) {
            unset($input[AddressModel::FIELD_ID_REGION]);
            // TODO move string to resources and add similar checks + logging to app
            Log::warning('Security bypass attempt occurred. @' . __FILE__ . '#' . __LINE__);
        }

        if (isset($input[self::PARAM_REGION_CODE])) {

            /** @var RegionModel $region */
            $region = $this->regionModel
                ->selectByCode($input[self::PARAM_REGION_CODE])
                ->firstOrFail([RegionModel::FIELD_ID]);
            unset($input[self::PARAM_REGION_CODE]);

            Permissions::check($region, Permission::view());

            $input[AddressModel::FIELD_ID_REGION] = $region->{RegionModel::FIELD_ID};
        }
        return $input;
    }
}
