<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Model;

/**
 * @package Neomerx\Core
 */
interface BaseModelInterface
{
    /**
     * Get underlying model.
     *
     * @return Model
     */
    public function getModel();

    /**
     * Get the polymorphic relationship columns.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $modelId
     *
     * @return array
     */
    public function getModelMorphs($name, $type, $modelId);

    /**
     * @return array Validation rules.
     */
    public function getDataOnCreateRules();

    /**
     * @return array Validation rules.
     */
    public function getDataOnUpdateRules();

    /**
     * @return array Validation rules.
     */
    public function getInputOnCreateRules();

    /**
     * @return array Validation rules.
     */
    public function getInputOnUpdateRules();
}
