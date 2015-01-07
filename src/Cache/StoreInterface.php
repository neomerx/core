<?php namespace Neomerx\Core\Cache;

interface StoreInterface
{
    /**
     * Get item from cache.
     *
     * @param ItemProviderInterface $provider
     * @param FormatItemInterface   $formatter
     * @param string                $objectId
     *
     * @return mixed
     */
    public function get(ItemProviderInterface $provider, FormatItemInterface $formatter, $objectId);

    /**
     * Get original non-formatted item from cache.
     *
     * @param ItemProviderInterface $provider
     * @param string                $objectId
     * @return mixed
     */
    public function getObject(ItemProviderInterface $provider, $objectId);

    /**
     * Get items from cache.
     *
     * @param ItemProviderInterface $provider
     * @param array                 $formatters
     * @param array                 $objectIds
     *
     * @return array
     */
    public function getMany(ItemProviderInterface $provider, array $formatters, array $objectIds);

    /**
     * Remove item from cache.
     *
     * @param ItemProviderInterface $provider
     * @param string                $objectId
     *
     * @return void
     */
    public function forget(ItemProviderInterface $provider, $objectId);

    /**
     * Cache objects.
     *
     * @param ItemProviderInterface $provider
     * @param array                 $objectIds
     *
     * @return void
     */
    public function cache(ItemProviderInterface $provider, array $objectIds);
}
