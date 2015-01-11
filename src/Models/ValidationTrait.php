<?php namespace Neomerx\Core\Models;

use \Validator;

trait ValidationTrait
{
    /**
     * @var \Illuminate\Validation\Validator Stores validation result.
     */
    private $validator;

    /**
     * @var BaseModelInterface
     */
    private $baseModelVT;

    public function initValidationTrait(BaseModelInterface $baseModel)
    {
        $this->baseModelVT = $baseModel;
    }

    /**
     * Return validation result.
     *
     * @return \Illuminate\Validation\Validator Validation result.
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Validate data against rules.
     *
     * @return bool Result.
     */
    public function isDataOnCreateValid()
    {
        return $this->validateAndStoreValidator(
            $this->baseModelVT->getModel()->getAttributes(),
            $this->baseModelVT->getDataOnCreateRules()
        );
    }

    /**
     * Validate input against rules.
     *
     * @param $input array Input.
     *
     * @return bool Result.
     */
    public function isInputOnCreateValid(array $input)
    {
        return Validator::make($input, $this->baseModelVT->getInputOnCreateRules())->passes();
    }

    /**
     * Validate data against rules.
     *
     * @return bool Result.
     */
    public function isDataOnUpdateValid()
    {
        return $this->validateAndStoreValidator(
            $this->baseModelVT->getModel()->getDirty(),
            $this->baseModelVT->getDataOnUpdateRules()
        );
    }

    /**
     * Validate input against rules.
     *
     * @param array $input Input.
     *
     * @return bool Result.
     */
    public function isInputOnUpdateValid(array $input)
    {
        return Validator::make($input, $this->baseModelVT->getInputOnUpdateRules())->passes();
    }

    /**
     * Validates input on create.
     *
     * @param array $input
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validateInputOnCreate(array $input)
    {
        return Validator::make($input, $this->baseModelVT->getInputOnCreateRules());
    }

    /**
     * Validates input on update.
     *
     * @param array $input
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validateInputOnUpdate(array $input)
    {
        return Validator::make($input, $this->baseModelVT->getInputOnUpdateRules());
    }


    /**
     * @return array Validation rules.
     */
    abstract public function getDataOnCreateRules();

    /**
     * @return array Validation rules.
     */
    abstract public function getDataOnUpdateRules();


    /**
     * @return array Validation rules.
     */
    public function getInputOnCreateRules()
    {
        return $this->getDataOnCreateRules();
    }

    /**
     * @return array Validation rules.
     */
    public function getInputOnUpdateRules()
    {
        return $this->getDataOnUpdateRules();
    }

    /**
     * Validates data against rules.
     *
     * @param array $data  Data.
     * @param array $rules Rules.
     *
     * @return bool Result.
     */
    private function validateAndStoreValidator(array $data, array $rules)
    {
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $this->validator = $validator;
            return false;
        }

        return true;
    }
}
