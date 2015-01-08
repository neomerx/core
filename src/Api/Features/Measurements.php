<?php namespace Neomerx\Core\Api\Features;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Language;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\Measurement;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\MeasurementProperties;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Measurements implements MeasurementsInterface
{
    use LanguagePropertiesTrait;

    const EVENT_PREFIX = 'Api.Measurement.';
    const BIND_NAME = __CLASS__;

    /**
     * @var Measurement
     */
    private $measurement;

    /**
     * @var MeasurementProperties
     */
    private $properties;

    /**
     * @var Language
     */
    private $language;

    /**
     * @param Measurement           $measurement
     * @param MeasurementProperties $properties
     * @param Language              $language
     */
    public function __construct(
        Measurement $measurement,
        MeasurementProperties $properties,
        Language $language
    ) {
        $this->measurement      = $measurement;
        $this->properties = $properties;
        $this->language   = $language;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        list($input, $propertiesInput) = $this->extractPropertiesInput($this->language, $input);

        // check language properties are not empty
        count($propertiesInput) ? null : S\throwEx(new InvalidArgumentException('properties'));

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var \Neomerx\Core\Models\Measurement $measurement */
            $measurement = $this->measurement->createOrFailResource($input);
            Permissions::check($measurement, Permission::create());

            $measurementId = $measurement->{Measurement::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->properties->createOrFail(array_merge(
                    [Measurement::FIELD_ID => $measurementId, Language::FIELD_ID => $languageId],
                    $propertyInput
                ));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new MeasurementArgs(self::EVENT_PREFIX . 'created', $measurement));

        return $measurement;
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var \Neomerx\Core\Models\Measurement $measurement */
        $measurement = $this->measurement->selectByCode($code)->withProperties()->firstOrFail();
        Permissions::check($measurement, Permission::view());
        return $measurement;
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
            /** @var \Neomerx\Core\Models\Measurement $measurement */
            $measurement = $this->measurement->selectByCode($code)->firstOrFail();
            Permissions::check($measurement, Permission::edit());
            empty($input) ?: $measurement->updateOrFail($input);

            // update language properties
            $measurementId = $measurement->{Measurement::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $property = $this->properties->updateOrCreate(
                    [Measurement::FIELD_ID => $measurementId, Language::FIELD_ID => $languageId],
                    $propertyInput
                );
                /** @noinspection PhpUndefinedMethodInspection */
                $property->exists ?: S\throwEx(new ValidationException($property->getValidator()));
            }

            $allExecutedOk = true;
        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new MeasurementArgs(self::EVENT_PREFIX . 'updated', $measurement));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        /** @var \Neomerx\Core\Models\Measurement $measurement */
        $measurement = $this->measurement->selectByCode($code)->firstOrFail();

        Permissions::check($measurement, Permission::delete());

        $measurement->deleteOrFail();

        Event::fire(new MeasurementArgs(self::EVENT_PREFIX . 'deleted', $measurement));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $measurements = $this->measurement->newQuery()->withProperties()->get();

        foreach ($measurements as $resource) {
            /** @var \Neomerx\Core\Models\Measurement $resource */
            Permissions::check($resource, Permission::view());
        }

        return $measurements;
    }
}
