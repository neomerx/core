<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Models\User;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class UserConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param User $user
     *
     * @return array
     */
    public function convert($user = null)
    {
        if ($user === null) {
            return null;
        }

        ($user instanceof User) ?: S\throwEx(new InvalidArgumentException('user'));

        $result = $user->attributesToArray();

        $roles = [];
        foreach ($user->roles as $role) {
            /** @var \Neomerx\Core\Models\Role $role */
            $roles[] = $role->code;
        }
        $result[User::FIELD_ROLES] = $roles;

        return $result;
    }
}
