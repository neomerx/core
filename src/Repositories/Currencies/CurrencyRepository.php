<?php namespace Neomerx\Core\Repositories\Currencies;

use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class CurrencyRepository extends BaseRepository implements CurrencyRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Currency::class);
    }

    /**
     * @inheritdoc
     */
    public function create(array $attributes)
    {
        $resource = $this->createWith($attributes, []);

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function update(Currency $resource, array $attributes)
    {
        $this->updateWith($resource, $attributes, []);
    }
}
