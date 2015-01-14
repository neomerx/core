<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CountryProperties;

class CountryPropertiesRepository extends IndexBasedResourceRepository implements CountryPropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CountryProperties::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Country $resource, Language $language, array $attributes = null)
    {
        /** @var CountryProperties $properties */
        $properties = $this->makeModel();
        $this->fill($properties, $resource, $language, $attributes);
        return $properties;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        CountryProperties $properties,
        Country $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $data = [CountryProperties::FIELD_ID_COUNTRY => $resource, CountryProperties::FIELD_ID_LANGUAGE => $language];
        $this->fillModel($properties, $data, $attributes);
    }
}
