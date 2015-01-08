<?php namespace Neomerx\Core\Api\Features;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Language;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\Characteristic;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\CharacteristicValue;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\CharacteristicValueProperties;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Values implements ValuesInterface
{
    use LanguagePropertiesTrait;

    const EVENT_PREFIX = 'Api.Feature.';

    /**
     * @var CharacteristicValue
     */
    private $characteristicValue;

    /**
     * @var CharacteristicValueProperties
     */
    private $properties;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var Characteristic
     */
    private $characteristic;

    /**
     * @param CharacteristicValue           $characteristicValue
     * @param CharacteristicValueProperties $properties
     * @param Language                      $language
     * @param Characteristic                $characteristic
     */
    public function __construct(
        CharacteristicValue $characteristicValue,
        CharacteristicValueProperties $properties,
        Language $language,
        Characteristic $characteristic
    ) {
        $this->characteristicValue = $characteristicValue;
        $this->properties          = $properties;
        $this->language            = $language;
        $this->characteristic      = $characteristic;
    }

    /**
     * @inheritdoc
     */
    public function all(Characteristic $characteristic)
    {
        /** @noinspection PhpParamsInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $resources = $characteristic->values()->withProperties()->get();

        /** @var \Neomerx\Core\Models\CharacteristicValue $resource */
        foreach ($resources as $resource) {
            Permissions::check($resource, Permission::view());
        }

        return $resources;
    }

    /**
     * Add values to characteristic.
     *
     * @param Characteristic $characteristic
     * @param array          $values
     */
    public function addValues(Characteristic $characteristic, array $values)
    {
        Permissions::check($characteristic, Permission::edit());

        $characteristicId = $characteristic->{Characteristic::FIELD_ID};
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            foreach ($values as $value) {
                $value[CharacteristicValue::FIELD_ID_CHARACTERISTIC] = $characteristicId;
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

        /** @var \Neomerx\Core\Models\CharacteristicValue $characteristicValue */
        $characteristicValue = $this->characteristicValue->createOrFailResource($input);

        Permissions::check($characteristicValue, Permission::create());

        $valueId = $characteristicValue->{CharacteristicValue::FIELD_ID};
        foreach ($propertiesInput as $languageId => $propertyInput) {
            $this->properties->createOrFailResource(array_merge([
                CharacteristicValueProperties::FIELD_ID_CHARACTERISTIC_VALUE => $valueId,
                CharacteristicValueProperties::FIELD_ID_LANGUAGE             => $languageId
            ], $propertyInput));
        }

        Event::fire(new FeatureValueArgs(self::EVENT_PREFIX . 'createdValue', $characteristicValue));
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var \Neomerx\Core\Models\CharacteristicValue $resource */
        /** @noinspection PhpParamsInspection */
        $resource = $this->characteristicValue->selectByCode($code)->withProperties()->firstOrFail();
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
            /** @var \Neomerx\Core\Models\CharacteristicValue $characteristicValue */
            $characteristicValue = $this->characteristicValue->selectByCode($code)->firstOrFail();

            Permissions::check($characteristicValue, Permission::edit());

            empty($input) ?: $characteristicValue->updateOrFail($input);

            // update language properties
            $valueId = $characteristicValue->{CharacteristicValue::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $property = $this->properties->updateOrCreate([
                    CharacteristicValueProperties::FIELD_ID_CHARACTERISTIC_VALUE => $valueId,
                    CharacteristicValueProperties::FIELD_ID_LANGUAGE             => $languageId
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
        /** @var \Neomerx\Core\Models\CharacteristicValue $characteristicValue */
        $characteristicValue = $this->characteristicValue->selectByCode($code)->firstOrFail();

        Permissions::check($characteristicValue, Permission::delete());

        $characteristicValue->deleteOrFail();

        Event::fire(new FeatureValueArgs(self::EVENT_PREFIX . 'deletedValue', $characteristicValue));
    }
}
