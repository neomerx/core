<?php namespace Neomerx\Core\Api\Territories;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\Country as Model;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\Region as RegionModel;
use \Neomerx\Core\Exceptions\ValidationException;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Models\Language as LanguageModel;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\CountryProperties as PropertiesModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Countries implements CountriesInterface
{
    use LanguagePropertiesTrait;

    const EVENT_PREFIX = 'Api.Country.';
    const BIND_NAME    = __CLASS__;

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
    public function __construct(Model $model, PropertiesModel $properties, LanguageModel $language)
    {
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

            /** @var Model $country */
            $country = $this->model->createOrFailResource($input);
            Permissions::check($country, Permission::create());

            $countryId = $country->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->properties->createOrFail(array_merge(
                    [Model::FIELD_ID => $countryId, LanguageModel::FIELD_ID => $languageId],
                    $propertyInput
                ));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new CountryArgs(self::EVENT_PREFIX . 'created', $country));

        return $country;
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @var Model $country */
        $country = $this->model->selectByCode($code)->withProperties()->firstOrFail();
        Permissions::check($country, Permission::view());

        return $country;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        list($input, $propertiesInput) = $this->extractPropertiesInput($this->language, $input);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var Model $country */
            $country = $this->model->selectByCode($code)->firstOrFail();

            Permissions::check($country, Permission::edit());

            empty($input) ?: $country->updateOrFail($input);

            $countryId = $country->{Model::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                /** @var PropertiesModel $property */
                $property = $this->properties->updateOrCreate(
                    [Model::FIELD_ID => $countryId, LanguageModel::FIELD_ID => $languageId],
                    $propertyInput
                );
                $property->exists ?: S\throwEx(new ValidationException($property->getValidator()));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new CountryArgs(self::EVENT_PREFIX . 'updated', $country));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        /** @var Model $country */
        $country = $this->model->selectByCode($code)->firstOrFail();

        Permissions::check($country, Permission::delete());

        $country->deleteOrFail();

        Event::fire(new CountryArgs(self::EVENT_PREFIX . 'deleted', $country));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $countries = $this->model->newQuery()->withProperties()->get();

        foreach ($countries as $country) {
            /** @var Model $country */
            Permissions::check($country, Permission::view());
        }

        return $countries;
    }

    /**
     * {@inheritdoc}
     */
    public function regions(Model $country)
    {
        Permissions::check($country, Permission::view());

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var Collection $regions */
        $regions = $country->regions()->orderBy(RegionModel::FIELD_POSITION, 'asc')->get();

        foreach ($regions as $region) {
            Permissions::check($region, Permission::view());
        }

        return $regions;
    }
}
