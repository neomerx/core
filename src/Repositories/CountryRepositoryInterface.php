<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Models\Language;

interface CountryRepositoryInterface
{
    /**
     * @param array|null $attributes
     *
     * @return Country
     */
    public function instance(array $attributes = null);

    /**
     * @param Country $resource
     * @param array|null $attributes
     *
     * @return void
     */
    public function fill(Country $resource, array $attributes = null);
}
