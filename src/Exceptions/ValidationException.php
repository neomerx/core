<?php namespace Neomerx\Core\Exceptions;

use \Illuminate\Validation\Validator;
use \Illuminate\Contracts\Validation\ValidationException as BaseValidationException;

/**
 * @package Neomerx\Core
 */
class ValidationException extends BaseValidationException implements ExceptionInterface
{
    /**
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        parent::__construct($validator);
    }

    /**
     * @return Validator
     */
    public function getValidator()
    {
        return $this->getMessageProvider();
    }
}
