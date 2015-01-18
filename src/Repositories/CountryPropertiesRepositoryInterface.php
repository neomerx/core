<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CountryProperties;
use \Illuminate\Database\Eloquent\Collection;

interface CountryPropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Country    $resource
     * @param Language   $language
     * @param array|null $attributes
     *
     * @return CountryProperties
     */
    public function instance(Country $resource, Language $language, array $attributes = null);

    /**
     * @param CountryProperties $properties
     * @param Country|null      $resource
     * @param Language|null     $language
     * @param array|null        $attributes
     *
     * @return void
     */
    public function fill(
        CountryProperties $properties,
        Country $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param Country $resource
     *
     * @return Collection
     */
    public function regions(Country $resource);
}
