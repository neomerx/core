<?php namespace Neomerx\Core\Models;

/**
 * @package Neomerx\Core
 */
interface GetSpecificationInterface
{
    /**
     * Relation to specification.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specification();
}
