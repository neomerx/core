<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class RoleConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * Format model to array representation.
     *
     * @param Role $role
     *
     * @return array
     */
    public function convert($role = null)
    {
        if ($role === null) {
            return null;
        }

        ($role instanceof Role) ?: S\throwEx(new InvalidArgumentException('role'));

        $result = $role->attributesToArray();

        return $result;
    }
}
