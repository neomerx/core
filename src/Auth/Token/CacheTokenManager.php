<?php namespace Neomerx\Core\Auth\Token;

use \Illuminate\Contracts\Cache\Repository as CacheInterface;

/**
 * Cache based implementation of token manager.
 */
class CacheTokenManager implements TokenManagerInterface
{
    /**
     * @var int
     */
    private $cacheDurationInMinutes;

    /**
     * @var string
     */
    private $cacheKeyPrefix;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @param CacheInterface $cache
     * @param string         $cacheKeyPrefix
     * @param int            $durationInMinutes
     */
    public function __construct(
        CacheInterface $cache,
        $cacheKeyPrefix,
        $durationInMinutes = 5
    ) {
        assert('is_string($cacheKeyPrefix) === true && empty($cacheKeyPrefix) === false');
        assert('is_int($durationInMinutes) && $durationInMinutes > 0');

        $this->cache                  = $cache;
        $this->cacheKeyPrefix         = $cacheKeyPrefix;
        $this->cacheDurationInMinutes = $durationInMinutes;
    }

    /**
     * @inheritdoc
     */
    public function saveToken($token, $payload)
    {
        $this->cache->put($this->getCacheKey($token), $payload, $this->cacheDurationInMinutes);
    }

    /**
     * @inheritdoc
     */
    public function revokeToken($token)
    {
        $this->cache->forget($this->getCacheKey($token));
    }

    /**
     * @inheritdoc
     */
    public function hasToken($token)
    {
        return $this->cache->has($this->getCacheKey($token));
    }

    /**
     * @inheritdoc
     */
    public function getPayload($token)
    {
        return $this->cache->get($this->getCacheKey($token));
    }

    /**
     * @param string $token
     *
     * @return string
     */
    protected function getCacheKey($token)
    {
        return $this->cacheKeyPrefix.$token;
    }
}
