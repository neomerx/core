<?php namespace Neomerx\Core\Models;

interface BaseModelInterface
{
    /**
     * @return array Validation rules.
     */
    public static function getDataOnCreateRules();

    /**
     * @return array Validation rules.
     */
    public static function getDataOnUpdateRules();

    /**
     * @return array Validation rules.
     */
    public static function getInputOnCreateRules();

    /**
     * @return array Validation rules.
     */
    public static function getInputOnUpdateRules();
}
