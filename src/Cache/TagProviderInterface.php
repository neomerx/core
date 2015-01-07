<?php namespace Neomerx\Core\Cache;

interface TagProviderInterface
{
    /**
     * Get tags for object.
     *
     * @param mixed $object
     *
     * @return array
     */
    public function getTags($object);
}
