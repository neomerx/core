<?php namespace Neomerx\Core\Repositories\Features;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Models\Measurement;
use \Neomerx\Core\Models\MeasurementProperty;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface MeasurementPropertyRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Measurement $resource
     * @param Language    $language
     * @param array       $attributes
     *
     * @return MeasurementProperty
     */
    public function createWithObjects(Measurement $resource, Language $language, array $attributes);

    /**
     * @param int   $measurementId
     * @param int   $languageId
     * @param array $attributes
     *
     * @return MeasurementProperty
     */
    public function create($measurementId, $languageId, array $attributes);

    /**
     * @param MeasurementProperty $properties
     * @param Measurement|null      $resource
     * @param Language|null         $language
     * @param array|null            $attributes
     *
     * @return void
     */
    public function updateWithObjects(
        MeasurementProperty $properties,
        Measurement $resource = null,
        Language $language = null,
        array $attributes = null
    );

    /**
     * @param MeasurementProperty $properties
     * @param int|null              $measurementId
     * @param int|null              $languageId
     * @param array|null            $attributes
     *
     * @return void
     */
    public function update(
        MeasurementProperty $properties,
        $measurementId = null,
        $languageId = null,
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
