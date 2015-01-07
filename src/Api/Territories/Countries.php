<?php namespace Neomerx\Core\Api\Territories;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Language;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\CountryProperties;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Countries implements CountriesInterface
{
    use LanguagePropertiesTrait;

    const EVENT_PREFIX = 'Api.Country.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Country
     */
    private $country;

    /**
     * @var CountryProperties
     */
    private $properties;

    /**
     * @var Language
     */
    private $language;

    /**
     * @param Country           $country
     * @param CountryProperties $properties
     * @param Language          $language
     */
    public function __construct(Country $country, CountryProperties $properties, Language $language)
    {
        $this->country    = $country;
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

            /** @var Country $country */
            $country = $this->country->createOrFailResource($input);
            Permissions::check($country, Permission::create());

            $countryId = $country->{Country::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->properties->createOrFail(array_merge($propertyInput, [
                    CountryProperties::FIELD_ID_COUNTRY  => $countryId,
                    CountryProperties::FIELD_ID_LANGUAGE => $languageId
                ]));
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
        /** @var Country $country */
        $country = $this->country->selectByCode($code)->withProperties()->firstOrFail();
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

            /** @var Country $country */
            $country = $this->country->selectByCode($code)->firstOrFail();

            Permissions::check($country, Permission::edit());

            empty($input) ?: $country->updateOrFail($input);

            $countryId = $country->{Country::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                /** @var CountryProperties $property */
                $property = $this->properties->updateOrCreate(
                    [
                        CountryProperties::FIELD_ID_COUNTRY  => $countryId,
                        CountryProperties::FIELD_ID_LANGUAGE => $languageId
                    ],
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
        /** @var Country $country */
        $country = $this->country->selectByCode($code)->firstOrFail();

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
        $countries = $this->country->newQuery()->withProperties()->get();

        foreach ($countries as $country) {
            /** @var Country $country */
            Permissions::check($country, Permission::view());
        }

        return $countries;
    }

    /**
     * {@inheritdoc}
     */
    public function regions(Country $country)
    {
        Permissions::check($country, Permission::view());

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var Collection $regions */
        $regions = $country->regions()->orderBy(Region::FIELD_POSITION, 'asc')->get();

        foreach ($regions as $region) {
            Permissions::check($region, Permission::view());
        }

        return $regions;
    }
}
