<?php namespace Neomerx\Core\Repositories\Currencies;

use \Neomerx\Core\Models\Currency;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class CurrencyRepository extends CodeBasedResourceRepository implements CurrencyRepositoryInterface
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
    public function instance(array $attributes)
    {
        /** @var Currency $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Currency $resource, array $attributes)
    {
        $this->fillModel($resource, [], $attributes);
    }
}
