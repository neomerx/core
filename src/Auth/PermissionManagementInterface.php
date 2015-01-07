<?php namespace Neomerx\Core\Auth;

interface PermissionManagementInterface
{
    /**
     * Get true/false if user has permission for object.
     *
     * @param ObjectIdentityInterface $object
     * @param Permission              $permission
     *
     * @return bool
     */
    public function has(ObjectIdentityInterface $object, Permission $permission);

    /**
     * Check user permission for object.
     *
     * @param ObjectIdentityInterface $object
     * @param Permission              $permission
     *
     * @return void
     */
    public function check(ObjectIdentityInterface $object, Permission $permission);
}
