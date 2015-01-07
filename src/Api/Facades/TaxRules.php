<?php namespace Neomerx\Core\Api\Facades;

use \Neomerx\Core\Models\TaxRule;
use \Illuminate\Support\Facades\Facade;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Api\Taxes\TaxRulesInterface;

/**
 * @see TaxRulesInterface
 *
 * @method static TaxRule    create(array $input)
 * @method static TaxRule    read(string $code)
 * @method static void       update(string $code, array $input)
 * @method static void       delete(string $code)
 * @method static Collection all()
 */
class TaxRules extends Facade
{
    const INTERFACE_BIND_NAME = TaxRulesInterface::class;

    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return self::INTERFACE_BIND_NAME;
    }
}
