<?php namespace Neomerx\Core\Api\Taxes;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Api\CrudInterface;
use \Illuminate\Database\Eloquent\Collection;

interface TaxRulesInterface extends CrudInterface
{
    const PARAM_FILTER_ALL     = '*';
    const PARAM_TERRITORY_CODE = 'code'; // country or region code
    const PARAM_TERRITORY_TYPE = 'type'; // country or region
    const PARAM_TYPE_CODE      = 'code'; // customer or product tax type code
    const PARAM_TYPE_NAME      = 'name'; // customer or product tax type name
    const PARAM_TAX_CODE       = 'tax_code';
    const PARAM_PRIORITY       = TaxRule::FIELD_PRIORITY;

    /**
     * Create tax rule.
     *
     * @param array $input
     *
     * @return TaxRule
     */
    public function create(array $input);

    /**
     * Read tax rule by identifier.
     *
     * @param int $ruleId
     *
     * @return TaxRule
     */
    public function read($ruleId);

    /**
     * Get all tax rules.
     *
     * @return Collection
     */
    public function all();
}
