<?php namespace Neomerx\Core\Repositories\Carriers;

use \Neomerx\Core\Models\Carrier;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\CarrierProperties;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface CarrierPropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Carrier $resource
     * @param Language $language
     * @param array    $attributes
     *
     * @return CarrierProperties
     */
    public function instance(Carrier $resource, Language $language, array $attributes);

    /**
     * @param CarrierProperties $properties
     * @param Carrier|null $resource
     * @param Language|null $language
     * @param array|null    $attributes
     *
     * @return void
     */
    public function fill(
        CarrierProperties $properties,
        Carrier $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Carrier
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
