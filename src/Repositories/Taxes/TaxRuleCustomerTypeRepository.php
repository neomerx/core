<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Models\TaxRuleCustomerType;
use \Neomerx\Core\Repositories\BaseRepository;
use \Neomerx\Core\Repositories\Taxes\TaxRuleCustomerTypeRepositoryInterface as RepositoryInterface;

/**
 * @package Neomerx\Core
 */
class TaxRuleCustomerTypeRepository extends BaseRepository implements RepositoryInterface
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
    public function createWithObjects(TaxRule $rule, Nullable $type = null)
    {
        return $this->create($this->idOf($rule), $this->idOfNullable($type, CustomerType::class));
    }

    /**
     * @inheritdoc
     */
    public function create($ruleId, Nullable $typeId = null)
    {
        $resource = $this->createWith([], $this->getRelationships($ruleId, $typeId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(TaxRuleCustomerType $resource, TaxRule $rule = null, Nullable $type = null)
    {
        $this->update($resource, $this->idOf($rule), $this->idOfNullable($type, CustomerType::class));
    }

    /**
     * @inheritdoc
     */
    public function update(TaxRuleCustomerType $resource, $ruleId = null, Nullable $typeId = null)
    {
        $this->updateWith($resource, [], $this->getRelationships($ruleId, $typeId));
    }

    /**
     * @param int           $ruleId
     * @param Nullable|null $typeId
     *
     * @return array
     */
    private function getRelationships($ruleId, Nullable $typeId = null)
    {
        return $this->filterNulls([
            TaxRuleCustomerType::FIELD_ID_TAX_RULE     => $ruleId,
        ], [
            TaxRuleCustomerType::FIELD_ID_CUSTOMER_TYPE => $typeId,
        ]);
    }
}
