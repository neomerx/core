<?php namespace Neomerx\Core\Models;

interface GetSpecificationInterface
{
    /**
     * Relation to specification.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specification();
}
