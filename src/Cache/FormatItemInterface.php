<?php namespace Neomerx\Core\Cache;

interface FormatItemInterface
{
    /**
     * Get unique ID of the formatter.
     *
     * @return string
     */
    public function getUId();

    /**
     * Format object to a defined format.
     *
     * @param mixed $object [object, [related key 1, related key 1, ...]] If object has data from other objects
     *                      then keys to related objects should be specified (for proper cache cleaning on updates).
     *
     * @return array
     */
    public function format($object);
    // TODO FormatInterface has similarities with converters in formatting. Make it DRY

    /**
     * Get a key to be used to store a formatted object in cache.
     * The key could be used by external systems to work with an underlying cache engine.
     *
     * @param string $objectId
     *
     * @return string
     */
    public function getKey($objectId);
}
