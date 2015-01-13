<?php namespace Neomerx\Core\Exceptions;

use \Illuminate\Validation\Validator;

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
     * {@inheritDoc}
     */
    public function __construct(Validator $validator = null, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($this->loadIfEmpty($message, 'validation_exception'), $code, $previous);
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
