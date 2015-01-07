<?php namespace Neomerx\Core\Cache;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

abstract class Cache
{
    /**
     * @var array
     */
    private $formatters;

    /**
     * @var ItemProviderInterface
     */
    private $provider;

    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @param ItemProviderInterface $provider
     * @param array                 $formatters
     * @param StoreInterface        $store
     */
    public function __construct(
        ItemProviderInterface $provider,
        array $formatters,
        StoreInterface $store
    ) {
        $formatterMap = [];
        foreach ($formatters as $formatter) {
            /** @var FormatItemInterface $formatter */
            $formatter instanceof FormatItemInterface ?: S\throwEx(new InvalidArgumentException('formatters'));
            $uid = $formatter->getUId();
            // check no formatters with duplicate unique IDs
            !array_key_exists($uid, $formatterMap) ?: S\throwEx(new InvalidArgumentException('formatters'));
            $formatterMap[$uid] = $formatter;
        }

        $this->store       = $store;
        $this->provider    = $provider;
        $this->formatters  = $formatterMap;
    }

    /**
     * Get item from cache.
     *
     * @param string $format
     * @param string $objectId
     *
     * @return mixed
     */
    public function get($format, $objectId)
    {
        return $this->store->get($this->provider, $this->getFormatter($format), $objectId);
    }

    /**
     * Get original non-formatted item from cache.
     *
     * @param string $objectId
     *
     * @return mixed
     */
    public function getObject($objectId)
    {
        return $this->store->getObject($this->provider, $objectId);
    }

    /**
     * Get items from cache.
     *
     * @param array $formats
     * @param array $objectIds
     *
     * @return array
     */
    public function getMany(array $formats, array $objectIds)
    {
        $selectedFormatters = [];
        foreach ($formats as $format) {
            $selectedFormatters[] = $this->getFormatter($format);
        }
        return $this->store->getMany($this->provider, $selectedFormatters, $objectIds);
    }

    /**
     * Cache objects.
     *
     * @param array $objectIds
     */
    public function cacheMany(array $objectIds)
    {
        $this->store->cache($this->provider, $objectIds);
    }

    /**
     * Remove item from cache.
     *
     * @param string $objectId
     *
     * @return void
     */
    public function forget($objectId)
    {
        $this->store->forget($this->provider, $objectId);
    }

    /**
     * @param string $format
     *
     * @return FormatItemInterface
     */
    private function getFormatter($format)
    {
        array_key_exists($format, $this->formatters) ?: S\throwEx(new InvalidArgumentException('format'));
        return $this->formatters[$format];
    }
}
