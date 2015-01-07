<?php namespace Neomerx\Core\Cache;

/**
 * Interface for a provider that gives items to store in a cache.
 */
interface ItemProviderInterface
{
    /**
     * Get tag for object.
     *
     * @param string $objectId
     *
     * @return string
     */
    public function getTag($objectId);

    /**
     * Get object by ID.
     *
     * @param string $objectId
     *
     * @return array    [object, [related key 1, related key 1, ...]] If object has data from other
     * objects then keys to related objects should be specified (for proper cache cleaning on updates).
     */
    public function getObject($objectId);

    /**
     * Get multiple objects by IDs at a time.
     *
     * @param array $objectIds
     *
     * @return array           [objectId => object (see getObject output format), ...]
     */
    public function getObjects(array $objectIds);

    /**
     * Get a key to be used to store a object in cache.
     * The key could be used by external systems to work with an underlying cache engine.
     *
     * @param string $objectId
     *
     * @return string
     */
    public function getKey($objectId);
}
