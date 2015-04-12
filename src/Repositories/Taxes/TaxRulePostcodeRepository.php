<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Models\TaxRulePostcode;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

/**
 * @package Neomerx\Core
 */
class TaxRulePostcodeRepository extends IndexBasedResourceRepository implements TaxRulePostcodeRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(TaxRulePostcode::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(TaxRule $rule, array $attributes = null)
    {
        /** @var TaxRulePostcode $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $rule, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(TaxRulePostcode $resource, TaxRule $rule = null, array $attributes = null)
    {
        $this->fillModel($resource, [
            TaxRulePostcode::FIELD_ID_TAX_RULE => $rule,
        ], $attributes);
    }
}
