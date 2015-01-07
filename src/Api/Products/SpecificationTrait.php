<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\BaseModel;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Exceptions\NullArgumentException;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\Characteristic as CharacteristicModel;
use \Neomerx\Core\Models\CharacteristicValue as CharacteristicValueModel;

trait SpecificationTrait
{
    public static $relations = [
        'value.properties.language',
        'value.characteristic.properties.language',
        'value.characteristic.measurement.properties.language',
    ];

    /**
     * @param Product $product
     * @param string  $positionOrderBy
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function readProductSpecification(Product $product, $positionOrderBy = 'asc')
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $product->specification()->with(self::$relations)->orderBy('position', $positionOrderBy)->get();
    }

    /**
     * @param CharacteristicValueModel $chValueModel
     * @param array                    $parameters
     * @param mixed                    $model        Product or Variant
     */
    private function updateSpecification(CharacteristicValueModel $chValueModel, array $parameters, BaseModel $model)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            foreach ($parameters as $valueCodesPair) {

                list($oldValue, $newValue) = $this->readAndCheckValues($chValueModel, $valueCodesPair);

                // update
                /** @noinspection PhpUndefinedMethodInspection */
                $spec = $model->specification()
                    ->where(CharacteristicValueModel::FIELD_ID, '=', $oldValue->{CharacteristicValueModel::FIELD_ID})
                    ->firstOrFail();
                /** @noinspection PhpUndefinedMethodInspection */
                $spec->updateOrFail([
                    CharacteristicValueModel::FIELD_ID => $newValue->{CharacteristicValueModel::FIELD_ID}
                ]);

                Event::fire(new SpecificationArgs(Products::EVENT_PREFIX . 'updatedSpecification', $spec));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }
    }

    /**
     * @param CharacteristicValueModel $chValueModel
     * @param array                    $valueCodesPair
     *
     * @return array
     */
    private function readAndCheckValues(CharacteristicValueModel $chValueModel, array $valueCodesPair)
    {
        isset($valueCodesPair['oldValueCode']) ? : S\throwEx(new NullArgumentException('oldValueCode'));
        isset($valueCodesPair['newValueCode']) ? : S\throwEx(new NullArgumentException('newValueCode'));

        $oldValueCode = $valueCodesPair['oldValueCode'];
        $newValueCode = $valueCodesPair['newValueCode'];

        $oldValue = $chValueModel->selectByCode($oldValueCode)->firstOrFail();
        $newValue = $chValueModel->selectByCode($newValueCode)->firstOrFail();

        // check both values belong to the same characteristic
        $sameParent =  $oldValue->{CharacteristicModel::FIELD_ID} === $newValue->{CharacteristicModel::FIELD_ID};
        $sameParent ?: S\throwEx(new InvalidArgumentException($newValueCode));

        return [$oldValue, $newValue];
    }
}
