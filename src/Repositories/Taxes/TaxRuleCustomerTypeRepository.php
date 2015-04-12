<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Models\TaxRuleCustomerType;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;
use \Neomerx\Core\Repositories\Taxes\TaxRuleCustomerTypeRepositoryInterface as RepositoryInterface;

/**
 * @package Neomerx\Core
 */
class TaxRuleCustomerTypeRepository extends IndexBasedResourceRepository implements RepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(TaxRuleCustomerType::class);
    }

    /**
     * @inheritdoc
     */
    public function instance(TaxRule $rule, CustomerType $type = null)
    {
        /** @var TaxRuleCustomerType $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $rule, $type);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(TaxRuleCustomerType $resource, TaxRule $rule = null, CustomerType $type = null)
    {
        $this->fillModel($resource, [
            TaxRuleCustomerType::FIELD_ID_TAX_RULE      => $rule,
            TaxRuleCustomerType::FIELD_ID_CUSTOMER_TYPE => $type,
        ]);
    }
}
