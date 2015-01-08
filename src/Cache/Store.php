<?php namespace Neomerx\Core\Cache;

use \Neomerx\Core\Support as S;
use \Illuminate\Support\Facades\Cache as SysCache;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class Store implements StoreInterface
{
    use TagTrait;

    const BIND_NAME = __CLASS__;

    /**
     * @var int
     */
    private $cachePeriod = 60;

    /**
     * Get item from cache.
     *
     * @param ItemProviderInterface $provider
     * @param FormatItemInterface   $formatter
     * @param string                $objectId
     *
     * @return mixed
     */
    public function get(ItemProviderInterface $provider, FormatItemInterface $formatter, $objectId)
    {
        // key for formatted object
        $fObjectKey = $formatter->getKey($objectId);

        /** @noinspection PhpUndefinedMethodInspection */
        if (!is_null($fObject = SysCache::get($fObjectKey))) {
            return $fObject;
        }

        $object = $this->getOriginal($objectId, $provider);

        // we got the original object now
        // we can format it, put to cache and return

        return $this->formatAndCache($object, $fObjectKey, $formatter);
    }

    /**
     * Get original non-formatted item from cache.
     *
     * @param ItemProviderInterface $provider
     * @param string                $objectId
     * @return mixed
     */
    public function getObject(ItemProviderInterface $provider, $objectId)
    {
        return $this->getOriginal($objectId, $provider);
    }

    /**
     * Get items from cache.
     *
     * @param ItemProviderInterface $provider
     * @param array                 $formatters
     * @param array                 $objectIds
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getMany(ItemProviderInterface $provider, array $formatters, array $objectIds)
    {
        $result = [];

        // IDs for not cached objects
        $notCachedIds = [];
        foreach ($objectIds as $objectId) {

            $object               = null;
            $objectKey            = null;
            $hasCheckedObjInCache = false;

            foreach ($formatters as $formatter) {
                /** @var FormatItemInterface $formatter */
                $formatCode = $formatter->getUId();
                // key for formatted object
                $fObjectKey = $formatter->getKey($objectId);

                /** @noinspection PhpUndefinedMethodInspection */
                if (!is_null($fObject = SysCache::get($fObjectKey))) {
                    $this->addObject($result, $fObject, $objectId, $formatCode);
                } else {

                    // formatted object not found in cache
                    // let's try to read the original object from cache and format

                    // we get object and its key only once and only if we get to this code branch
                    // which might be the case for multiple formats for the same object
                    if (!$hasCheckedObjInCache) {
                        $objectKey = $provider->getKey($objectId);
                        /** @noinspection PhpUndefinedMethodInspection */
                        $object = SysCache::get($objectKey);
                        $hasCheckedObjInCache = true;
                    }

                    if (isset($object)) {
                        $fObject = $this->formatAndCache($object, $fObjectKey, $formatter);
                        $this->addObject($result, $fObject, $objectId, $formatCode);
                    } else {
                        array_key_exists($objectId, $notCachedIds) ?: $notCachedIds[$objectId] = $objectKey;
                    }
                }
            }

        }

        // when we are here we have some formatted objects in $result but maybe some not
        // we need to read the rest (IDs in $notCachedIds), format and cache before returning $result

        if (!empty($notCachedIds)) {
            foreach ($provider->getObjects(array_keys($notCachedIds)) as $objectId => $objectAndTags) {

                /** @noinspection PhpUndefinedMethodInspection */
                SysCache::tags($objectAndTags[1])->put($notCachedIds[$objectId], $objectAndTags[0], $this->cachePeriod);
                //                ^ object tags               ^ cache key             ^ object
                foreach ($formatters as $formatter) {
                    $formatCode = $formatter->getUId();
                    $fObject    = $this->formatAndCache($objectAndTags[0], $formatter->getKey($objectId), $formatter);
                    $this->addObject($result, $fObject, $objectId, $formatCode);
                }

            }
        }

        return $result;
    }

    /**
     * Cache objects.
     *
     * @param ItemProviderInterface $provider
     * @param array                 $objectIds
     *
     * @return void
     */
    public function cache(ItemProviderInterface $provider, array $objectIds)
    {
        foreach ($provider->getObjects($objectIds) as $objectAndTags) {
            /** @noinspection PhpUndefinedMethodInspection */
            SysCache::tags($objectAndTags[1])
                ->put($provider->getKey($objectAndTags[0]), $objectAndTags[0], $this->cachePeriod);
        }
    }

    /**
     * Remove item from cache.
     *
     * @param ItemProviderInterface $provider
     * @param string                $objectId
     *
     * @return void
     */
    public function forget(ItemProviderInterface $provider, $objectId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        SysCache::tags($provider->getTag($objectId))->flush();
        /** @noinspection PhpUndefinedMethodInspection */
        SysCache::forget($provider->getKey($objectId));
    }

    /**
     * @return int
     */
    public function getCachePeriod()
    {
        return $this->cachePeriod;
    }

    /**
     * @param int $minutes
     */
    public function setCachePeriod($minutes)
    {
        settype($minutes, 'int');
        $minutes > 0 ?: S\throwEx(new InvalidArgumentException('minutes'));
        $this->cachePeriod = $minutes;
    }

    /**
     * @param mixed               $object
     * @param string              $key
     * @param FormatItemInterface $formatter
     *
     * @return mixed
     */
    private function formatAndCache($object, $key, FormatItemInterface $formatter)
    {
        list($fObject, $tags) = $formatter->format($object);
        /** @noinspection PhpUndefinedMethodInspection */
        SysCache::tags($tags)->put($key, $fObject, $this->cachePeriod);
        return $fObject;
    }

    /**
     * @param string             $objectId
     * @param ItemProviderInterface $itemProvider
     *
     * @return mixed
     */
    private function getOriginal($objectId, ItemProviderInterface $itemProvider)
    {
        // key for original object
        $objectKey = $itemProvider->getKey($objectId);

        // get the object either from cache or from item provider
        /** @noinspection PhpUndefinedMethodInspection */
        if (is_null($object = SysCache::get($objectKey))) {
            list($object, $tags) = $itemProvider->getObject($objectId);
            /** @noinspection PhpUndefinedMethodInspection */
            SysCache::tags($tags)->put($objectKey, $object, $this->cachePeriod);
        }

        return $object;
    }

    /**
     * @param array  $array
     * @param mixed  $object
     * @param int    $objectId
     * @param string $formatCode
     */
    private function addObject(array &$array, $object, $objectId, $formatCode)
    {
        if (array_key_exists($objectId, $array)) {
            $array[$objectId][$formatCode] = $object;
        } else {
            $array[$objectId] = [$formatCode => $object];
        }
    }
}
