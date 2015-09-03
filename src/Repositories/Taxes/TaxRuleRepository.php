<?php namespace Neomerx\Core\Repositories\Taxes;

use \Neomerx\Core\Models\Tax;
use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class TaxRuleRepository extends BaseRepository implements TaxRuleRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(TaxRule::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(Tax $tax, array $attributes)
    {
        return $this->create($this->idOf($tax), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function create($taxId, array $attributes)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($taxId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(TaxRule $resource, Tax $tax = null, array $attributes = null)
    {
        $this->update($resource, $this->idOf($tax), $attributes);
    }

    /**
     * @inheritdoc
     */
    public function update(TaxRule $resource, $taxId = null, array $attributes = null)
    {
        $this->updateWith($resource, $attributes, $this->getRelationships($taxId));
    }

    /**
     * @param int $taxId
     *
     * @return array
     */
    private function getRelationships($taxId)
    {
        return $this->filterNulls([
            TaxRule::FIELD_ID_TAX => $taxId,
        ]);
    }
}
