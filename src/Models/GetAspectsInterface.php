<?php namespace Neomerx\Core\Models;

/**
 * @package Neomerx\Core
 */
interface GetAspectsInterface
{
    /**
     * Relation to aspects.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aspects();
}
