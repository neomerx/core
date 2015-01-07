<?php namespace Neomerx\Core\Converters;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\User as Model;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

class UserConverterGeneric implements ConverterInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * @inheritdoc
     */
    public function convert($resource = null)
    {
        if ($resource === null) {
            return null;
        }

        ($resource instanceof Model) ?: S\throwEx(new InvalidArgumentException('resource'));

        /** @var Model $resource */

        $result = $resource->attributesToArray();

        $roles = [];
        foreach ($resource->roles as $role) {
            /** @var Role $role */
            $roles[] = $role->code;
        }
        $result[Model::FIELD_ROLES] = $roles;

        return $result;
    }
}
