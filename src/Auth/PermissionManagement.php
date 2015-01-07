<?php namespace Neomerx\Core\Auth;

class PermissionManagement implements PermissionManagementInterface
{
    /**
     * {@inheritdoc}
     */
    public function has(ObjectIdentityInterface $object, Permission $permission)
    {
        // just an example. not implemented yet.
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function check(ObjectIdentityInterface $object, Permission $permission)
    {
        // just an example. not implemented yet.
        //$identity = $object->getIdentifier();
        //$canCreate = $permission->has(Permission::CREATE);

        $object->getIdentifier();
        $permission->has(Permission::CREATE);
    }
}
