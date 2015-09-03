<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\ProductTaxType;
use \Neomerx\Core\Models\TaxRuleProductType;
use \Neomerx\Core\Repositories\BaseRepository;
use \Neomerx\Core\Repositories\Taxes\TaxRuleProductTypeRepositoryInterface as RepositoryInterface;

/**
 * @package Neomerx\Core
 */
class TaxRuleProductTypeRepository extends BaseRepository implements RepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(TaxRuleProductType::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(TaxRule $rule, Nullable $type = null)
    {
        return $this->create($this->idOf($rule), $this->idOfNullable($type, ProductTaxType::class));
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
    public function updateWithObjects(TaxRuleProductType $resource, TaxRule $rule = null, Nullable $type = null)
    {
        $this->update($resource, $this->idOf($rule), $this->idOfNullable($type, ProductTaxType::class));
    }

    /**
     * @inheritdoc
     */
    public function update(TaxRuleProductType $resource, $ruleId = null, Nullable $typeId = null)
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
            TaxRuleProductType::FIELD_ID_TAX_RULE => $ruleId,
        ], [
            TaxRuleProductType::FIELD_ID_PRODUCT_TAX_TYPE => $typeId,
        ]);
    }
}
