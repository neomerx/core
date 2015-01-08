<?php namespace Neomerx\Core\Cache;

interface TagProviderInterface
{
    /**
     * Get tags for object.
     *
     * @param \Neomerx\Core\Models\Variant $object
     *
     * @return string[]
     */
    public function getTags($object);
}
