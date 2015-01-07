<?php namespace Neomerx\Core\Api\Features;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Models\Language as LanguageModel;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Models\CharacteristicValue as Model;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\Characteristic as CharacteristicModel;
use \Neomerx\Core\Models\CharacteristicValueProperties as PropertiesModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Values implements ValuesInterface
{
    use LanguagePropertiesTrait;

    const EVENT_PREFIX = 'Api.Feature.';

    /**
     * @var Model
     */
    private $model;

    /**
     * @var PropertiesModel
     */
    private $properties;

    /**
     * @var LanguageModel
     */
    private $language;

    /**
     * @var CharacteristicModel
     */
    private $characteristic;

    /**
     * @param Model               $model
     * @param PropertiesModel     $properties
     * @param LanguageModel       $language
     * @param CharacteristicModel $characteristic
     */
    public function __construct(
        Model $model,
        PropertiesModel $properties,
        LanguageModel $language,
        CharacteristicModel $characteristic
    ) {
        $this->model          = $model;
        $this->properties     = $properties;
        $this->language       = $language;
        $this->characteristic = $characteristic;
    }

    /**
     * @inheritdoc
     */
    public function all(CharacteristicModel $characteristic)
    {
        /** @noinspection PhpParamsInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $resources = $characteristic->values()->withProperties()->get();

        /** @var Model $resource */
        foreach ($resources as $resource) {
            Permissions::check($resource, Permission::view());
        }

        return $resources;
    }

    /**
     * Add values to characteristic.
     *
     * @param CharacteristicModel $characteristic
     * @param array          $values
     */
    public function addValues(CharacteristicModel $characteristic, array $values)
    {
        Permissions::check($characteristic, Permission::edit());

        $characteristicId = $characteristic->{CharacteristicModel::FIELD_ID};
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            foreach ($values as $value) {
                $value[Model::FIELD_ID_CHARACTERISTIC] = $characteristicId;
                $this->create($value);
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        list($input, $propertiesInput) = $this->extractPropertiesInput($this->language, $input);

        // check language properties are not empty
        count($propertiesInput) ? null : S\throwEx(new InvalidArgumentException('properties'));

        /** @var Model $characteristicValue */
        $characteristicValue = $this->model->createOrFailResource($input);

        Permissions::check($characteristicValue, Permission::create());

        $valueId = $characteristicValue->{Model::FIELD_ID};
        foreach ($propertiesInput as $languageId => $propertyInput) {
            $this->properties->createOrFailResource(array_merge([
                PropertiesModel::FIELD_ID_CHARACTERISTIC_VALUE => $valueId,
                PropertiesModel::FIELD_ID_LANGUAGE             => $languageId
            ], $propertyInput));
        }

        Event::fire(new FeatureValueArgs(self::EVENT_PREFIX . 'createdValue', $characteristicValue));
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var Model $resource */
        /** @noinspection PhpParamsInspection */
        $resource = $this->model->selectByCode($code)->withProperties()->firstOrFail();
        Permissions::check($resource, Permission::view());
        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        // get input for properties
        list($input, $propertiesInput) = $this->extractPropertiesInput($this->language, $input);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {
            // update resource
            /** @var Model $characteristicValue */
            $characteristicValue = $this->model->selectByCode($code)->firstOrFail();

            Permissions::check($characteristicValue, Permission::edit());

            empty($input) ?: $characteristicValue->updateOrFail($input);

            // update language properties
            $valueId = $characteristicValue->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $property = $this->properties->updateOrCreate([
                    PropertiesModel::FIELD_ID_CHARACTERISTIC_VALUE => $valueId,
                    PropertiesModel::FIELD_ID_LANGUAGE             => $languageId
                    // TODO Swap [] and $propertyInput order. Otherwise relation IDs could be overwritten. Everywhere.
                ], $propertyInput);
                /** @noinspection PhpUndefinedMethodInspection */
                $property->exists ?: S\throwEx(new ValidationException($property->getValidator()));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new FeatureValueArgs(self::EVENT_PREFIX . 'updatedValue', $characteristicValue));

        return $characteristicValue;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        /** @var Model $characteristicValue */
        $characteristicValue = $this->model->selectByCode($code)->firstOrFail();

        Permissions::check($characteristicValue, Permission::delete());

        $characteristicValue->deleteOrFail();

        Event::fire(new FeatureValueArgs(self::EVENT_PREFIX . 'deletedValue', $characteristicValue));
    }
}
