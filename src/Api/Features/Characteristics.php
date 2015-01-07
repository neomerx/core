<?php namespace Neomerx\Core\Api\Features;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\Characteristic as Model;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Models\Language as LanguageModel;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\Measurement as MeasurementModel;
use \Neomerx\Core\Models\CharacteristicProperties as PropertiesModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Characteristics implements CharacteristicsInterface
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
    private $propertiesModel;

    /**
     * @var LanguageModel
     */
    private $languageModel;

    /**
     * @var MeasurementModel
     */
    private $measurementModel;

    /**
     * Searchable fields of the resource.
     * Could be used as parameters in search function.
     *
     * @var array
     */
    protected static $searchRules   = [
        Model::FIELD_CODE         => SearchGrammar::TYPE_STRING,
        'created'                 => [SearchGrammar::TYPE_DATE, Model::FIELD_CREATED_AT],
        'updated'                 => [SearchGrammar::TYPE_DATE, Model::FIELD_UPDATED_AT],
        SearchGrammar::LIMIT_SKIP => SearchGrammar::TYPE_LIMIT,
        SearchGrammar::LIMIT_TAKE => SearchGrammar::TYPE_LIMIT,
    ];

    /**
     * @param Model            $model
     * @param PropertiesModel  $properties
     * @param LanguageModel    $language
     * @param MeasurementModel $measurement
     */
    public function __construct(
        Model $model,
        PropertiesModel $properties,
        LanguageModel $language,
        MeasurementModel $measurement
    ) {
        $this->model            = $model;
        $this->propertiesModel  = $properties;
        $this->languageModel    = $language;
        $this->measurementModel = $measurement;
    }

    /**
     * @inheritdoc
     */
    public function create(array $input)
    {
        list($input, $propertiesInput) = $this->extractPropertiesInput($this->languageModel, $input);

        // check language properties are not empty
        count($propertiesInput) ? null : S\throwEx(new InvalidArgumentException('properties'));

        $this->replaceMeasurementCodeWithId($input);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var Model $characteristic */
            $characteristic = $this->model->createOrFailResource($input);

            Permissions::check($characteristic, Permission::create());

            $resourceId = $characteristic->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->propertiesModel->createOrFail(array_merge([
                    PropertiesModel::FIELD_ID_CHARACTERISTIC => $resourceId,
                    PropertiesModel::FIELD_ID_LANGUAGE       => $languageId
                ], $propertyInput));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new FeatureCharacteristicArgs(self::EVENT_PREFIX . 'created', $characteristic));

        return $characteristic;
    }

    /**
     * @inheritdoc
     */
    public function read($code)
    {
        /** @var Model $resource */
        /** @noinspection PhpUndefinedMethodInspection */
        $resource = $this->model->selectByCode($code)->withProperties()->withMeasurement()->firstOrFail();

        Permissions::check($resource, Permission::view());

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function update($code, array $input)
    {
        // get input for properties
        list($input, $propertiesInput) = $this->extractPropertiesInput($this->languageModel, $input);
        $this->replaceMeasurementCodeWithId($input);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {
            // update resource
            /** @var Model $characteristic */
            $characteristic = $this->model->selectByCode($code)->firstOrFail();

            Permissions::check($characteristic, Permission::edit());

            empty($input) ?: $characteristic->updateOrFail($input);

            // update language properties
            $resourceId = $characteristic->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                /** @var PropertiesModel $property */
                $property = $this->propertiesModel->updateOrCreate([
                    PropertiesModel::FIELD_ID_CHARACTERISTIC => $resourceId,
                    PropertiesModel::FIELD_ID_LANGUAGE       => $languageId
                ], $propertyInput);
                $property->exists ?: S\throwEx(new ValidationException($property->getValidator()));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new FeatureCharacteristicArgs(self::EVENT_PREFIX . 'updated', $characteristic));

        return $characteristic;
    }

    /**
     * @param string $code
     */
    public function delete($code)
    {
        /** @var Model $characteristic */
        $characteristic = $this->model->selectByCode($code)->firstOrFail();

        Permissions::check($characteristic, Permission::delete());

        Event::fire(new FeatureCharacteristicArgs(self::EVENT_PREFIX . 'deleted', $characteristic));

        $characteristic->deleteOrFail();
    }

    /**
     * @inheritdoc
     */
    public function search(array $parameters = [])
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $builder = $this->model->newQuery()->withProperties()->withMeasurement();

        // add search parameters if required
        if (!empty($parameters)) {
            $parser  = new SearchParser(new SearchGrammar($builder), static::$searchRules);
            $builder = $parser->buildQuery($parameters);
        }

        $resources = $builder->get();

        /** @var Model $resource */
        foreach ($resources as $resource) {
            Permissions::check($resource, Permission::view());
        }

        return $resources;
    }

    /**
     * @param array &$input
     *
     * @return void
     */
    private function replaceMeasurementCodeWithId(array &$input)
    {
        $code = $input[self::PARAM_MEASUREMENT_CODE];
        $input[MeasurementModel::FIELD_ID] = $this->measurementModel
            ->selectByCode($code)
            ->firstOrFail([MeasurementModel::FIELD_ID])
            ->{MeasurementModel::FIELD_ID};
        unset($input[self::PARAM_MEASUREMENT_CODE]);
    }
}
