<?php namespace Neomerx\Core\Repositories\Customers;

use \Neomerx\Core\Models\Customer;
use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Support\Nullable;
use \Neomerx\Core\Models\CustomerRisk;
use \Neomerx\Core\Models\CustomerType;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Customer::class);
    }

    /**
     * @inheritdoc
     */
    public function createWithObjects(CustomerType $type, Language $language, array $attributes, Nullable $risk = null)
    {
        return $this->create(
            $this->idOf($type),
            $this->idOf($language),
            $attributes,
            $this->idOfNullable($risk, CustomerRisk::class)
        );
    }

    /**
     * @inheritdoc
     */
    public function create($typeId, $languageId, array $attributes, Nullable $riskId = null)
    {
        $resource = $this->createWith($attributes, $this->getRelationships($typeId, $languageId, $riskId));

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function updateWithObjects(
        Customer $resource,
        CustomerType $type = null,
        Language $language = null,
        array $attributes = null,
        Nullable $risk = null
    ) {
        $this->update(
            $resource,
            $this->idOf($type),
            $this->idOf($language),
            $attributes,
            $this->idOfNullable($risk, CustomerRisk::class)
        );
    }

    /**
     * @inheritdoc
     */
    public function update(
        Customer $resource,
        $typeId = null,
        $languageId = null,
        array $attributes = null,
        Nullable $riskId = null
    ) {
        $this->updateWith($resource, $attributes, $this->getRelationships($typeId, $languageId, $riskId));
    }

    /**
     * @param int           $typeId
     * @param int           $languageId
     * @param Nullable|null $riskId
     *
     * @return array
     */
    protected function getRelationships($typeId, $languageId, Nullable $riskId = null)
    {
        return $this->filterNulls([
            Customer::FIELD_ID_CUSTOMER_TYPE => $typeId,
            Customer::FIELD_ID_LANGUAGE      => $languageId,
        ], [
            Customer::FIELD_ID_CUSTOMER_RISK => $riskId,
        ]);
    }
}
