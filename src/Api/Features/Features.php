<?php namespace Neomerx\Core\Api\Features;

use \Neomerx\Core\Models\Characteristic;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class Features implements FeaturesInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * @var Characteristics
     */
    private $characteristics;

    /**
     * @var Values
     */
    private $values;

    /**
     * @var Measurements
     */
    private $measurements;

    /**
     * @param Characteristics $characteristics
     * @param Values          $values
     * @param Measurements    $measurements
     */
    public function __construct(Characteristics $characteristics, Values $values, Measurements $measurements)
    {
        $this->characteristics = $characteristics;
        $this->values          = $values;
        $this->measurements    = $measurements;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        return $this->characteristics->create($input);
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        return $this->characteristics->read($code);
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        $this->characteristics->update($code, $input);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        $this->characteristics->delete($code);
    }

    /**
     * {@inheritdoc}
     */
    public function search(array $parameters = [])
    {
        return $this->characteristics->search($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function allValues(Characteristic $characteristic)
    {
        return $this->values->all($characteristic);
    }

    /**
     * {@inheritdoc}
     */
    public function addValues(Characteristic $characteristic, array $input)
    {
        $this->values->addValues($characteristic, $input);
    }

    /**
     * {@inheritdoc}
     */
    public function readValue($code)
    {
        return $this->values->read($code);
    }

    /**
     * {@inheritdoc}
     */
    public function updateValue($code, array $input)
    {
        $this->values->update($code, $input);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteValue($code)
    {
        $this->values->delete($code);
    }
}
