<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\Tax;
use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Repositories\IndexBasedResourceRepository;

class TaxRuleRepository extends IndexBasedResourceRepository implements TaxRuleRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(TaxRule::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(Tax $tax, array $attributes)
    {
        /** @var TaxRule $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $tax, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(TaxRule $resource, Tax $tax = null, array $attributes = null)
    {
        $this->fillModel($resource, [
            TaxRule::FIELD_ID_TAX => $tax,
        ], $attributes);
    }
}
