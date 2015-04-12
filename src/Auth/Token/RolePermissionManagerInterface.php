<?php namespace Neomerx\Core\Auth\Token;

use \Neomerx\Core\Auth\PermissionManagerInterface;

/**
 * @package Neomerx\Core
 */
interface RolePermissionManagerInterface extends PermissionManagerInterface
{
    /** Roles field name */
    const USER_AUTH_ROLES = 'authRoles';
}
