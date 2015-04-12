<?php namespace Neomerx\Core\Repositories\Currencies;

use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CurrencyProperties;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class CurrencyPropertiesRepository extends IndexBasedResourceRepository implements CurrencyPropertiesRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(CurrencyProperties::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(Currency $resource, Language $language, array $attributes)
    {
        /** @var CurrencyProperties $properties */
        $properties = $this->makeModel();
        $this->fill($properties, $resource, $language, $attributes);
        return $properties;
    }

    /**
     * @inheritdoc
     */
    public function fill(
        CurrencyProperties $properties,
        Currency $resource = null,
        Language $language = null,
        array $attributes = null
    ) {
        $data = [
            CurrencyProperties::FIELD_ID_CURRENCY => $resource,
            CurrencyProperties::FIELD_ID_LANGUAGE => $language
        ];
        $this->fillModel($properties, $data, $attributes);
    }
}
