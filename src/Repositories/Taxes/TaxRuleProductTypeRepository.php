<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Models\TaxRuleProductType;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;
use \Neomerx\Core\Repositories\Taxes\TaxRuleProductTypeRepositoryInterface as RepositoryInterface;

class TaxRuleProductTypeRepository extends IndexBasedResourceRepository implements RepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(TaxRuleProductType::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(TaxRule $rule, ProductTaxType $type = null)
    {
        /** @var TaxRuleProductType $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $rule, $type);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(TaxRuleProductType $resource, TaxRule $rule = null, ProductTaxType $type = null)
    {
        $this->fillModel($resource, [
            TaxRuleProductType::FIELD_ID_TAX_RULE         => $rule,
            TaxRuleProductType::FIELD_ID_PRODUCT_TAX_TYPE => $type,
        ]);
    }
}
