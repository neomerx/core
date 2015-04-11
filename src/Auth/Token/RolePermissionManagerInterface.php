<?php namespace Neomerx\Core\Auth\Token;

use \Neomerx\Core\Auth\PermissionManagerInterface;

interface RolePermissionManagerInterface extends PermissionManagerInterface
{
    const USER_AUTH_ROLES = 'authRoles';
}
