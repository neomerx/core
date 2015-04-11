<?php namespace Neomerx\Core\Auth\Token;

use \stdClass;
use \JsonSerializable;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends stdClass implements AuthenticatableContract, JsonSerializable
{
    const FIELD_REMEMBER_TOKEN = 'remember_token';

    /**
     * @param int           $authIdentifier
     * @param string        $authPassword
     * @param string|null   $token
     * @param stdClass|null $attributes
     *
     * @throws InvalidArgumentException
     */
    public function __construct($authIdentifier, $authPassword, $token, stdClass $attributes = null)
    {
        if ($token !== null && (is_string($token) === false || empty($token) === true)) {
            throw new InvalidArgumentException('token');
        }
        if (is_string($authPassword) === false || empty($authPassword) === true) {
            throw new InvalidArgumentException('authPassword');
        }
        if (is_scalar($authIdentifier) === false || empty($authIdentifier) === true) {
            throw new InvalidArgumentException('authIdentifier');
        }

        $this->applyAttributes($attributes);
        $this->setRememberToken($token);
        $this->authPassword   = $authPassword;
        $this->authIdentifier = $authIdentifier;
    }

    /**
     * @inheritdoc
     */
    public function getAuthIdentifier()
    {
        return $this->authIdentifier;
    }

    /**
     * @inheritdoc
     */
    public function getAuthPassword()
    {
        return $this->authPassword;
    }

    /**
     * @inheritdoc
     */
    public function getRememberToken()
    {
        return $this->{self::FIELD_REMEMBER_TOKEN};
    }

    /**
     * @inheritdoc
     */
    public function setRememberToken($value)
    {
        $this->{self::FIELD_REMEMBER_TOKEN} = $value;
    }

    /**
     * @inheritdoc
     */
    public function getRememberTokenName()
    {
        return self::FIELD_REMEMBER_TOKEN;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $clone = (array)$this;
        unset($clone[self::FIELD_REMEMBER_TOKEN]);
        return (object)$clone;
    }

    /**
     * @param string $json
     * @param string $token
     *
     * @return User
     */
    public static function jsonDecode($json, $token)
    {
        $decoded = json_decode($json);
        return new self($decoded->authIdentifier, $decoded->authPassword, $token, $decoded);
    }

    /**
     * @param stdClass|null $attributes
     */
    private function applyAttributes(stdClass $attributes = null)
    {
        if ($attributes !== null) {
            foreach (get_object_vars($attributes) as $property => $value) {
                $this->{$property} = $value;
            }
        }
    }
}
