<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Models\TaxRulePostcode;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class TaxRulePostcodeRepository extends BaseRepository implements TaxRulePostcodeRepositoryInterface
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
    public function createWithObjects(TaxRule $rule, array $attributes = [])
    {
        return $this->create($this->idOf($rule), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($ruleId, array $attributes = [])
    {
        $resource = $this->createWith($attributes, $this->getRelationships($ruleId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(TaxRulePostcode $resource, TaxRule $rule = null, array $attributes = [])
    {
        $this->update($resource, $this->idOf($rule), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(TaxRulePostcode $resource, $ruleId = null, array $attributes = [])
    {
        $this->updateWith($resource, $attributes, $this->getRelationships($ruleId));
    }

    /**
     * @param int $ruleId
     *
     * @return array
     */
    protected function getRelationships($ruleId)
    {
        return $this->filterNulls([
            TaxRulePostcode::FIELD_ID_TAX_RULE => $ruleId,
        ]);
    }
}
