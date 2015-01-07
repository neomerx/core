<?php namespace Neomerx\Core\Api\Features;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\Measurement as Model;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Models\Language as LanguageModel;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\MeasurementProperties as PropertiesModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Measurements implements MeasurementsInterface
{
    use LanguagePropertiesTrait;

    const EVENT_PREFIX = 'Api.Measurement.';
    const BIND_NAME = __CLASS__;

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
     * @param Model           $model
     * @param PropertiesModel $properties
     * @param LanguageModel   $language
     */
    public function __construct(
        Model $model,
        PropertiesModel $properties,
        LanguageModel $language
    ) {
        $this->model      = $model;
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

            /** @var Model $measurement */
            $measurement = $this->model->createOrFailResource($input);
            Permissions::check($measurement, Permission::create());

            $measurementId = $measurement->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->properties->createOrFail(array_merge(
                    [Model::FIELD_ID => $measurementId, LanguageModel::FIELD_ID => $languageId],
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
        /** @var Model $measurement */
        $measurement = $this->model->selectByCode($code)->withProperties()->firstOrFail();
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
            /** @var Model $measurement */
            $measurement = $this->model->selectByCode($code)->firstOrFail();
            Permissions::check($measurement, Permission::edit());
            empty($input) ?: $measurement->updateOrFail($input);

            // update language properties
            $measurementId = $measurement->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $property = $this->properties->updateOrCreate(
                    [Model::FIELD_ID => $measurementId, LanguageModel::FIELD_ID => $languageId],
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
        /** @var Model $measurement */
        $measurement = $this->model->selectByCode($code)->firstOrFail();

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
        $measurements = $this->model->newQuery()->withProperties()->get();

        foreach ($measurements as $resource) {
            /** @var Model $resource */
            Permissions::check($resource, Permission::view());
        }

        return $measurements;
    }
}
