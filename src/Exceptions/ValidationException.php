<?php namespace Neomerx\Core\Exceptions;

use \Illuminate\Validation\Validator;
use \Neomerx\Core\Support\Translate as T;

/**
 * @package Neomerx\Core
 */
class ValidationException extends LogicException
{
    /**
     * @var \Illuminate\Validation\Validator
     */
    private $validator;

    /**
     * Constructor.
     *
     * @param Validator|null $validator
     * @inheritdoc
     */
    public function __construct(Validator $validator = null, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($this->loadIfEmpty($message, T::KEY_EX_VALIDATION_EXCEPTION), $code, $previous);
        $this->validator = $validator;
    }

    /**
     * @return \Illuminate\Validation\Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }
}
