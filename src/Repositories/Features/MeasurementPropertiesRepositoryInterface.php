<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Measurement;
use \Neomerx\Core\Models\MeasurementProperties;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface MeasurementPropertiesRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Measurement $resource
     * @param Language    $language
     * @param array       $attributes
     *
     * @return MeasurementProperties
     */
    public function instance(Measurement $resource, Language $language, array $attributes);

    /**
     * @param MeasurementProperties $properties
     * @param Measurement|null      $resource
     * @param Language|null         $language
     * @param array|null            $attributes
     *
     * @return void
     */
    public function fill(
        MeasurementProperties $properties,
        Measurement $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param int   $resourceId
     * @param array $scopes
     * @param array $columns
     *
     * @return Measurement
     */
    public function read($resourceId, array $scopes = [], array $columns = ['*']);
}
